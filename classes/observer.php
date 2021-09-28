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
 * Event observer.
 *
 * @package    block_point_view
 * @copyright  2020 Jayson Haulkory
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer.
 *
 * @package    block_point_view
 * @copyright  2020 Jayson Haulkory
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_point_view_observer {

    /**
     * Reactions automatically activated when a new activity is created (only if reactions are enabled).
     *
     * @param \core\event\course_module_created $event
     */
    public static function store(\core\event\course_module_created $event) {
        global $DB;

        $coursecontext = context_course::instance($event->courseid);
        $blockrecord = $DB->get_record('block_instances', array('blockname' => 'point_view',
                'parentcontextid' => $coursecontext->id), '*');

        if (!empty($blockrecord->configdata)) {
            $blockinstance = block_instance('point_view', $blockrecord);

            $enablefornewmodules = isset($blockinstance->config->enable_point_views)
                                    && $blockinstance->config->enable_point_views
                                    && (!isset($blockinstance->config->enable_point_views_new_modules)
                                            || $blockinstance->config->enable_point_views_new_modules);

            if ($enablefornewmodules) {
                $blockinstance->config->{'moduleselectm' . $event->objectid} = $event->objectid;
                $blockinstance->instance_config_commit();
            }
        }

    }

    /**
     * Course module deleted - delete config data and database entries for votes for this module.
     *
     * @param \core\event\course_module_deleted $event
     */
    public static function remove(\core\event\course_module_deleted $event) {
        global $DB;

        $coursecontext = context_course::instance($event->courseid);
        $blockrecord = $DB->get_record('block_instances', array('blockname' => 'point_view',
                'parentcontextid' => $coursecontext->id), '*');

        if (!empty($blockrecord->configdata)) {
            $blockinstance = block_instance('point_view', $blockrecord);
            unset($blockinstance->config->{'moduleselectm' . $event->objectid});
            unset($blockinstance->config->{'difficulty_' . $event->objectid});
            $blockinstance->instance_config_commit();
        }

        $DB->delete_records('block_point_view', array('courseid' => $event->courseid, 'cmid' => $event->objectid));
    }
}