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

$string['checkboxinputlabelm24'] = "Module 24";
$string['checkboxinputlabelm25'] = "Module 25";
$string['checkboxinputlabelm26'] = "Module 26";

$string['defaulttext'] = "This plugin gives the possibility to react to activities</br></br>It is important to know that you are testing the alpha version.</br>This plugin is developed by Quentin Fombaron (<a href='mailto:quentin.fombaron1@etu.univ-grenoble-alpes.fr?subject=Like%20plugin%20Moodle%20feedback'>CLICK HERE</a> to send me an email).</br>Thank you in advance for your returns and bug reports.</br></br><i>June 12<SUP>th</SUP> 2018 (version 0.5.0)</i>";

$string['config_header_activities'] = "Enable likes in activities";
$string['no_activities_config_message'] = "There are no activities or resources with activity completion set or no activities or resources have been selected. Set activity completion on activities and resources. Then configure this block.";

$string['enableall'] = "Enable all in ";
$string['disableall'] = "Disable all in ";

$manage = array('books', 'chats', 'files', 'forums', 'glossaries', 'pages', 'quizs', 'resources', 'urls', 'vpls', 'wikis');

foreach ($manage as $type) {
    $string['enable'.$type] = "Enable all ".ucfirst($type);
    $string['disable'.$type] = "Disable all ".ucfirst($type);
}

