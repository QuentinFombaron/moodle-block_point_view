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
 *
 * @package    block_point_view
 * @copyright  2020 Quentin Fombaron
 * @author     Quentin Fombaron <q.fombaron@outlook.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
global $CFG, $DB, $PAGE, $COURSE, $OUTPUT;
require_once($CFG->dirroot . '/blocks/point_view/lib.php');
require_once(__DIR__ . '/locallib.php');

$courseid = required_param('courseid', PARAM_INT);

$context = CONTEXT_COURSE::instance($courseid);

if (has_capability('block/point_view:access_menu', $context)) {

    $id = required_param('instanceid', PARAM_INT);

    $contextid = required_param('contextid', PARAM_INT);

    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

    $blockcontext = CONTEXT_BLOCK::instance($id);

    require_login();

    confirm_sesskey();

    require_capability('block/point_view:view', $blockcontext);

    $PAGE->set_course($course);
    $PAGE->set_context($context);

    $blockrecord = $DB->get_record('block_instances', array('id' => $id));

    block_point_view_check_instance($blockrecord, $context, format_string($course->fullname));

    block_point_view_print_header_with_tabs('overview', $id, $contextid, $courseid);

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

    $params = array('courseid' => $COURSE->id);

    $result = $DB->get_records_sql($sql, $params);

    if (!empty($result)) {

        $sqldata = $DB->get_records('block_point_view', ['courseid' => $COURSE->id], '', 'id,cmid,userid,vote');

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

        $table->rowclasses = array();

        $table->colclasses = array(
                '',
                '',
                'reactions-col',
                'reactions-col',
                'reactions-col',
                '',
                ''
        );

        $table->data = array();

        $users = $DB->get_records('user', null, '', user_picture::fields());

        $pixparam = block_point_view_get_current_pix($block, $contextid, array('easy', 'better', 'hard'));


        $activities = (block_point_view_get_course_data($courseid)['activities']);
        foreach ($activities as $index => $activity) {

            if (isset($result[($activity['id'])]->cmid)) {

                $details = array(
                    'easy' => array(),
                    'better' => array(),
                    'hard' => array()
                );

                foreach ($sqldata as $row) {

                    if ($row->cmid == $activity['id']) {
                        array_push($details[array_keys($details)[$row->vote - 1]], intval($row->userid));
                    }
                }

                array_push($table->rowclasses,
                    'row_module' /*. $activity['id']*/,
                    'row_module' /*. $activity['id']*/ . '_details'
                    );

                $attributes = ['class' => 'iconlarge activityicon'];

                $icon = $OUTPUT->pix_icon('icon', $activity['modulename'], $activity['type'], $attributes);

                $votestypes = array('typeone' => 'easy', 'typetwo' => 'better', 'typethree' => 'hard');
                $votecells = array();
                foreach ($votestypes as $type => $difficulty) {
                    $votes = intval($result[($activity['id'])]->$type);
                    $votecell = new html_table_cell(
                            html_writer::empty_tag(
                                    'img',
                                    array('src' => $pixparam[$difficulty], 'class' => 'overview_img')) .
                            '<span class="votePercent">' .
                            round(100 * $votes / intval($result[($activity['id'])]->total)) . '%' .
                            '</span>' .
                            '<span class="voteInt" style="display: none;">' . $votes . '</span>');
                    if ($votes === 0) {
                        $votecell->attributes['class'] .= ' novote';
                    }
                    $votecells[] = $votecell;
                }

                $detailsrow = new html_table_row(array(
                        '',
                        '',
                        tostring($OUTPUT, $details['easy'], $users, $course),
                        tostring($OUTPUT, $details['better'], $users, $course),
                        tostring($OUTPUT, $details['hard'], $users, $course),
                        '',
                        ''
                ));
                $detailsrow->style = 'display: none;';

                array_push($table->data,
                    array(
                        get_section_name($COURSE, $activity['section']),
                        $icon . format_string($activity['name']),
                        $votecells[0],
                        $votecells[1],
                        $votecells[2],
                        $result[($activity['id'])]->total,
                        '<i class="fa fa-fw fa-caret-right" style="display: none;"></i>'
                    ),
                    $detailsrow
                    );
            }
        }
        echo html_writer::table($table);

    } else {

        echo html_writer::tag('p', get_string('noactivity', 'block_point_view'));

    }

    block_point_view_print_footer_of_tabs();
}