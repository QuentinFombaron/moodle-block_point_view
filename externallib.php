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
 * Point of View external lib
 *
 *
 * @package    block_point_view
 * @copyright  2018 Quentin Fombaron
 * @author     Quentin Fombaron <quentin.fombaron1@etu.univ-grenoble-alpes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * Class block_point_view_external
 *
 * @package block_point_view
 * @copyright  2018 Quentin Fombaron
 * @author     Quentin Fombaron <quentin.fombaron1@etu.univ-grenoble-alpes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_point_view_external extends external_api
{
    /**
     * All necessary parameters to database update
     *
     * @return external_function_parameters
     */
    public static function update_db_parameters() {
        return new external_function_parameters(
            array(
                'func' => new external_value(PARAM_TEXT, 'function name to call', VALUE_REQUIRED),
                'userid' => new external_value(PARAM_INT, 'id of user', VALUE_REQUIRED),
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
                'cmid' => new external_value(PARAM_INT, 'id of course module', VALUE_REQUIRED),
                'vote' => new external_value(PARAM_INT, 'id of vote', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Update the database after added, removed or removed a vote or all votes of course.
     *
     * @param int $func Function name of database update
     * @param int $userid  User ID
     * @param int $courseid Course ID
     * @param int $cmid Course Module ID
     * @param int $vote Vote ID
     * @return string Log message
     * @throws invalid_parameter_exception
     */

    /**
     * @param $func
     * @param $userid
     * @param $courseid
     * @param $cmid
     * @param $vote
     * @return string
     * @throws invalid_parameter_exception
     */
    public static function update_db($func, $userid, $courseid, $cmid, $vote) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/blocks/point_view/lib.php');

        $table = 'block_point_view';

        $params = self::validate_parameters(self::update_db_parameters(), array(
            'func' => $func,
            'userid' => $userid,
            'courseid' => $courseid,
            'cmid' => $cmid,
            'vote' => $vote
            )
        );

        switch ($params['func']) {

            /* INSERT a new line in block_point_view table */
            case 'insert':
                $dataobject = new stdClass();

                $dataobject->userid = $params['userid'];

                $dataobject->courseid = $params['courseid'];

                $dataobject->cmid = $params['cmid'];

                $dataobject->vote = $params['vote'];

                try {

                    $DB->insert_record($table, $dataobject, false);

                    return 'Add OK';

                } catch (dml_exception $e) {

                    return 'Exception : ' . $e->getMessage() . '\n';

                }
                break;

            /* REMOVE a line in block_point_view table */
            case 'remove':
                $conditions = array(
                    'userid' => $params['userid'],
                    'courseid' => $params['courseid'],
                    'cmid' => $params['cmid'],
                    'vote' => $params['vote']
                );

                try {

                    $DB->delete_records($table, $conditions);

                    return 'Remove OK';

                } catch (dml_exception $e) {

                    return 'Exception : ' . $e->getMessage() . '\n';

                }
                break;

            /* UPDATE a line in block_point_view table */
            case 'update':
                try {

                    /* Get the good record to have the ID (ask by 'update_record' function)*/
                    $target = $DB->get_record(
                        $table,
                        array(
                            'userid' => $params['userid'],
                            'courseid' => $params['courseid'],
                            'cmid' => $params['cmid']
                        )
                    );

                } catch (dml_exception $e) {

                    return 'Exception : ' . $e->getMessage() . '\n';

                }

                /* Update the vote */
                $target->vote = $vote;

                try {

                    /* Overwrite the selected line */
                    $DB->update_record($table, $target);

                    return 'Update OK';

                } catch (dml_exception $e) {

                    return 'Exception : ' . $e->getMessage() . '\n';

                }

                break;

            /* REMOVE all votes of a course */
            case 'reset':
                $conditions = array('courseid' => $params['courseid']);

                try {

                    $DB->delete_records($table, $conditions);

                    return 'Reset OK';

                } catch (dml_exception $e) {

                    return 'Exception : ' . $e->getMessage() . '\n';

                }
                break;

        }

        return '';
    }

    /**
     * Returns a log message
     * @return external_description
     */
    public static function update_db_returns() {
        return new external_value(PARAM_TEXT, 'Log message');
    }
}