<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * File call to overview the votes in the table
 *
 * @package    block_point_view
 * @copyright  2020 Quentin Fombaron, 2021 Astor Bizard
 * @author     Quentin Fombaron <q.fombaron@outlook.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
global $CFG, $DB, $PAGE, $OUTPUT;
require_once(__DIR__ . '/locallib.php');

require_login();

$id = required_param('instanceid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$contextid = required_param('contextid', PARAM_INT);
$downloadformat = optional_param('downloadformat', '', PARAM_RAW);

$context = context_course::instance($courseid);

require_capability('block/point_view:access_overview', $context);

$course = get_course($courseid);

$PAGE->set_course($course);
$PAGE->set_context($context);

$blockrecord = $DB->get_record('block_instances', array('id' => $id));

block_point_view_check_instance($blockrecord, $context, format_string($course->fullname));

$parameters = array (
        'instanceid' => $id,
        'contextid'  => $contextid,
        'courseid'   => $courseid
);

$PAGE->set_url(new moodle_url("{$CFG->wwwroot}/blocks/point_view/overview.php", $parameters));

$title = get_string('reactionsdetails', 'block_point_view');
$heading = format_string($course->fullname) . ' - ' . get_string('pluginname', 'block_point_view');
$PAGE->set_title($heading . ' - ' . $title);
$PAGE->set_heading($heading);
$PAGE->navbar->add($title);
$PAGE->set_pagelayout('report');

$block = block_instance('point_view', $blockrecord);

$PAGE->requires->js_call_amd('block_point_view/script_menu_point_view', 'init');

$sql = 'SELECT cmid,
    COALESCE(COUNT(cmid), 0) AS total,
    COALESCE(TableTypeOne.TotalTypeOne, 0) AS typeone,
    COALESCE(TableTypeTwo.TotalTypeTwo, 0) AS typetwo,
    COALESCE(TableTypeThree.TotalTypethree, 0) AS typethree
  FROM {block_point_view}
    NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypeOne FROM {block_point_view}
      WHERE vote = 1 GROUP BY cmid) AS TableTypeOne
    NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypeTwo FROM {block_point_view}
      WHERE vote = 2 GROUP BY cmid) AS TableTypeTwo
    NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypethree FROM {block_point_view}
      WHERE vote = 3 GROUP BY cmid) AS TableTypeThree
    WHERE courseid = :courseid
  GROUP BY cmid, TableTypeOne.TotalTypeOne, TableTypeTwo.TotalTypeTwo, TableTypeThree.TotalTypethree;';

$result = $DB->get_records_sql($sql, array('courseid' => $courseid));

$users = $DB->get_records('user', null, '', user_picture::fields());

$sqldata = $DB->get_records('block_point_view', ['courseid' => $courseid], '', 'id,cmid,userid,vote');

$usersdisplay = array();

$tabledata = array();
$tablerowclasses = array();

$isdownloading = ($downloadformat > '');
$downloaddata = array();

$votestypes = array('typeone' => 'easy', 'typetwo' => 'better', 'typethree' => 'hard');
$pixparam = block_point_view_get_current_pix($block, array_values($votestypes));

