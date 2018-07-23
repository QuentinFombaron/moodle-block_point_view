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
 * Construct tabs in the menu
 *
 *
 * @package    block_point_view
 * @copyright  2018 Quentin Fombaron
 * @author     Quentin Fombaron <quentin.fombaron1@etu.univ-grenoble-alpes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/../../config.php');

try {
    require_login();
} catch (coding_exception $e) {
    echo 'Exception [coding_exception] (blocks/point_view/tabs.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (require_login_exception $e) {
    echo 'Exception [require_login_exception] (blocks/point_view/tabs.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (moodle_exception $e) {
    echo 'Exception [moodle_exception] (blocks/point_view/tabs.php -> require_login()) : ',
    $e->getMessage(), "\n";
}

$parameters = array(
        'instanceid' => $id,
        'contextid'  => $contextid,
        'courseid'   => $courseid,
        'enablepix'  => $enablepix,
        'sesskey'    => sesskey(),
    );

try {

    $tabs = array(
        new tabobject(
            'overview',
            new moodle_url("{$CFG->wwwroot}/blocks/point_view/menu.php", $parameters),
            get_string('overview_title_tab', 'block_point_view')
        ),
        new tabobject(
            'export',
            new moodle_url("{$CFG->wwwroot}/blocks/point_view/export.php", $parameters),
            get_string('export_title_tab', 'block_point_view')
        )
    );

} catch (coding_exception $e) {

    echo 'Exception [coding_exception] (blocks/point_view/tabs.php) : ',
    $e->getMessage(), "\n";

} catch (moodle_exception $e) {

    echo 'Exception [moodle_exception] (blocks/point_view/tabs.php) : ',
    $e->getMessage(), "\n";

}

echo $OUTPUT->tabtree($tabs, $tab);
