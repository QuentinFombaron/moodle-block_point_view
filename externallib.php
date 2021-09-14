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
 * @copyright  2020 Quentin Fombaron
 * @author     Quentin Fombaron <q.fombaron@outlook.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/blocks/point_view/lib.php');

/**
 * Class block_point_view_external
 *
 * @package block_point_view
 * @copyright  2020 Quentin Fombaron
 * @author     Quentin Fombaron <q.fombaron@outlook.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_point_view_external extends external_api {
    /**
     * All necessary parameters
     *
     * @return external_function_parameters
     */
    public static function update_db_parameters() {
        return new external_function_parameters(
            array(
                'func' => new external_value(PARAM_TEXT, 'function name to call', VALUE_REQUIRED),
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
                'cmid' => new external_value(PARAM_INT, 'id of course module', VALUE_DEFAULT, 0),
                'vote' => new external_value(PARAM_INT, 'id of vote', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Update the database after added, removed or removed a vote or all votes of course.
     *
     * @param string $func Function name of database update
     * @param int $courseid Course ID
     * @param int $cmid Course Module ID
     * @param int $vote Vote ID
     * @return string Log message
     * @throws invalid_parameter_exception
     * @throws dml_exception
     */
    public static function update_db(string $func, int $courseid, int $cmid, int $vote) {
        global $DB, $USER;

        $params = self::validate_parameters(self::update_db_parameters(), array(
                'func' => $func,
                'courseid' => $courseid,
                'cmid' => $cmid,
                'vote' => $vote
            )
        );

        $table = 'block_point_view';

        $coursecontext = context_course::instance($courseid);

        switch ($params['func']) {
            case 'update':

                $blockrecord = $DB->get_record('block_instances', array('blockname' => 'point_view',
                        'parentcontextid' => $coursecontext->id), '*', MUST_EXIST);
                $blockinstance = block_instance('point_view', $blockrecord);

                $canreact = isset($blockinstance->config->enable_point_views_checkbox)
                        && $blockinstance->config->enable_point_views_checkbox
                        && isset($blockinstance->config->{'moduleselectm' . $params['cmid']})
                        && $blockinstance->config->{'moduleselectm' . $params['cmid']}
                        && get_fast_modinfo($params['courseid'], $USER->id)->cms[$params['cmid']]->uservisible;

                if (!$canreact){
                    throw new moodle_exception('reactionsunavailable', 'block_point_view');
                }

                $dbparams = array(
                        'userid' => $USER->id,
                        'courseid' => $params['courseid'],
                        'cmid' => $params['cmid']
                );

                if ($params['vote'] === 0) {
                    $DB->delete_records($table, $dbparams);
                } else {
                    $currentvote = $DB->get_record($table, $dbparams);
                    if ($currentvote === false) {
                        $dbparams['vote'] = $params['vote'];
                        $DB->insert_record($table, $dbparams);
                    } else {
                        $currentvote->vote = $params['vote'];
                        $DB->update_record($table, $currentvote);
                    }
                }

                break;
            case 'reset':
                // Reset all reactions of course.
                require_capability('moodle/site:manageblocks', $coursecontext);
                $DB->delete_records($table, array('courseid' => $params['courseid']));
                break;
            default:
                break;
        }

        return '';
    }

    /**
     * Returns a log message
     *
     * @return external_description
     */
    public static function update_db_returns() {
        return new external_value(PARAM_TEXT, 'Log message');
    }

    /* --------------------------------------------------------------------------------------------------------- */

    /**
     * All necessary parameters
     *
     * @return external_function_parameters
     */
    public static function get_database_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'id of user', VALUE_REQUIRED),
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Get course reactions from database
     *
     * @param int $userid User ID
     * @param int $courseid Course ID
     * @return array Course reactions
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function get_database($userid, $courseid) {
        global $DB;

        self::validate_parameters(self::get_database_parameters(), array(
                'userid' => $userid,
                'courseid' => $courseid,
            )
        );

        $pointviews = [];

        $coursecontext = context_course::instance($courseid);
        $blockrecord = $DB->get_record('block_instances', array('blockname' => 'point_view',
            'parentcontextid' => $coursecontext->id), '*', MUST_EXIST);
        $blockinstance = block_instance('point_view', $blockrecord);

        $enablepointviewscheckbox = (isset($blockinstance->config->enable_point_views_checkbox)) ?
            $blockinstance->config->enable_point_views_checkbox :
            0;

        if ($enablepointviewscheckbox) {

            $sql = 'SELECT cmid,
            COALESCE(COUNT(cmid), 0) AS total,
            COALESCE(TableTypeOne.TotalTypeOne, 0) AS typeone,
            COALESCE(TableTypeTwo.TotalTypeTwo, 0) AS typetwo,
            COALESCE(TableTypeThree.TotalTypethree, 0) AS typethree,
            COALESCE(TableUser.UserVote, 0) AS uservote
            FROM {block_point_view}
              NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypeOne FROM {block_point_view}
                WHERE vote = 1 GROUP BY cmid) AS TableTypeOne
              NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypeTwo FROM {block_point_view}
                WHERE vote = 2 GROUP BY cmid) AS TableTypeTwo
              NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypethree FROM {block_point_view}
                WHERE vote = 3 GROUP BY cmid) AS TableTypeThree
              NATURAL LEFT JOIN (SELECT cmid, vote AS UserVote FROM {block_point_view} WHERE userid = :userid) AS TableUser
                WHERE courseid = :courseid
            GROUP BY cmid, TableTypeOne.TotalTypeOne, TableTypeTwo.TotalTypeTwo,
            TableTypeThree.TotalTypethree, TableUser.UserVote;';

            $params = array('userid' => $userid, 'courseid' => $courseid);

            $result = $DB->get_records_sql($sql, $params);

            /* Parameters for the Javascript */
            $pointviews = $result;
        }

        return $pointviews;
    }

    /**
     * Return course reactions array
     *
     * @return external_description
     */
    public static function get_database_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'cmid' => new external_value(PARAM_INT, 'CMID'),
                    'total' => new external_value(PARAM_INT, 'Total'),
                    'typeone' => new external_value(PARAM_INT, 'Type one vote'),
                    'typetwo' => new external_value(PARAM_INT, 'Type two vote'),
                    'typethree' => new external_value(PARAM_INT, 'Type three vote'),
                    'uservote' => new external_value(PARAM_INT, 'User vote')
                )
            )
        );
    }

    /* --------------------------------------------------------------------------------------------------------- */

    /**
     * All necessary parameters
     *
     * @return external_function_parameters
     */
    public static function get_pixparam_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
                'contextid' => new external_value(PARAM_INT, 'id of context', VALUE_REQUIRED)
            )
        );
    }

    /**
     * Get pictures images location
     *
     * @param int $courseid Course ID
     * @param int $contextid Context ID
     * @return array Pictures parameters
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function get_pixparam($courseid, $contextid) {
        $params = self::validate_parameters(self::get_pixparam_parameters(), array(
                'courseid' => $courseid,
                'contextid' => $contextid
            )
        );

        require_once(__DIR__ . '/locallib.php');
        return block_point_view_get_pix($params['courseid'], $params['contextid']);
    }

    /**
     * Return pictures parameters array
     *
     * @return external_description
     */
    public static function get_pixparam_returns() {
        return new external_single_structure(
            array(
                'easy' => new external_value(PARAM_TEXT, 'Easy image'),
                'easytxt' => new external_value(PARAM_TEXT, 'Easy description'),
                'better' => new external_value(PARAM_TEXT, 'Better'),
                'bettertxt' => new external_value(PARAM_TEXT, 'Better description'),
                'hard' => new external_value(PARAM_TEXT, 'Hard'),
                'hardtxt' => new external_value(PARAM_TEXT, 'Hard description'),
                'group_' => new external_value(PARAM_TEXT, 'No vote group'),
                'group_E' => new external_value(PARAM_TEXT, 'Easy group'),
                'group_B' => new external_value(PARAM_TEXT, 'Better group'),
                'group_H' => new external_value(PARAM_TEXT, 'Hard group'),
                'group_EB' => new external_value(PARAM_TEXT, 'Easy + Better group'),
                'group_EH' => new external_value(PARAM_TEXT, 'Easy + Hard group'),
                'group_BH' => new external_value(PARAM_TEXT, 'Better + Hard group'),
                'group_EBH' => new external_value(PARAM_TEXT, 'Easy + Better + Hard group')
            )
        );
    }

    /* --------------------------------------------------------------------------------------------------------- */

    /**
     * All necessary parameters
     *
     * @return external_function_parameters
     */
    public static function get_moduleselect_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Get modules of a course or course in home page
     *
     * @param int $courseid course ID
     * @return array Seleted modules
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function get_moduleselect($courseid) {
        global $DB;

        $moduleselect = array();

        self::validate_parameters(self::get_moduleselect_parameters(), array(
            'courseid' => $courseid
        ));

        $coursecontext = context_course::instance($courseid);
        $blockrecord = $DB->get_record('block_instances', array('blockname' => 'point_view',
            'parentcontextid' => $coursecontext->id), '*', MUST_EXIST);
        $blockinstance = block_instance('point_view', $blockrecord);

        $sqlid = $DB->get_records('course_modules', array('course' => $courseid), null, 'id');

        foreach ($sqlid as $row) {

            if (isset($blockinstance->config->{'moduleselectm' . $row->id})) {

                if ($blockinstance->config->{'moduleselectm' . $row->id} != 0
                && $blockinstance->config->enable_point_views_checkbox) {
                    array_push($moduleselect, $row->id);

                }
            }
        }

        return $moduleselect;
    }

    /**
     * Return seleted modules array
     *
     * @return external_description
     */
    public static function get_moduleselect_returns() {
        return new external_multiple_structure(
             new external_value(PARAM_INT, 'Course module ID')
        );
    }

    /* --------------------------------------------------------------------------------------------------------- */

    /**
     * All necessary parameters
     *
     * @return external_function_parameters
     */
    public static function get_difficulty_levels_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Get difficulty tracks
     *
     * @param int $courseid Course ID
     * @return array Difficulty levels
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function get_difficulty_levels($courseid) {
        $params = self::validate_parameters(self::get_difficulty_levels_parameters(), array(
                'courseid' => $courseid
        ));
        $courseid = $params['courseid'];

        require_once(__DIR__ . '/locallib.php');
        return block_point_view_get_difficulty_levels($courseid);
    }

    /**
     * Return difficulty levels array
     *
     * @return external_description
     */
    public static function get_difficulty_levels_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'Course module ID'),
                    'difficultyLevel' => new external_value(PARAM_INT, 'Difficulty track'),
                )
            )
        );
    }

    /* --------------------------------------------------------------------------------------------------------- */

    public static function delete_custom_pix_parameters() {
        return new external_function_parameters(
                array(
                        'contextid' => new external_value(PARAM_INT, 'id of context', VALUE_REQUIRED),
                        'draftitemid' => new external_value(PARAM_INT, 'id of draft file area', VALUE_REQUIRED)
                )
        );
    }

    public static function delete_custom_pix($contextid, $draftitemid) {
        global $USER;
        // TODO check capability
        $fs = get_file_storage();
        $success = $fs->delete_area_files($contextid, 'block_point_view', 'point_views_pix');
        $success = $success && $fs->delete_area_files(context_user::instance($USER->id)->id, 'user', 'draft', $draftitemid);
        return array('success' => $success);
    }

    /**
     * Return track colors array
     *
     * @return external_description
     */
    public static function delete_custom_pix_returns() {
        return new external_single_structure(
                array(
                        'success' => new external_value(PARAM_BOOL, 'Whether operation was successful', VALUE_REQUIRED),
                )
        );
    }

    /* --------------------------------------------------------------------------------------------------------- */

    public static function get_modules_with_reactions_parameters() {
        return new external_function_parameters(
                array(
                        'userid' => new external_value(PARAM_INT, 'id of user', VALUE_REQUIRED),
                        'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
                )
                );
    }

    public static function get_modules_with_reactions($userid, $courseid) {
        $params = self::validate_parameters(self::get_database_parameters(), array(
                'userid' => $userid,
                'courseid' => $courseid,
        ));

        require_once(__DIR__ . '/locallib.php');
        return block_point_view_get_modules_with_reactions($params['userid'], $params['courseid']);
    }

    public static function get_modules_with_reactions_returns() {
        return new external_multiple_structure(
                new external_single_structure(
                        array(
                                'cmid' => new external_value(PARAM_INT, 'CMID'),
                                'totaleasy' => new external_value(PARAM_INT, 'Total \'easy\' votes'),
                                'totalbetter' => new external_value(PARAM_INT, 'Total \'better\' votes'),
                                'totalhard' => new external_value(PARAM_INT, 'Total \'hard\' votes'),
                                'uservote' => new external_value(PARAM_INT, 'User vote')
                        )
                        )
                );
    }
}