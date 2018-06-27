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

$string['config_default_title'] = "Like";

$string['contentinputlabel'] = "Block Content";

$string['defaulttext'] = "This plugin gives the possibility to react to activities<br /><br />It is important to know that you are testing the Alpha version.<br />This plugin is developed by Quentin Fombaron (<a href='mailto:quentin.fombaron1@etu.univ-grenoble-alpes.fr?subject=\"Like\"%20Moodle%20plugin%20-%20Feedback'>CLICK HERE</a> to send me an email).<br />Thank you in advance for your returns and bug reports.<br /><br /><i>June 18<SUP>th</SUP> 2018 (version 0.6.0)</i><br /><br />";

$string['config_header_activities'] = "Likes/Difficulties configuration";
$string['config_header_images'] = "Image configuration";
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
    $string['howto_'.$type] = "management of ".substr(ucfirst($type), 0, -1)." activities";
    $string['howto_'.$type.'_help'] = "This buttons gives the possibility to enable or disable all  ".substr(ucfirst($type), 0, -1)." activities";
}

$string['enablelikes'] = "Enable likes";
$string['enabledifficulties'] = "Enable difficulty tracks";
$string['enablecustompix'] = "Use custom like emojis";
$string['likepix'] = "Emojis";
$string['likepixdesc'] = "<h5 style='color:red'>Important :</h5> Name the files <b><span style='font-family: Courier'>[emoji_name].png</span></b> for the emoji pictures, for instance: <span style='font-family: Courier'>easy.png</span>, <span style='font-family: Courier'>better.png</span> and <span style='font-family: Courier'>hard.png</span>. Also need groups of emojis <b><span style='font-family: Courier'>group_[emojis_initials].png</span></b>, for instance: <span style='font-family: Courier'>group_EB.png</span> for the group of Easy and Better reactions. Don't forget the file <b><span style='font-family: Courier'>group_.png</span></b>  for the none vote image. The recommended image size is 200x200.";
$string['enableimgperso'] = "Enable image personalisation";

$string['howto_text'] = "text visible in the block";
$string['howto_text_help'] = "This field gives the possibility to modify the text visible in the block";

$string['nonetrack'] = "None track";
$string['greentrack'] = "Green track";
$string['bluetrack'] = "Blue track";
$string['redtrack'] = "Red track";
$string['blacktrack'] = "Black track";

$string['menu'] = 'Menu';
$string['overview_title_tab'] = "Overview";
$string['export_title_tab'] = "Export";

$string['no_activities_message'] = 'No activities or resources are being monitored. Use configuration to set up monitoring';
$string['no_visible_activities_message'] = 'None of the monitored activities are currently visible';