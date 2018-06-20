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
 *
 * @copyright TODO
 * @license   TODO
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/blocks/like/lib.php');

if ($ADMIN->fulltree) {
    try {
        $settings->add(new admin_setting_configcheckbox(
            'block_like/enable_likes',
            get_string('enablelikes', 'block_like'),
            '',
            DEFAULT_LIKE_ENABLELIKES
        ));

        $settings->add(new admin_setting_configcheckbox(
            'block_like/enable_custom_likes_pix',
            get_string('enablecustompix', 'block_like'),
            '',
            DEFAULT_LIKE_ENABLECUSTOMPIX
        ));

        $settings->add(new admin_setting_configstoredfile(
            'block_like/likes_pix',
            new lang_string('likepix', 'block_like'),
            new lang_string('likepixdesc', 'block_like'),
            'preset',
            0,
            ['subdirs' => 0, 'maxfiles' => 20, 'accepted_types' => '.png']
        ));
    } catch (coding_exception $e) {
        echo 'Exception coding_exception -> blocks/like/settings.php) : ', $e->getMessage(), "\n";
    }
}