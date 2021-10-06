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

function xmldb_block_point_view_upgrade($oldversion, $block) {
    global $DB, $OUTPUT;
    $v1_6 = 2021092308; // Block v1.6.
    if ($oldversion < $v1_6) {
        echo $OUTPUT->notification('Some capabilities have changed
            (block/point_view:view -> X,
            block/point_view:access_menu -> block/point_view:access_overview),
            please check permissions on administration tab.', \core\output\notification::NOTIFY_INFO);

        $blockrecords = $DB->get_records('block_instances', array('blockname' => 'point_view'));
        foreach ($blockrecords as $blockrecord) {
            if (!empty($blockrecord->configdata)) {
                $blockinstance = block_instance('point_view', $blockrecord);

                $config = clone($blockinstance->config);
                if (isset($config->enable_point_views_checkbox)) {
                    $config->enable_point_views = $config->enable_point_views_checkbox;
                    unset($config->enable_point_views_checkbox);
                }

                if (isset($config->enable_difficulties_checkbox)) {
                    $config->enable_difficultytracks = $config->enable_difficulties_checkbox;
                    unset($config->enable_difficulties_checkbox);
                }

                if (!isset($config->pixselect)) {
                    $custompix = isset($config->enable_pix_checkbox) && $config->enable_pix_checkbox;
                    if ($custompix) {
                        $config->pixselect = 'custom';
                    } else if (get_config('block_point_view', 'enable_pix_admin')) {
                        $config->pixselect = 'admin';
                    } else {
                        $config->pixselect = 'default';
                    }
                }
                unset($config->enable_pix_checkbox);

                $DB->update_record('block_instances', array(
                        'id' => $blockrecord->id,
                        'configdata' => base64_encode(serialize($config)),
                        'timemodified' => time()
                ));
            }
        }
        upgrade_block_savepoint( true , $v1_6, 'point_view');
    }

    return true;
}