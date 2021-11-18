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
 * Point of View privacy
 *
 *
 * @package    block_point_view
 * @copyright  2020 Quentin Fombaron
 * @author     Quentin Fombaron <q.fombaron@outlook.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_point_view\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_userlist;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\userlist;

/**
 * Class provider
 *
 * @package block_point_view
 * @copyright  2020 Quentin Fombaron
 * @author     Quentin Fombaron <q.fombaron@outlook.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This tool stores user data.
    \core_privacy\local\metadata\provider,

    // This plugin is capable of determining which users have data within it.
    \core_privacy\local\request\core_userlist_provider,

    // This tool may provide access to and deletion of user data.
    \core_privacy\local\request\plugin\provider
{

    /**
     * Point of View Metadata
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'block_point_view',
            [
                'courseid' => 'privacy:metadata:activity_votes_database:courseid',
                'cmid' => 'privacy:metadata:activity_votes_database:cmid',
                'userid' => 'privacy:metadata:activity_votes_database:userid',
                'vote' => 'privacy:metadata:activity_votes_database:vote'

            ],
            'privacy:metadata:block_point_view'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $sql = 'SELECT DISTINCT ctx.id
                FROM {block_point_view} bpv
                JOIN {context} ctx
                    ON ctx.instanceid = bpv.userid
                        AND ctx.contextlevel = :contextlevel
                WHERE bpv.userid = :userid';

        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];

        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts to export information for.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        $pointviewdata = [];
        $results = static::get_records($contextlist->get_user()->id);
        foreach ($results as $result) {
            $pointviewdata[] = (object) [
                'courseid' => $result->courseid,
                'cmid' => $result->cmid,
                'vote' => $result->vote
            ];
        }
        if (!empty($pointviewdata)) {
            $data = (object) [
                'votes' => $pointviewdata,
            ];
            \core_privacy\local\request\writer::with_context($contextlist->current())->export_data([
                get_string('pluginname', 'block_point_view')], $data);
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     * @throws \dml_exception
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        if ($context instanceof \context_user) {
            static::delete_data($context->instanceid);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     * @throws \dml_exception
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        static::delete_data($contextlist->get_user()->id);
    }

    /**
     * Delete data related to a userid.
     *
     * @param  int $userid The user ID
     * @throws \dml_exception
     */
    protected static function delete_data($userid) {
        global $DB;

        $DB->delete_records('block_point_view', ['userid' => $userid]);
    }

    /**
     * Get records related to this plugin and user.
     *
     * @param  int $userid The user ID
     * @return array An array of records.
     * @throws \dml_exception
     */
    protected static function get_records($userid) {
        global $DB;

        return $DB->get_records('block_point_view', ['userid' => $userid]);
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        $params = [
            'contextid' => $context->id,
            'contextuser' => CONTEXT_USER,
        ];

        $sql = "SELECT bpv.userid as userid
                  FROM {block_point_view} bpv
                  JOIN {context} ctx
                       ON ctx.instanceid = bpv.userid
                       AND ctx.contextlevel = :contextuser
                 WHERE ctx.id = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist $userlist The approved context and user information to delete information for.
     * @throws \dml_exception
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();

        if ($context instanceof \context_user) {
            static::delete_data($context->instanceid);
        }
    }
}
