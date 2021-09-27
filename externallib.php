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

global $CFG;
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

                $canreact = isset($blockinstance->config->enable_point_views)
                        && $blockinstance->config->enable_point_views
                        && isset($blockinstance->config->{'moduleselectm' . $params['cmid']})
                        && $blockinstance->config->{'moduleselectm' . $params['cmid']}
                        && get_fast_modinfo($params['courseid'], $USER->id)->cms[$params['cmid']]->uservisible;

                if (!$canreact) {
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

    public static function delete_custom_pix_parameters() {
        return new external_function_parameters(
                array(
                        'contextid' => new external_value(PARAM_INT, 'id of context', VALUE_REQUIRED),
                        'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
                        'draftitemid' => new external_value(PARAM_INT, 'id of draft file area', VALUE_REQUIRED)
                )
        );
    }

    public static function delete_custom_pix($contextid, $courseid, $draftitemid) {
        global $USER;
        require_capability('moodle/site:manageblocks', context_course::instance($courseid));
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
}