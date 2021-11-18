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
 * Administration configuration
 *
 *
 * @package    block_point_view
 * @copyright  2020 Quentin Fombaron
 * @author     Quentin Fombaron <q.fombaron@outlook.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configcheckbox(
        'block_point_view/enable_point_views_admin',
        new lang_string('enablepoint_views', 'block_point_view'),
        '',
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'block_point_view/enable_pix_admin',
        new lang_string('enablecustompix', 'block_point_view'),
        '',
        0
    ));

    $settings->add(new admin_setting_configstoredfile(
        'block_point_view/point_views_pix_admin',
        new lang_string('customemoji', 'block_point_view'),
        new lang_string('customemoji_help', 'block_point_view'),
        'point_views_pix_admin',
        0,
        ['subdirs' => 0, 'maxfiles' => 11, 'accepted_types' => '.png']
    ));

    $settings->add(new admin_setting_configcolourpicker(
        'block_point_view/green_track_color_admin',
        new lang_string('greentrack', 'block_point_view'),
        '',
        '#129800',
        null,
        true
    ));

    $settings->add(new admin_setting_configcolourpicker(
        'block_point_view/blue_track_color_admin',
        new lang_string('bluetrack', 'block_point_view'),
        '',
        '#0B619F',
        null,
        true
    ));

    $settings->add(new admin_setting_configcolourpicker(
        'block_point_view/red_track_color_admin',
        new lang_string('redtrack', 'block_point_view'),
        '',
        '#BD0F29',
        null,
        true
    ));

    $settings->add(new admin_setting_configcolourpicker(
        'block_point_view/black_track_color_admin',
        new lang_string('blacktrack', 'block_point_view'),
        '',
        '#01262E',
        null,
        true
    ));
}
