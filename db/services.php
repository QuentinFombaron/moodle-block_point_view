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
 * Point of View services
 *
 *
 * @package    block_point_view
 * @copyright  2018 Quentin Fombaron
 * @author     Quentin Fombaron <quentin.fombaron1@etu.univ-grenoble-alpes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'block_point_view_update_db' => array(
        'classname'   => 'block_point_view_external',
        'methodname'  => 'update_db',
        'classpath'   => 'blocks/point_view/externallib.php',
        'description' => 'Update Database due to a vote.',
        'type'        => 'write',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'block_point_view_get_database' => array(
        'classname'   => 'block_point_view_external',
        'methodname'  => 'get_database',
        'classpath'   => 'blocks/point_view/externallib.php',
        'description' => 'Get votes data from database',
        'type'        => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'block_point_view_get_pixparam' => array(
        'classname'   => 'block_point_view_external',
        'methodname'  => 'get_pixparam',
        'classpath'   => 'blocks/point_view/externallib.php',
        'description' => 'Get picture parameters',
        'type'        => 'write',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'block_point_view_get_moduleselect' => array(
        'classname'   => 'block_point_view_external',
        'methodname'  => 'get_moduleselect',
        'classpath'   => 'blocks/point_view/externallib.php',
        'description' => 'Get course module selection',
        'type'        => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'block_point_view_get_difficulty_levels' => array(
        'classname'   => 'block_point_view_external',
        'methodname'  => 'get_difficulty_levels',
        'classpath'   => 'blocks/point_view/externallib.php',
        'description' => 'Get difficulty levels',
        'type'        => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'block_point_view_get_section_range' => array(
        'classname'   => 'block_point_view_external',
        'methodname'  => 'get_section_range',
        'classpath'   => 'blocks/point_view/externallib.php',
        'description' => 'Get section ID range',
        'type'        => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'block_point_view_get_course_data' => array(
        'classname'   => 'block_point_view_external',
        'methodname'  => 'get_course_data',
        'classpath'   => 'blocks/point_view/externallib.php',
        'description' => 'Get Course data',
        'type'        => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'block_point_view_get_track_colors' => array(
        'classname'   => 'block_point_view_external',
        'methodname'  => 'get_track_colors',
        'classpath'   => 'blocks/point_view/externallib.php',
        'description' => 'Get Track Color',
        'type'        => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'block_point_view_get_enrol_list' => array(
        'classname'   => 'block_point_view_external',
        'methodname'  => 'get_enrol_list',
        'classpath'   => 'blocks/point_view/externallib.php',
        'description' => 'ids list of courses enrolled by user',
        'type'        => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    )
);

$services = array(
        'Point of View Service' => array(
        'functions' => array(
            'block_point_view_update_db',
            'block_point_view_get_database',
            'block_point_view_get_pixparam',
            'block_point_view_get_moduleselect',
            'block_point_view_get_difficulty_levels',
            'block_point_view_get_section_range',
            'block_point_view_get_course_data',
            'block_point_view_get_track_colors',
            'block_point_view_get_enrol_list'
        ),
        'requiredcapability' => '',
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);