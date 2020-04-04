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
 * File called to download an exporting file
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

        $format = required_param('format', PARAM_TEXT);

        $contextid = required_param('contextid', PARAM_INT);

        $instanceid = required_param('instanceid', PARAM_INT);

        $context = CONTEXT_COURSE::instance($courseid);

        require_login();

        $PAGE->set_context($context);

        if ($format != null) {
            try {
                $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

                $activities = (block_point_view_get_course_data($courseid))['activities'];

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

                $params = array('courseid' => $courseid);

                $result = $DB->get_records_sql($sql, $params);

                $pointview = $DB->get_records('block_point_view', ['courseid' => $courseid], '', 'id,cmid,userid,vote');

                $users = $DB->get_records('user', null, '', user_picture::fields());

                $courses = get_courses();

                $vote = array (
                    1 => "easy",
                    2 => "better",
                    3 => "hard"
                );

                $data = array();

                if ($courseid == 1) {

                    foreach ($courses as $activity) {

                        $sectioncourse = $DB->get_record('course_categories', array('id' => $activity->category, ), 'name');

                        if (isset($result[($activity->id)]->cmid)) {

                            foreach ($pointview as $row) {

                                if ($row->cmid == $activity->id) {

                                    array_push($data, array(
                                        $sectioncourse->name,
                                        format_string($activity->fullname),
                                        $result[($activity->id)]->typeone,
                                        $result[($activity->id)]->typetwo,
                                        $result[($activity->id)]->typethree,
                                        $result[($activity->id)]->total,
                                        $vote[$row->vote],
                                        $users[($row->userid)]->firstname . ' ' . $users[($row->userid)]->lastname
                                    )
                                        );
                                }
                            }
                        }
                    }
                } else {
                    foreach ($activities as $index => $activity) {

                        if (isset($result[($activity['id'])]->cmid)) {

                            foreach ($pointview as $row) {

                                if ($row->cmid == $activity['id']) {

                                    array_push($data, array(
                                        get_section_name($course, $activity['section']),
                                        format_string($activity['name']),
                                        $result[($activity['id'])]->typeone,
                                        $result[($activity['id'])]->typetwo,
                                        $result[($activity['id'])]->typethree,
                                        $result[($activity['id'])]->total,
                                        $vote[$row->vote],
                                        $users[($row->userid)]->firstname . ' ' . $users[($row->userid)]->lastname
                                    )
                                        );
                                }
                            }
                        }
                    }
                }
            } catch (dml_exception $e) {

                echo 'Exception [dml_exception] (blocks/point_view/download.php -> require_login()) : ',
                $e->getMessage(), "\n";

            } catch (moodle_exception $e) {

                echo 'Exception [moodle_exception] (blocks/point_view/download.php -> require_login()) : ',
                $e->getMessage(), "\n";

            }

            $headers = array(
                'section_name',
                'module_name',
                'easy_vote_number',
                'better_vote_number',
                'hard_vote_number',
                'total_vote_number',
                'user_vote',
                'user'
            );

            switch ($format) {
                case 'csv' :

                    require_once($CFG->libdir . '/csvlib.class.php');

                    $writer = new csv_export_writer();

                    $filename = clean_filename("point_views_export");

                    $writer->set_filename($filename);

                    $writer->add_data($headers);

                    foreach ($data as $row) {

                        $writer->add_data($row);

                    }

                    $writer->download_file();

                    break;

                case 'ods':
                case 'xls' :

                    $downloadfilename = clean_filename("point_views_export." . $format);

                    if ($format == "ods") {

                        require_once($CFG->libdir . '/odslib.class.php');

                        $workbook = new MoodleODSWorkbook("-");

                    }

                    if ($format == "xls") {

                        require_once($CFG->libdir . '/excellib.class.php');

                        $workbook = new MoodleExcelWorkbook("-");

                    }

                    $workbook->send($downloadfilename);

                    $myxls = $workbook->add_worksheet("data_point_views");

                    $line = 0;

                    $colonne = 0;

                    foreach ($headers as $h) {

                        $myxls->write_string($line, $colonne, $h);

                        $colonne++;

                    }

                    $line = 1;

                    foreach ($data as $d) {

                        $colonne = 0;

                        foreach ($d as $row) {

                            $myxls->write_string($line, $colonne, $row);

                            $colonne++;

                        }

                        $line = $line + 1;

                    }

                    $workbook->close();

                    break;
            }
        }
    }

} catch (coding_exception $e) {
    echo 'Exception [coding_exception] (blocks/point_view/download.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (require_login_exception $e) {
    echo 'Exception [require_login_exception] (blocks/point_view/download.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (moodle_exception $e) {
    echo 'Exception [moodle_exception] (blocks/point_view/download.php -> require_login()) : ',
    $e->getMessage(), "\n";
}