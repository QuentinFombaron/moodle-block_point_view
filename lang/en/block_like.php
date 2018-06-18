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
 * Strings for component 'block_like'
 *
 * @package [TODO]
 * @copyright [TODO]
 * @author [TODO]
 * @license [TODO]
 */

$string['pluginname'] = "Like";

$string['contentinputlabel'] = "Block Content";

$string['defaulttext'] = "This plugin gives the possibility to react to activities</br></br>It is important to know that you are testing the Alpha version.</br>This plugin is developed by Quentin Fombaron (<a href='mailto:quentin.fombaron1@etu.univ-grenoble-alpes.fr?subject=\"Like\"%20Moodle%20plugin%20-%20Feedback'>CLICK HERE</a> to send me an email).</br>Thank you in advance for your returns and bug reports.</br></br><i>June 18<SUP>th</SUP> 2018 (version 0.6.0)</i>";

$string['config_header_activities'] = "On which activities are likes activated";
$string['no_activities_config_message'] = "There is no activities";

$string['enableall'] = "Enable all in ";
$string['disableall'] = "Disable all in ";
$string['go_to_save'] = "Jump to <b>Save</b>";
$string['close_field'] = "<b>Close</b> field";
$string['manage'] = "Manage ";


$manage = array('books', 'chats', 'files', 'forums', 'glossaries', 'pages', 'quizs', 'resources', 'urls', 'vpls', 'wikis');

foreach ($manage as $type) {
    $string['enable'.$type] = "Enable all ".ucfirst($type);
    $string['disable'.$type] = "Disable all ".ucfirst($type);
}

