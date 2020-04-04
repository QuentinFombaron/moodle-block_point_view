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
 * @copyright  2020 Jayson haulkory
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer.
 * Stores all actions about modules create/update/delete in plugin own's table.
 * This allows the block to avoid expensive queries to the log table.
 *
 * @package    block_recent_activity
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_point_view_observer {

    /**
     * Reactions automaticaly activated when a new activity is created (only if reactions are enabled).
     *
     * @param \core\event\base $event
     */
    public static function store(\core\event\base $event) {
        global $DB, $CFG, $COURSE;
        echo $COURSE->id;
        if (intval($COURSE->id) !== intval(1)) {
            $coursecontext = context_course::instance($event->courseid);
            $blockrecord = $DB->get_record('block_instances', array('blockname' => 'point_view',
            'parentcontextid' => $coursecontext->id), '*');
        } else {
            $homepagecontext = $DB->get_record("context", array('contextlevel' => intval(50),
                'instanceid' => intval(1)), 'id', MUST_EXIST);
            $blockrecord = $DB->get_record('block_instances', array('blockname' => 'point_view',
                'parentcontextid' => intval($homepagecontext->id)), '*');
        }
        if (!empty($blockrecord->configdata)) {
            $blockinstance = block_instance('point_view', $blockrecord);
            $blockinstance->config->enable_point_views_checkbox;

            $enablepointviewscheckbox = (isset($blockinstance->config->enable_point_views_checkbox)) ?
            $blockinstance->config->enable_point_views_checkbox :
            0;

            if ($enablepointviewscheckbox) {
                try {
                    $moduleselectm = "moduleselectm" . $event->objectid;
                    $blockinstance->config->$moduleselectm = $event->objectid;

                    $DB->update_record("block_instances", array('id' => $blockrecord->id,
                        'configdata' => base64_encode(serialize($blockinstance->config))));
                } catch (dml_exception $e) {

                    return 'Exception : ' . $e->getMessage() . '\n';

                }
            }
        }

    }
}