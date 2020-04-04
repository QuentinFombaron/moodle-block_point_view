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
 * @copyright  2018 Quentin Fombaron
 * @author     Quentin Fombaron <quentin.fombaron1@etu.univ-grenoble-alpes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/point_view/lib.php');

try {
    $courseid = required_param('courseid', PARAM_INT);

    $context = CONTEXT_COURSE::instance($courseid);

    if (has_capability('block/point_view:access_menu', $context)) {

        $id = required_param('instanceid', PARAM_INT);

        $contextid = required_param('contextid', PARAM_INT);

        $enablepix = required_param('enablepix', PARAM_INT);

        $tab = optional_param('tab', 'overview', PARAM_ALPHA);

        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);


        $block = $DB->get_record('block_instances', array('id' => $id), '*', MUST_EXIST);

        $blockcontext = CONTEXT_BLOCK::instance($id);

        require_login();

        confirm_sesskey();

        require_capability('block/point_view:view', $blockcontext);

        $PAGE->set_course($course);

        $PAGE->set_url(
            '/blocks/point_view/menu.php',
            array(
                'instanceid' => $id,
                'contextid' => $contextid,
                'courseid' => $courseid,
                'enablepix' => $enablepix,
                'sesskey' => sesskey()
            )
        );

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

        $PAGE->set_context($context);

        $PAGE->requires->css(new moodle_url($CFG->wwwroot . '/blocks/point_view/styles.css'));

        $envconf = array(
            'userid' => $USER->id,
            'courseid' => $COURSE->id,
        );

        $paramsamd = array($envconf);

        $PAGE->requires->js_call_amd('block_point_view/script_menu_point_view', 'init', $paramsamd);

        $title = get_string('menu', 'block_point_view');

        $PAGE->set_title($title);

        $PAGE->set_heading(get_string('pluginname', 'block_point_view'));

        $PAGE->navbar->add($title);

        $PAGE->set_pagelayout('report');

        echo $OUTPUT->header();

        echo $OUTPUT->heading($title, 2);

        echo $OUTPUT->container_start('block_point_view');

        require("tabs.php");

        if (!empty($result)) {

            $sqldata = $DB->get_records('block_point_view', ['courseid' => $COURSE->id], '', 'id,cmid,userid,vote');

            $table = new html_table();

            $table->head = array(
                get_string('colsection', 'block_point_view'),
                get_string('colmodule', 'block_point_view'),
                '',
                get_string('colreactions', 'block_point_view'),
                '',
                'Total',
                ''
            );

            $table->attributes['class'] = 'generaltable';

            $table->rowclasses = array();

            $table->data = array();

            $users = $DB->get_records('user', null, '', user_picture::fields());

            $pixparam = array(
                'easy' => $CFG->wwwroot . '/blocks/point_view/pix/easy.png',
                'better' => $CFG->wwwroot . '/blocks/point_view/pix/better.png',
                'hard' => $CFG->wwwroot . '/blocks/point_view/pix/hard.png',
            );

            $fs = get_file_storage();

            if (get_config('block_point_view', 'enable_pix_admin')) {

                foreach ($pixparam as $file => $data) {

                    if ($fs->file_exists(1, 'block_point_view', 'point_views_pix_admin', 0, '/', $file . '.png')) {

                        $pixparam[$file] = block_point_view_pix_url(1, 'point_views_pix_admin', $file);

                    }
                }
            } else if ($enablepix) {

                foreach ($pixparam as $file => $data) {

                    if ($fs->file_exists($contextid, 'block_point_view', 'point_views_pix', 0, '/', $file . '.png')) {

                        $pixparam[$file] = block_point_view_pix_url($contextid, 'point_views_pix', $file);

                    }
                }
            }
            if ($COURSE->id == 1) { /* Consulting courses votes */
                $courses = get_courses();
                foreach ($courses as $activity) {

                    if (isset($result[($activity->id)]->cmid)) {

                        $details = array(
                            'easy' => array(),
                            'better' => array(),
                            'hard' => array()
                        );

                        foreach ($sqldata as $row) {

                            if ($row->cmid == $activity->id) {

                                switch ($row->vote) {
                                    case 1 :
                                        array_push($details['easy'], intval($row->userid));
                                        break;
                                    case 2 :
                                        array_push($details['better'], intval($row->userid));
                                        break;
                                    case 3 :
                                        array_push($details['hard'], intval($row->userid));
                                        break;
                                }
                            }
                        }

                        array_push($table->rowclasses,
                            'row_module' . $activity->id,
                            'row_module' . $activity->id . '_details'
                            );

                        $attributes = ['class' => 'iconlarge activityicon'];

                        $icon = $OUTPUT->pix_icon('i/course', 'course', 'core', $attributes);

                        $sectioncourse = $DB->get_record('course_categories', array('id' => $activity->category, ),  'name');

                        array_push($table->data,
                            array(
                                $sectioncourse->name,
                                $icon . format_string($activity->fullname),
                                html_writer::empty_tag(
                                    'img',
                                    array('src' => $pixparam['easy'], 'class' => 'overview_img')) .
                                ' <span class="votePercent">' .
                                round(100 * intval($result[($activity->id)]->typeone)
                                    / intval($result[($activity->id)]->total)) . '%</span><span class="voteInt">' .
                                $result[($activity->id)]->typeone . '</span>',
                                html_writer::empty_tag(
                                    'img',
                                    array('src' => $pixparam['better'], 'class' => 'overview_img')) .
                                ' <span class="votePercent">' .
                                round(100 * intval($result[($activity->id)]->typetwo)
                                    / intval($result[($activity->id)]->total)) . '%</span><span class="voteInt">' .
                                $result[($activity->id)]->typetwo . '</span>',
                                html_writer::empty_tag(
                                    'img',
                                    array('src' => $pixparam['hard'], 'class' => 'overview_img')) .
                                ' <span class="votePercent">' .
                                round(100 * intval($result[($activity->id)]->typethree)
                                    / intval($result[($activity->id)]->total)) . '% </span><span class="voteInt">' .
                                $result[($activity->id)]->typethree . '</span>',
                                $result[($activity->id)]->total,
                                '+'
                            ),
                            array(
                                '',
                                '',
                                tostring($OUTPUT, $details['easy'], $users, $course),
                                tostring($OUTPUT, $details['better'], $users, $course),
                                tostring($OUTPUT, $details['hard'], $users, $course),
                                ''
                            )
                            );
                    }
                }
            } else { /* Consulting modules votes in a course */
                $activities = (block_point_view_get_course_data($courseid))['activities'];
                foreach ($activities as $index => $activity) {

                    if (isset($result[($activity['id'])]->cmid)) {

                        $details = array(
                            'easy' => array(),
                            'better' => array(),
                            'hard' => array()
                        );

                        foreach ($sqldata as $row) {

                            if ($row->cmid == $activity['id']) {

                                switch ($row->vote) {
                                    case 1 :
                                        array_push($details['easy'], intval($row->userid));
                                        break;
                                    case 2 :
                                        array_push($details['better'], intval($row->userid));
                                        break;
                                    case 3 :
                                        array_push($details['hard'], intval($row->userid));
                                        break;
                                }
                            }
                        }

                        array_push($table->rowclasses,
                            'row_module' . $activity['id'],
                            'row_module' . $activity['id'] . '_details'
                            );

                        $attributes = ['class' => 'iconlarge activityicon'];

                        $icon = $OUTPUT->pix_icon('icon', $activity['modulename'], $activity['type'], $attributes);

                        array_push($table->data,
                            array(
                                get_section_name($COURSE, $activity['section']),
                                $icon . format_string($activity['name']),
                                html_writer::empty_tag(
                                    'img',
                                    array('src' => $pixparam['easy'], 'class' => 'overview_img')) .
                                ' <span class="votePercent">' .
                                round(100 * intval($result[($activity['id'])]->typeone)
                                    / intval($result[($activity['id'])]->total)) . '%</span><span class="voteInt">' .
                                $result[($activity['id'])]->typeone . '</span>',
                                html_writer::empty_tag(
                                    'img',
                                    array('src' => $pixparam['better'], 'class' => 'overview_img')) .
                                ' <span class="votePercent">' .
                                round(100 * intval($result[($activity['id'])]->typetwo)
                                    / intval($result[($activity['id'])]->total)) . '%</span><span class="voteInt">' .
                                $result[($activity['id'])]->typetwo . '</span>',
                                html_writer::empty_tag(
                                    'img',
                                    array('src' => $pixparam['hard'], 'class' => 'overview_img')) .
                                ' <span class="votePercent">' .
                                round(100 * intval($result[($activity['id'])]->typethree)
                                    / intval($result[($activity['id'])]->total)) . '% </span><span class="voteInt">' .
                                $result[($activity['id'])]->typethree . '</span>',
                                $result[($activity['id'])]->total,
                                '+'
                            ),
                            array(
                                '',
                                '',
                                tostring($OUTPUT, $details['easy'], $users, $course),
                                tostring($OUTPUT, $details['better'], $users, $course),
                                tostring($OUTPUT, $details['hard'], $users, $course),
                                ''
                            )
                            );
                    }
                }
            }
            echo html_writer::table($table);

        } else {

            echo html_writer::tag('p', get_string('noneactivity', 'block_point_view'));

        }

        echo $OUTPUT->container_end();

        echo $OUTPUT->footer();

    }

} catch (coding_exception $e) {

    echo 'Exception [coding_exception] (blocks/point_view/menu.php) : ',
    $e->getMessage(), "\n";

} catch (dml_exception $e) {

    echo 'Exception [dml_exception] (blocks/point_view/menu.php) : ',
    $e->getMessage(), "\n";

} catch (moodle_exception $e) {

    echo 'Exception [moodle_exception] (blocks/point_view/menu.php) : ',
    $e->getMessage(), "\n";

}

