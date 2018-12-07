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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/blocks/point_view/lib.php');

try {
    require_login();
} catch (coding_exception $e) {
    echo 'Exception [coding_exception] (blocks/point_view/block_point_view.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (require_login_exception $e) {
    echo 'Exception [require_login_exception] (blocks/point_view/block_point_view.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (moodle_exception $e) {
    echo 'Exception [moodle_exception] (blocks/point_view/block_point_view.php -> require_login()) : ',
    $e->getMessage(), "\n";
}

/**
 * Class block_point_view_external
 *
 * @package block_point_view
 * @copyright  2018 Quentin Fombaron
 * @author     Quentin Fombaron <quentin.fombaron1@etu.univ-grenoble-alpes.fr>
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
                'userid' => new external_value(PARAM_INT, 'id of user', VALUE_REQUIRED),
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
                'cmid' => new external_value(PARAM_INT, 'id of course module', VALUE_REQUIRED),
                'vote' => new external_value(PARAM_INT, 'id of vote', VALUE_REQUIRED)
            )
        );
    }

    /**
     * Update the database after added, removed or removed a vote or all votes of course.
     *
     * @param string $func Function name of database update
     * @param int $userid User ID
     * @param int $courseid Course ID
     * @param int $cmid Course Module ID
     * @param int $vote Vote ID
     * @return string Log message
     * @throws invalid_parameter_exception
     * @throws dml_exception
     */
    public static function update_db(string $func, int $userid, int $courseid, int $cmid, int $vote) {
        global $DB;

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

                $DB->insert_record($table, $dataobject, false);

                return 'Add OK';

                break;

            /* REMOVE a line in block_point_view table */
            case 'remove':
                $conditions = array(
                    'userid' => $params['userid'],
                    'courseid' => $params['courseid'],
                    'cmid' => $params['cmid'],
                    'vote' => $params['vote']
                );

                $DB->delete_records($table, $conditions);

                return 'Remove OK';

                break;

            /* UPDATE a line in block_point_view table */
            case 'update':

                /* Get the good record to have the ID (ask by 'update_record' function)*/
                $target = $DB->get_record(
                    $table,
                    array(
                        'userid' => $params['userid'],
                        'courseid' => $params['courseid'],
                        'cmid' => $params['cmid']
                    )
                );

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
            $pointviews = (!empty($result)) ? array_values($result) : array();
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
        global $CFG, $DB;

        self::validate_parameters(self::get_pixparam_parameters(), array(
                'courseid' => $courseid,
                'contextid' => $contextid
            )
        );

        $coursecontext = context_course::instance($courseid);
        $blockrecord = $DB->get_record('block_instances', array('blockname' => 'point_view',
            'parentcontextid' => $coursecontext->id), '*', MUST_EXIST);
        $blockinstance = block_instance('point_view', $blockrecord);

        $pixparam = array(
            'easy' => $CFG->wwwroot . '/blocks/point_view/pix/easy.png',
            'easytxt' => (isset($blockinstance->config->text_easy)) ?
                $blockinstance->config->text_easy
                : get_string('defaulttexteasy', 'block_point_view' ),
            'better' => $CFG->wwwroot . '/blocks/point_view/pix/better.png',
            'bettertxt' => (isset($blockinstance->config->text_better)) ?
                $blockinstance->config->text_better
                : get_string('defaulttextbetter', 'block_point_view'),
            'hard' => $CFG->wwwroot . '/blocks/point_view/pix/hard.png',
            'hardtxt' => (isset($blockinstance->config->text_hard)) ?
                $blockinstance->config->text_hard
                : get_string('defaulttexthard', 'block_point_view'),
            'group_' => $CFG->wwwroot . '/blocks/point_view/pix/group_.png',
            'group_E' => $CFG->wwwroot . '/blocks/point_view/pix/group_E.png',
            'group_B' => $CFG->wwwroot . '/blocks/point_view/pix/group_B.png',
            'group_H' => $CFG->wwwroot . '/blocks/point_view/pix/group_H.png',
            'group_EB' => $CFG->wwwroot . '/blocks/point_view/pix/group_EB.png',
            'group_EH' => $CFG->wwwroot . '/blocks/point_view/pix/group_EH.png',
            'group_BH' => $CFG->wwwroot . '/blocks/point_view/pix/group_BH.png',
            'group_EBH' => $CFG->wwwroot . '/blocks/point_view/pix/group_EBH.png',
        );

        $pixfiles = array(
            'easy',
            'better',
            'hard',
            'group_',
            'group_E',
            'group_B',
            'group_H',
            'group_EB',
            'group_EH',
            'group_BH',
            'group_EBH'
        );

        $fs = get_file_storage();

        if (get_config('block_point_view', 'enable_pix_admin')) {

            foreach ($pixfiles as $file) {

                if ($fs->file_exists(1, 'block_point_view', 'point_views_pix_admin', 0, '/', $file . '.png')) {

                    $pixparam[$file] = block_point_view_pix_url(1, 'point_views_pix_admin', $file);

                }
            }
        } else {

            $fs->delete_area_files(1, 'block_point_view');

        }

        if (isset($blockinstance->config->enable_pix_checkbox) && $blockinstance->config->enable_pix_checkbox) {

            foreach ($pixfiles as $file) {

                if ($fs->file_exists($contextid, 'block_point_view', 'point_views_pix', 0, '/', $file . '.png')) {

                    $pixparam[$file] = block_point_view_pix_url($contextid, 'point_views_pix', $file);

                }
            }
        } else {

            $fs->delete_area_files($contextid, 'block_point_view');

        }

        return $pixparam;

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
     * Get modules of a course
     *
     * @param int $courseid course ID
     * @return array Seleted modules
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function get_moduleselect($courseid) {
        global $DB;

        self::validate_parameters(self::get_moduleselect_parameters(), array(
                'courseid' => $courseid
            )
        );

        $coursecontext = context_course::instance($courseid);
        $blockrecord = $DB->get_record('block_instances', array('blockname' => 'point_view',
            'parentcontextid' => $coursecontext->id), '*', MUST_EXIST);
        $blockinstance = block_instance('point_view', $blockrecord);

        $sqlid = $DB->get_records('course_modules', array('course' => $courseid), null, 'id');

        $moduleselect = array();

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
        global $DB;

        self::validate_parameters(self::get_difficulty_levels_parameters(), array(
                'courseid' => $courseid
            )
        );

        $coursecontext = context_course::instance($courseid);
        $blockrecord = $DB->get_record('block_instances', array('blockname' => 'point_view',
            'parentcontextid' => $coursecontext->id), '*', MUST_EXIST);
        $blockinstance = block_instance('point_view', $blockrecord);

        $sqlid = $DB->get_records('course_modules', array('course' => $courseid), null, 'id');

        $difficultylevels = array();

        foreach ($sqlid as $row) {

            if (isset($blockinstance->config->{'moduleselectm' . $row->id})) {

                if ($blockinstance->config->enable_difficulties_checkbox) {

                    $difficultylevels[$row->id] = array(
                        'id' => $row->id,
                        'difficultyLevel' => $blockinstance->config->{'difficulty_' . $row->id}
                        );

                }
            }
        }

        return $difficultylevels;

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

    /**
     * All necessary parameters
     *
     * @return external_function_parameters
     */
    public static function get_section_range_parameters() {
        return new external_function_parameters(
            array(
                'sectionid' => new external_value(PARAM_INT, 'Max section ID', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Get section ids range
     *
     * @param int $sectionid Max section ID
     * @return array Section IDs
     * @throws invalid_parameter_exception
     */
    public static function get_section_range($sectionid) {
        self::validate_parameters(self::get_section_range_parameters(), array(
                'sectionid' => $sectionid
            )
        );

        return range(2, $sectionid);

    }

    /**
     * Return section ids array
     *
     * @return external_description
     */
    public static function get_section_range_returns() {
        return new external_multiple_structure(
            new external_value(PARAM_INT, 'Section ID')
        );
    }

    /* --------------------------------------------------------------------------------------------------------- */

    /**
     * All necessary parameters
     *
     * @return external_function_parameters
     */
    public static function get_course_data_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED),
                'contextid' => new external_value(PARAM_INT, 'Context ID', VALUE_REQUIRED)
            )
        );
    }

    /**
     * Get course data
     *
     * @param int $courseid Course ID
     * @param int $contextid Context ID
     * @return array Types and course IDs
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function get_course_data($courseid, $contextid) {
        self::validate_parameters(self::get_course_data_parameters(), array(
                'courseid' => $courseid,
                'contextid' => $contextid
            )
        );

        $coursedata = block_point_view_get_course_data($courseid, $contextid);

        return array('types' => $coursedata['types'], 'ids' => $coursedata['ids']);

    }

    /**
     * Return types and course IDs array
     *
     * @return external_description
     */
    public static function get_course_data_returns() {
        return new external_single_structure(
            array(
                'types' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'Course type', VALUE_REQUIRED)
                ),
                'ids' => new external_multiple_structure(
                    new external_value(PARAM_TEXT, 'Course ID', VALUE_REQUIRED)
                ),
            )
        );
    }

    /* --------------------------------------------------------------------------------------------------------- */

    /**
     * All necessary parameters
     *
     * @return external_function_parameters
     */
    public static function get_track_colors_parameters() {
        return new external_function_parameters(
            array()
        );
    }

    /**
     * Get tracks color from general configuration
     *
     * @return array
     * @throws dml_exception
     */
    public static function get_track_colors() {
        $trackcolor = array(
            'greentrack' => get_config('block_point_view', 'green_track_color_admin'),
            'bluetrack' => get_config('block_point_view', 'blue_track_color_admin'),
            'redtrack' => get_config('block_point_view', 'red_track_color_admin'),
            'blacktrack' => get_config('block_point_view', 'black_track_color_admin'),
        );

        return $trackcolor;

    }

    /**
     * Return track colors array
     *
     * @return external_description
     */
    public static function get_track_colors_returns() {
        return new external_single_structure(
            array(
                'greentrack' => new external_value(PARAM_TEXT, 'Green track color', VALUE_REQUIRED),
                'bluetrack' => new external_value(PARAM_TEXT, 'Blue track color', VALUE_REQUIRED),
                'redtrack' => new external_value(PARAM_TEXT, 'Red track color', VALUE_REQUIRED),
                'blacktrack' => new external_value(PARAM_TEXT, 'Black track color', VALUE_REQUIRED),
            )
        );
    }
}