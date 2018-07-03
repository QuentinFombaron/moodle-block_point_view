<?php

define('AJAX_SCRIPT', true);

require(__DIR__ . '/../../config.php');

try {
    require_login();
} catch (coding_exception $e) {
    echo json_encode('Exception coding_exception (require_login() -> blocks/like/update_db.php) : ', $e->getMessage(), "\n");
} catch (require_login_exception $e) {
    echo json_encode('Exception require_login_exception (require_login() -> blocks/like/update_db.php) : ', $e->getMessage(), "\n");
} catch (moodle_exception $e) {
    echo json_encode('Exception moodle_exception (require_login() -> blocks/like/update_db.php) : ', $e->getMessage(), "\n");
}

/* Get all the received parameters */
$table = 'block_like';

$userid = $_POST['userid'];
$courseid = $_POST['courseid'];
$cmid = $_POST['cmid'];
$vote = $_POST['vote'];

switch ($_POST['func']) {
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

    case 'remove':
        $conditions = array('userid' => $userid, 'courseid' => $courseid, 'cmid' => $cmid, 'vote' => $vote);

        try {
            $DB->delete_records($table, $conditions);
            echo json_encode('Remove OK');
        } catch (dml_exception $e) {
            echo json_encode('Exception : ', $e->getMessage(), '\n');
        }
        break;

    case 'update':
        try {
            /* Get the good record to have the ID (ask by 'update_record' function)*/
            $target = $DB->get_record('block_like', array('userid' => $userid, 'courseid' => $courseid, 'cmid' => $cmid));
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
}