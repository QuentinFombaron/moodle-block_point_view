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
 * TODO
 *0b619f
 * @copyright TODO
 * @license   TODO
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/blocks/like/lib.php');

if ($ADMIN->fulltree) {
    try {
        $settings->add(new admin_setting_configcheckbox(
            'block_like/enable_likes_admin',
            get_string('enablelikes', 'block_like'),
            '',
            DEFAULT_LIKE_ENABLELIKES
        ));

        $settings->add(new admin_setting_configcheckbox(
            'block_like/enable_pix_admin',
            get_string('enablecustompix', 'block_like'),
            '',
            DEFAULT_LIKE_ENABLECUSTOMPIX
        ));

        $settings->add(new admin_setting_configstoredfile(
            'block_like/likes_pix_admin',
            new lang_string('likepix', 'block_like'),
            new lang_string('likepixdesc', 'block_like'),
            'likes_pix_admin',
            0,
            ['subdirs' => 0, 'maxfiles' => 11, 'accepted_types' => '.png']
        ));

        $settings->add(new admin_setting_configcolourpicker(
            'block_like/green_track_color_admin',
            'Green track color',
            '',
            '#129800',
            null,
            true
        ));

        $settings->add(new admin_setting_configcolourpicker(
            'block_like/blue_track_color_admin',
            'Blue track color',
            '',
            '#0B619F',
            null,
            true
        ));

        $settings->add(new admin_setting_configcolourpicker(
            'block_like/red_track_color_admin',
            'Red track color',
            '',
            '#BD0F29',
            null,
            true
        ));

        $settings->add(new admin_setting_configcolourpicker(
            'block_like/black_track_color_admin',
            'Black track color',
            '',
            '#01262E',
            null,
            true
        ));

    } catch (coding_exception $e) {
        echo 'Exception coding_exception -> blocks/like/settings.php) : ', $e->getMessage(), "\n";
    }
}