$cms = get_fast_modinfo($courseid, -1)->cms;
foreach ($cms as $cm) {

    if (isset($result[$cm->id])) {

        $sectionname = get_section_name($course, $cm->sectionnum);
        $modulename = $cm->get_formatted_name();

        if (!$isdownloading) {
            $icon = $OUTPUT->pix_icon('icon', $cm->get_module_type_name(), $cm->modname,
                    array('class' => 'iconlarge activityicon'));
            $modulename = $icon . $modulename;
        }

        $votecells = array();
        foreach ($votestypes as $type => $difficulty) {
            $nvotes = intval($result[$cm->id]->$type);
            if ($isdownloading) {
                $votecells[] = $nvotes;
            } else {
                $text = block_point_view_get_reaction_text($block, $difficulty);
                $votecell = new html_table_cell(
                        html_writer::empty_tag('img', array(
                                'src' => $pixparam[$difficulty],
                                'class' => 'overview_img',
                                'alt' => $text,
                                'title' => $text
                        )) .
                        '<span class="votePercent">' .
                        round(100 * $nvotes / intval($result[$cm->id]->total)) . '%' .
                        '</span>' .
                        '<span class="voteInt" style="display: none;">' . $nvotes . '</span>');
                if ($nvotes === 0) {
                    $votecell->attributes['class'] .= ' novote';
                }
                $votecells[] = $votecell;
            }
        }

        $details = array_fill(0, 7, array());

        foreach ($sqldata as $row) {
            if ($row->cmid == $cm->id) {
                if (!isset($usersdisplay[$row->userid])) {
                    $user = $users[$row->userid];
                    $usersdisplay[$row->userid] = fullname($user);
                    if (!$isdownloading) {
                        $usersdisplay[$row->userid] = $OUTPUT->user_picture($user) . $usersdisplay[$row->userid];
                    }
                }
                $details[$row->vote + 1][] = $usersdisplay[$row->userid];
            }
        }

        $data = array(
                $sectionname,
                $modulename,
                $votecells[0],
                $votecells[1],
                $votecells[2],
                $result[$cm->id]->total
        );

        if ($isdownloading) {
            // Set a slighlty different layout for table download.
            $vote = array ('easy', 'better', 'hard');
            foreach (array_slice($details, 2, 3) as $uservote => $usernames) {
                foreach ($usernames as $username) {
                    $downloaddata[] = array_merge($data, array( $vote[$uservote], $username));
                }
            }
        } else {
            $detailsrow = new html_table_row(array_map(function($usernames) {
                return implode('<br>', $usernames);
            }, $details));
            $detailsrow->style = 'display: none;';

            array_push($tabledata,
                    array_merge($data, array( '<i class="fa fa-fw fa-caret-right" style="display: none;"></i>' )),
                    $detailsrow
                    );

            array_push($tablerowclasses, 'row_module', 'row_module_details');
        }

    }
}

if ($isdownloading) {
    // This is a request to download the table.
    confirm_sesskey();

    $headers = array(
            get_string('section'),
            get_string('module', 'block_point_view'),
            'easy_vote_number',
            'better_vote_number',
            'hard_vote_number',
            get_string('total'),
            'user_vote',
            get_string('user')
    );

    $file = $CFG->dirroot . '/dataformat/' . $downloadformat . '/classes/writer.php';
    if (is_readable($file)) {
        include_once($file);
    }
    $writerclass = 'dataformat_' . $downloadformat. '\writer';
    if (!class_exists($writerclass)) {
        throw new moodle_exception('invalidparameter', 'debug');
    }

    $writer = new $writerclass();

    $writer->set_filename(clean_filename('block_point_view_export_' . $course->shortname));
    $writer->send_http_headers();
    $writer->set_sheettitle($course->shortname);
    $writer->start_output();

    $writer->start_sheet($headers);

    foreach ($downloaddata as $rownum => $row) {
        $writer->write_record($row, $rownum + 1);
    }

    $writer->close_sheet($headers);

    $writer->close_output();
    exit();
}

echo $OUTPUT->header();
echo $OUTPUT->heading($title);
echo $OUTPUT->container_start('block_point_view');

if (!empty($result)) {

    $table = new html_table();

    $table->head = array(
        get_string('section'),
        get_string('module', 'block_point_view'),
        '',
        get_string('reactions', 'block_point_view'),
        '',
        get_string('total'),
        ''
    );

    $table->size = array(
            '5%',
            '25%',
            '20%',
            '20%',
            '20%',
            '5%',
            '5%'
    );

    $table->attributes['class'] = 'generaltable';

    $table->rowclasses = $tablerowclasses;

    $table->colclasses = array(
            '',
            '',
            'reactions-col',
            'reactions-col',
            'reactions-col',
            '',
            ''
    );

    $table->data = $tabledata;

    echo html_writer::table($table);

    echo $OUTPUT->download_dataformat_selector(
            get_string('downloadas', 'table'),
            $PAGE->url->out_omit_querystring(),
            'downloadformat',
            $PAGE->url->params()
    );
} else {

    echo html_writer::tag('h4', get_string('nothingtodisplay'));

}

echo $OUTPUT->container_end();
echo $OUTPUT->footer();
