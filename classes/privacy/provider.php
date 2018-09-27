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
 * @copyright  2018 Quentin Fombaron
 * @author     Quentin Fombaron <quentin.fombaron1@etu.univ-grenoble-alpes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_point_view\privacy;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../config.php');

try {
    require_login();
} catch (\coding_exception $e) {
    echo 'Exception [coding_exception] (blocks/point_view/classes/privacy/provider.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (\require_login_exception $e) {
    echo 'Exception [require_login_exception] (blocks/point_view/classes/privacy/provider.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (\moodle_exception $e) {
    echo 'Exception [moodle_exception] (blocks/point_view/classes/privacy/provider.php -> require_login()) : ',
    $e->getMessage(), "\n";
}

use core_privacy\local\metadata\collection;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\helper;

/**
 * Class provider
 *
 * @package block_point_view
 * @copyright  2018 Quentin Fombaron
 * @author     Quentin Fombaron <quentin.fombaron1@etu.univ-grenoble-alpes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin does store personal user data.
    \core_privacy\local\metadata\provider {

    /**
     * Point of View Metadata
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection) : collection {

        $collection->add_subsystem_link(
            'activity_votes',
            [],
            'privacy:metadata:activity_votes'
        );

        $collection->add_database_table(
            'activity_votes_database',
            [
                'courseid' => 'privacy:metadata:activity_votes_database:courseid',
                'cmid' => 'privacy:metadata:activity_votes_database:cmid',
                'userid' => 'privacy:metadata:activity_votes_database:userid',
                'vote' => 'privacy:metadata:activity_votes_database:vote'

            ],
            'privacy:metadata:activity_votes_database'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return \core_privacy\local\request\contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : \core_privacy\local\request\contextlist {
        // This block doesn't know who information is stored against unless it
        // is at the user context.
        $contextlist = new \core_privacy\local\request\contextlist();

        $sql = 'SELECT c.id FROM {block_instances} b
        INNER JOIN {context} c ON c.instanceid = b.id AND c.contextlevel = :contextblock
        INNER JOIN {context} bpc ON bpc.id = b.parentcontextid
        WHERE b.blockname = \'point_view\'
        AND bpc.contextlevel = :contextuser
        AND bpc.instanceid = :userid';

        $params = [
            'contextblock' => CONTEXT_BLOCK,
            'contextuser' => CONTEXT_USER,
            'userid' => $userid,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT c.id AS contextid, bi.*
                FROM {context} c
                INNER JOIN {block_instances} bi ON bi.id = c.instanceid AND c.contextlevel = :contextlevel
                WHERE bi.blockname = 'point_view' AND(c.id {$contextsql})";

        $params = [
            'contextlevel' => CONTEXT_BLOCK,
        ];
        $params += $contextparams;

        $instances = $DB->get_recordset_sql($sql, $params);
        foreach ($instances as $instance) {
            $context = \context_block::instance($instance->id);
            $block = block_instance('point_view', $instance);
            if (empty($block->config)) {
                // Skip this block. It has not been configured.
                continue;
            }

            $pointview = writer::with_context($context)
                ->rewrite_pluginfile_urls([], 'block_point_view', 'content', null, $block->config->text);

            // Default to FORMAT_HTML which is what will have been used before the
            // editor was properly implemented for the block.
            $format = isset($block->config->format) ? $block->config->format : FORMAT_HTML;

            $filteropt = (object) [
                'overflowdiv' => true,
                'noclean' => true,
            ];
            $pointview = format_text($pointview, $format, $filteropt);

            $data = helper::get_context_data($context, $user);
            helper::export_context_files($context, $user);
            $data->title = $block->config->title;
            $data->content = $pointview;

            writer::with_context($context)->export_data([], $data);
        }
        $instances->close();
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist
     * @throws \dml_exception
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // The only way to delete data for the point_view block is to delete the block instance itself.
        foreach ($contextlist as $context) {

            if (!$context instanceof \context_block) {
                continue;
            }
            blocks_delete_instance(static::get_instance_from_context($context));
        }
    }

    /**
     * Get the block instance record for the specified context.
     *
     * @param \context_block $context
     * @return mixed
     * @throws \dml_exception
     */
    protected static function get_instance_from_context(\context_block $context) {
        global $DB;

        return $DB->get_record('block_instances', ['id' => $context->instanceid]);
    }
}