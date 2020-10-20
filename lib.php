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
 * Returns the activities in current course
 *
 * @param int $courseid ID of the course
 * @param null $contextid
 * @return array Activities with completion settings in the course
 * @throws coding_exception
 * @throws moodle_exception
 */
function block_point_view_get_course_data($courseid, $contextid = null) {
    global $PAGE;

    $PAGE->set_context($contextid);

    $modinfo = get_fast_modinfo($courseid, -1);

    $sections = $modinfo->get_sections();

    $activities = array();

    $types = array();

    $ids = array();

    foreach ($modinfo->instances as $module => $instances) {

        $modulename = get_string('pluginname', $module);

        foreach ($instances as $index => $cm) {

            if ($module != 'label') {

                if (!in_array($module, $types)) {

                    array_push($types, $module);

                }

                array_push($ids, $cm->id);

                $activities[] = array(
                    'type'       => $module,
                    'modulename' => $modulename,
                    'id'         => $cm->id,
                    'instance'   => $cm->instance,
                    'name'       => $cm->name,
                    'expected'   => $cm->completionexpected,
                    'section'    => $cm->sectionnum,
                    'position'   => array_search($cm->id, $sections[$cm->sectionnum]),
                    'url'        => method_exists($cm->url, 'out') ? $cm->url->out() : '',
                    'context'    => $cm->context,
                    'icon'       => $cm->get_icon_url(),
                    'available'  => $cm->available,
                );
            }
        }
    }

    usort($activities, 'block_point_view_compare_activities');

    return array('activities' => $activities, 'types' => $types, 'ids' => $ids);
}

/**
 * Used to compare two activities/resources based on order on course page
 *
 * @param array $a array of event information
 * @param array $b array of event information
 * @return mixed <0, 0 or >0 depending on order of activities/resources on course page
 */
function block_point_view_compare_activities($a, $b) {

    if ($a['section'] != $b['section']) {

        return $a['section'] - $b['section'];

    } else {

        return $a['position'] - $b['position'];

    }
}

/**
 * Create a group of buttons to Enable/Disable activities by types
 *
 * @param stdClass $mform
 * @param array $types
 * @throws coding_exception
 */
function block_point_view_manage_types($mform, $types) {

    foreach ($types as $type) {

        $manage = array();

            $manage[] =& $mform->createElement(
                'button',
                'enableall' . $type,
                get_string('enable_type', 'block_point_view')
                .' <b>' . get_string($type, 'block_point_view') . '</b>',
                array('class' => 'manage')
            );

            $manage[] =& $mform->createElement(
                'button',
                'disableall' . $type,
                get_string('disable_type', 'block_point_view')
                .' <b>' . get_string($type, 'block_point_view') . '</b>',
                array('class' => 'manage')
            );

            $mform->addGroup(
                $manage,
                $type.'_group_type',
                '',
                array(' '),
                false
            );

            $mform->addHelpButton(
                $type.'_group_type',
                'howto_type',
                'block_point_view'
            );

    }
}

/**
 * Form for editing HTML block instances.
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
 * Reaction image
 *
 * @param int $contextid
 * @param string $filearea
 * @param string $react
 * @return string
 */
function block_point_view_pix_url($contextid, $filearea, $react) {

    return strval(moodle_url::make_pluginfile_url(
        $contextid,
        'block_point_view',
        $filearea,
        0,
        '/',
        $react)
    );

}

/**
 * User data string for the overview table
 *
 * @param stdClass $output
 * @param stdClass $data
 * @param stdClass $users
 * @param stdClass $course
 * @return string
 */
function tostring($output, $data, $users, $course) {

    $string = '';

    foreach ($data as $item) {

        $string .= $output->user_picture($users[$item], array('course' => $course->id)) .
            $users[$item]->firstname . ' ' . $users[$item]->lastname . '<br />';

    }

    return $string;
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