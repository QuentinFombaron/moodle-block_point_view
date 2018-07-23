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
 * Point of View block
 * File called in AJAX for database modification
 *
 *
 * @package    block_point_view
 * @copyright  2018 Quentin Fombaron
 * @author     Quentin Fombaron <quentin.fombaron1@etu.univ-grenoble-alpes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require(__DIR__ . '/../../config.php');

try {
    require_login();
} catch (coding_exception $e) {
    echo 'Exception [coding_exception] (blocks/point_view/update_db.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (require_login_exception $e) {
    echo 'Exception [require_login_exception] (blocks/point_view/update_db.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (moodle_exception $e) {
    echo 'Exception [moodle_exception] (blocks/point_view/update_db.php -> require_login()) : ',
    $e->getMessage(), "\n";
}

/* Get all the received parameters */
$table = 'block_point_view';

$userid = $_POST['userid'];

$courseid = $_POST['courseid'];

$cmid = $_POST['cmid'];

$vote = $_POST['vote'];

switch ($_POST['func']) {

    /* INSERT a new line in block_point_view table */
    case 'insert':
        $dataobject = new stdClass();

        $dataobject->userid = $userid;

        $dataobject->courseid = $courseid;

        $dataobject->cmid = $cmid;

        $dataobject->vote = $vote;

        try {

            $DB->insert_record($table, $dataobject, false);

            echo json_encode('Add OK');

        } catch (dml_exception $e) {

            echo json_encode('Exception : ', $e->getMessage(), '\n');

        }
        break;

    /* REMOVE a line in block_point_view table */
    case 'remove':
        $conditions = array('userid' => $userid, 'courseid' => $courseid, 'cmid' => $cmid, 'vote' => $vote);

        try {

            $DB->delete_records($table, $conditions);

            echo json_encode('Remove OK');

        } catch (dml_exception $e) {

            echo json_encode('Exception : ', $e->getMessage(), '\n');

        }
        break;

    /* UPDATE a line in block_point_view table */
    case 'update':
        try {

            /* Get the good record to have the ID (ask by 'update_record' function)*/
            $target = $DB->get_record($table, array('userid' => $userid, 'courseid' => $courseid, 'cmid' => $cmid));

        } catch (dml_exception $e) {

            echo json_encode('Exception : ', $e->getMessage(), '\n');

        }

        /* Update the vote */
        $target->vote = $vote;

        try {

            /* Overwrite the selected line */
            $DB->update_record($table, $target);

            echo json_encode('Update OK');

        } catch (dml_exception $e) {

            echo json_encode('Exception : ', $e->getMessage(), '\n');

        }

        break;

    /* REMOVE all votes of a course */
    case 'reset':
        $conditions = array('courseid' => $courseid);

        try {

            $DB->delete_records($table, $conditions);

            echo json_encode('Reset OK');

        } catch (dml_exception $e) {

            echo json_encode('Exception : ', $e->getMessage(), '\n');

        }
        break;
}