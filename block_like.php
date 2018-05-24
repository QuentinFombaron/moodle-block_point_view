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
 * Block [TODO]
 *
 *
 * @package    block_like
 * @copyright  [TODO]
 * @author     [TODO]
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Displays the current user's profile information.
 *
 * @copyright  2010 Remote-Learner.net
 * @author     Olav Jordan <olav.jordan@remote-learner.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_like extends block_base {
    /**
     * block initializations
     */


    public function init() {
        $this->title   = get_string('pluginname', 'block_like');
    }

    public function get_content() {
        /* 1) Récupération données */
        /* $this->page->requires->js( new moodle_url($CFG->wwwroot . '/blocks/like/script/firstTest.js') ); */
        $this->page->requires->js(new moodle_url($CFG->wwwroot.'/blocks/like/script/script.js'));
        $this->page->requires->css(new moodle_url($CFG->wwwroot.'/blocks/like/style/style.css'));

        /* 2) Génération template */
    }

}
