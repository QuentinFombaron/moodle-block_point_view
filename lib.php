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
function block_like_get_activities($courseid, $config = null) {
    $modinfo = get_fast_modinfo($courseid, -1);
    $sections = $modinfo->get_sections();
    $activities = array();
    foreach ($modinfo->instances as $module => $instances) {
        $modulename = get_string('pluginname', $module);
        foreach ($instances as $index => $cm) {
            if (in_array($module, $config->moduletype) || $config == null) {
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

    return $activities;
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

function block_like_manage_types($mform, $types) {
    foreach ($types as $type) {
        $manage = array();
        try {
            $manage[] =& $mform->createElement('button', 'enable' . ucfirst($type),
                get_string('enable' . $type, 'block_like'));
            $manage[] =& $mform->createElement('button', 'disable' . ucfirst($type),
                get_string('disable' . $type, 'block_like'));
        } catch (coding_exception $e) {
            echo 'Exception coding_exception (specific_definition() -> blocks/like/edit_form.php) : ', $e->getMessage(), "\n";

        }
        $mform->addGroup($manage, 'manage_' . $type, '', array(' '), false);
    }
}