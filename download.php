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
 * Like block export page
 *
 * @package    block_like
 * @copyright  [TODO]
 * @license    [TODO]
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/like/lib.php');

global $COURSE;

try {
    require_login();
} catch (coding_exception $e) {
    echo 'Exception coding_exception (require_login() -> blocks/like/menu.php) : ', $e->getMessage(), "\n";
} catch (require_login_exception $e) {
    echo 'Exception require_login_exception (require_login() -> blocks/like/menu.php) : ', $e->getMessage(), "\n";
} catch (moodle_exception $e) {
    echo 'Exception moodle_exception (require_login() -> blocks/like/menu.php) : ', $e->getMessage(), "\n";
}

$format = $_POST['format'];
$courseid = $_POST['courseid'];
$contextid = $_POST['contextid'];
$instanceid = $_POST['instanceid'];

if ($format != null) {
    try {
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        $config = new stdClass();
        $config->moduletype = array(
            'book',
            'chat',
            'file',
            'forum',
            'glossary',
            'page',
            'quiz',
            'resource',
            'url',
            'vpl',
            'wiki'
        );
        $activities = block_like_get_activities($courseid, $config);

        $sql = 'SELECT Base.cmid,
            IFNULL(COUNT(Base.cmid), 0) AS total,
            IFNULL(TableTypeOne.TotalTypeOne, 0) AS typeone,
            IFNULL(TableTypeTwo.TotalTypeTwo, 0) AS typetwo,
            IFNULL(TableTypeThree.TotalTypethree, 0) AS typethree
          FROM {block_like} AS Base
            NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypeOne FROM {block_like}
              WHERE vote = 1 GROUP BY cmid) AS TableTypeOne
            NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypeTwo FROM {block_like}
              WHERE vote = 2 GROUP BY cmid) AS TableTypeTwo
            NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypethree FROM {block_like}
              WHERE vote = 3 GROUP BY cmid) AS TableTypeThree
            WHERE courseid = :courseid
          GROUP BY cmid;';

        $params = array('courseid' => $courseid);

        $result = $DB->get_records_sql($sql, $params);

        $like = $DB->get_records('block_like', ['courseid' => $courseid], '', 'id,cmid,userid,vote');

        $users = $DB->get_records('user', null, '', 'id,firstname,lastname');

        $data = array();

        foreach ($activities as $index => $activity) {
            if ($activity['type'] != 'label' && !is_null($result[($activity['id'])]->cmid)) {
                foreach ($like as $row) {
                    if ($row->cmid == $activity['id']) {
                        array_push($data, array(
                                get_section_name($course, $activity['section']),
                                format_string($activity['name']),
                                $result[($activity['id'])]->typeone,
                                $result[($activity['id'])]->typetwo,
                                $result[($activity['id'])]->typethree,
                                $result[($activity['id'])]->total,
                                $users[($row->userid)]->firstname . ' ' . $users[($row->userid)]->lastname
                            )
                        );
                    }
                }
            }
        }
    } catch (dml_exception $e) {
        echo 'Exception : ', $e->getMessage(), "\n";
    } catch (moodle_exception $e) {
        echo 'Exception : ', $e->getMessage(), "\n";
    }

    $headers = array(
        'section_name',
        'module_name',
        'easy_vote_number',
        'better_vote_number',
        'hard_vote_number',
        'total_vote_number',
        'user'
    );

    switch ($format) {
        case 'csv' :
            require_once($CFG->libdir . '/csvlib.class.php');

            $writer = new csv_export_writer();
            $filename = clean_filename("likes_export");
            $writer->set_filename($filename);

            $writer->add_data($headers);

            foreach ($data as $row) {
                $writer->add_data($row);
            }

            $writer->download_file();
            break;
        case 'ods':
        case 'xls' :
            $downloadfilename = clean_filename("likes_export." . $format);

            if ($format == "ods") {
                require_once($CFG->libdir . '/odslib.class.php');
                $workbook = new MoodleODSWorkbook("-");
            }

            if ($format == "xls") {
                require_once($CFG->libdir . '/excellib.class.php');
                $workbook = new MoodleExcelWorkbook("-");
            }

            $workbook->send($downloadfilename);
            $myxls = $workbook->add_worksheet("data_likes");

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