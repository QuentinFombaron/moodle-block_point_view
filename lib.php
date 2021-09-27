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
 * Function's library
 *
 *
 * @package    block_point_view
 * @copyright  2020 Quentin Fombaron
 * @author     Quentin Fombaron <q.fombaron@outlook.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\session\manager;

defined('MOODLE_INTERNAL') || die;

/** Administration configuration to enable Reactions */
const DEFAULT_POINT_VIEW_ENABLE_REACTIONS = 1;

/** Administration configuration to enable custom emojis */
const DEFAULT_POINT_VIEW_ENABLE_CUSTOM_PIX = 0;

/**
 *
 *
 * @param stdClass $course Course object
 * @param stdClass $bi Block instance record
 * @param context_course|context_system $context Context object
 * @param string $filearea File area
 * @param array $args Extra arguments
 * @param bool $forcedownload Whether or not force download
 * @param array $options Additional options affecting the file serving
 *
 * @return bool
 *
 * @throws moodle_exception
 */
function block_point_view_pluginfile($course, $bi, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $CFG, $USER;

    $fs = get_file_storage();
    $filename = array_pop($args);

    if ($filearea === 'content') {
        if ($context->contextlevel != CONTEXT_BLOCK) {
            send_file_not_found();
        }

        if ($context->get_course_context(false)) {
            require_course_login($course);
        } else if ($CFG->forcelogin) {
            require_login();
        } else {
            $parentcontext = $context->get_parent_context();
            if ($parentcontext->contextlevel === CONTEXT_COURSECAT) {
                if (!core_course_category::get($parentcontext->instanceid, IGNORE_MISSING)) {
                    send_file_not_found();
                }
            } else if ($parentcontext->contextlevel === CONTEXT_USER && $parentcontext->instanceid != $USER->id) {
                send_file_not_found();
            }
        }

        $file = $fs->get_file(
            $context->id,
            'block_point_view',
            $filearea,
            0,
            '/',
            $filename
        );
        if (!$file || $file->is_directory()) {
            send_file_not_found();
        }
    } else if (($filearea === 'point_views_pix') || ($filearea === 'point_views_pix_admin')) {
        $file = $fs->get_file(
            $context->id,
            'block_point_view',
            $filearea,
            0,
            '/',
            $filename . '.png'
        );
        if (!$file || $file->is_directory()) {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }

    manager::write_close();
    send_stored_file($file, null, 0, true, $options);

    return true;
}

/**
 * Perform global search replace such as when migrating site to new URL.
 *
 * @param string $search
 * @param string $replace
 *
 * @throws dml_exception
 */
function block_point_view_global_db_replace($search, $replace) {
    global $DB;

    $instances = $DB->get_recordset('block_instances', array('blockname' => 'point_view'));
    foreach ($instances as $instance) {
        $config = unserialize(base64_decode($instance->configdata));
        if (isset($config->text) and is_string($config->text)) {
            $config->text = str_replace($search, $replace, $config->text);
            $DB->update_record('block_instances', ['id' => $instance->id,
                'configdata' => base64_encode(serialize($config)), 'timemodified' => time()]);
        }
    }
    $instances->close();
}

/**
 * Given an array with a file path, it returns the itemid and the filepath for the defined filearea.
 *
 * @param string $filearea
 * @param array $args
 *
 * @return array
 */
function block_point_view_get_path_from_pluginfile($filearea, $args) {
    array_shift($args);

    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    return [
        'itemid' => 0,
        'filepath' => $filepath,
    ];
}