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
 * [TODO]
 *
 * @package [TODO]
 * @copyright [TODO]
 * @license [TODO]
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/../../config.php');

const DEFAULT_LIKE_ENABLELIKES = 1;
const DEFAULT_LIKE_ENABLECUSTOMPIX = 0;

try {
    require_login();
} catch (coding_exception $e) {
    echo 'Exception coding_exception (require_login() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";
} catch (require_login_exception $e) {
    echo 'Exception require_login_exception (require_login() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";
} catch (moodle_exception $e) {
    echo 'Exception moodle_exception (require_login() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";
}

/**
 * Returns the activities in current course
 *
 * @param int    courseid   ID of the course
 * @param int    config     The block instance configuration
 * @return array Activities with completion settings in the course
 * @throws moodle_exception
 */
function block_like_get_course_data ($courseid) {
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

    usort($activities, 'block_like_compare_activities');

    return array('activities' => $activities, 'types' => $types, 'ids' => $ids);
}

/**
 * Used to compare two activities/resources based on order on course page
 *
 * @param array $a array of event information
 * @param array $b array of event information
 * @return mixed <0, 0 or >0 depending on order of activities/resources on course page
 */
function block_like_compare_activities($a, $b) {
    if ($a['section'] != $b['section']) {
        return $a['section'] - $b['section'];
    } else {
        return $a['position'] - $b['position'];
    }
}

/**
 * Create a group of buttons to Enable/Disable activities by types
 * @param $mform
 * @param $types
 */
function block_like_manage_types($mform, $types) {
    foreach ($types as $type) {
        if ($type == 'quiz') {
            $typename = 'quizzes';
        } else if ($type == 'glossary') {
            $typename = 'glossaries';
        } else {
            $typename = $type.'s';
        }
        $manage = array();
        try {
            $manage[] =& $mform->createElement(
                'button',
                'enableall' . $type,
                get_string('enable_type', 'block_like', ucfirst($typename)),
                array('class' => 'manage')
            );
            $manage[] =& $mform->createElement(
                'button',
                'disableall' . $type,
                get_string('disable_type', 'block_like', ucfirst($typename))
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
                'block_like'
            );

        } catch (coding_exception $e) {
            echo 'Exception coding_exception (specific_definition() -> blocks/like/edit_form.php) : ', $e->getMessage(), "\n";
        }
    }
}

/**
 * File serving.
 *
 * @param stdClass $course The course object.
 * @param stdClass $bi Block instance record.
 * @param context $context The context object.
 * @param string $filearea The file area.
 * @param array $args List of arguments.
 * @param bool $forcedownload Whether or not to force the download of the file.
 * @param array $options Array of options.
 * @return void|false
 */
function block_like_pluginfile($course, $bi, $context, $filearea, $args, $forcedownload, array $options = array()) {
    $fs = get_file_storage();

    if (($filearea == 'likes_pix') || ($filearea == 'likes_pix_admin')) {
        $itemid = array_shift($args);

        if ($itemid != 0) {
            return false;
        }

        $filename = array_shift($args);
        $filepath = '/';
        $file = $fs->get_file($context->id, 'block_like', $filearea, $itemid, $filepath, $filename . '.png');
    } else {
        return false;
    }

    if (!$file) {
        return false;
    }

    send_stored_file($file, 0, 0, true, $options);
}

/**
 * @param $context
 * @param $react
 * @return string
 */
function block_like_pix_url($context, $filearea, $react) {
    return strval(moodle_url::make_pluginfile_url(
        $context,
        'block_like',
        $filearea,
        0,
        '/',
        $react)
    );
}

function tostring($output, $data, $users, $course) {
    $string = '';

    foreach ($data as $item) {
        $string .= $output->user_picture($users[$item], array('course' => $course->id)) .
            $users[$item]->firstname . ' ' . $users[$item]->lastname . '<br />';
    }
    return $string;
}