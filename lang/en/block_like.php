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

$string['pluginname'] = "Reactions and difficulty tracks";
$string['config_default_title'] = "Reactions and difficulty tracks";

$string['contentinputlabel'] = "Block Content";

$string['defaulttextcontent'] = "This plugin gives the possibility to react and to give difficulties levels to activities.<br /><br />It is important to know that you are testing the Beta version.<br />This plugin is developed by Quentin Fombaron (<a href='mailto:quentin.fombaron1@etu.univ-grenoble-alpes.fr?subject=\"Reaction\"%20Moodle%20plugin%20-%20Feedback'>CLICK HERE</a> to send me an email).<br />Thank you in advance for your returns and bug reports.<br /><br /><i>July 9<SUP>th</SUP> 2018 (version 1.0.0)</i><br /><br />";

$string['config_header_activities'] = "Reactions/Difficulties configuration";
$string['config_header_images'] = "Emojis configuration";
$string['config_header_reset'] = "Reset reactions";
$string['no_activities_config_message'] = "There is no activity";

$string['enableall'] = 'Enable all in <b>{$a}</b>';
$string['disableall'] = 'Disable all in <b>{$a}</b>';
$string['go_to_save'] = "Jump to <b>Save</b>";
$string['close_field'] = "<b>Close</b> field";

$string['enable_type'] = 'Enable all <b>{$a}</b>';
$string['disable_type'] = 'Disable all <b>{$a}</b>';
$string['howto_type'] = 'management of reactions in same type activities';
$string['howto_type_help'] = 'Enable or disable reactions on all activities of same type';

$string['enablelikes'] = "Enable Reactions";
$string['enabledifficulties'] = "Enable difficulty tracks";
$string['enablecustompix'] = "Use custom Reaction emojis";
$string['likepix'] = "Emojis";
$string['likepixdesc'] = "<h5 style='color:red'>Important :</h5> Name the files <b><span style='font-family: Courier'>[emoji_name].png</span></b> for the emoji pictures, for instance: <span style='font-family: Courier'>easy.png</span>, <span style='font-family: Courier'>better.png</span> and <span style='font-family: Courier'>hard.png</span>. Also need groups of emojis <b><span style='font-family: Courier'>group_[emojis_initials].png</span></b>, for instance: <span style='font-family: Courier'>group_EB.png</span> for the group of Easy and Better reactions. Don't forget the file <b><span style='font-family: Courier'>group_.png</span></b>  for the none vote image. The recommended image size is 200x200 for emojis and 400x200 for groups.<br/><br/> 11 fichiers attendus : <span style='font-family: Courier'>easy.png</span>, <span style='font-family: Courier'>better.png</span>, <span style='font-family: Courier'>hard.png</span>, <span style='font-family: Courier'>group_.png</span>, <span style='font-family: Courier'>group_E.png</span>, <span style='font-family: Courier'>group_B.png</span>, <span style='font-family: Courier'>group_H.png</span>, <span style='font-family: Courier'>group_EB.png</span>, <span style='font-family: Courier'>group_EH.png</span>, <span style='font-family: Courier'>group_BH.png</span> et <span style='font-family: Courier'>group_EBH.png</span>";
$string['enableimgperso'] = "Enable image personalisation";

$string['howto_text'] = "text visible in the block";
$string['howto_text_help'] = "This field gives the possibility to modify the text visible in the block";

$string['nonetrack'] = "None track";
$string['greentrack'] = "Green track";
$string['bluetrack'] = "Blue track";
$string['redtrack'] = "Red track";
$string['blacktrack'] = "Black track";

$string['menu'] = "Reactions details";
$string['overview_title_tab'] = "Overview";
$string['export_title_tab'] = "Export";

$string['texteasy'] = "<b>Easy !</b> text";
$string['textbetter'] = "<b>I'm getting better !</b> text";
$string['texthard'] = "<b>So hard...</b> text";
$string['defaulttexteasy'] = "Easy !";
$string['defaulttextbetter'] = "I'm getting better !";
$string['defaulttexthard'] = "So hard...";

$string['pixcurrently'] = "Currently used";
$string['pixreset'] = "Reset pictures";
$string['pixresettext'] = "&nbsp;<i style='font-size: 0.8em'>(Configuration will be saved and you will be redirected in course page)</i>";

$string['blockdisabled'] = "<h3 style=\"color: red\">The block is disabled</h3>";

$string['emojidesc'] = " Emoji description";

$string['noneactivity'] = 'No activity';

$string['colsection'] = "Section";
$string['colmodule'] = "Module";
$string['colreactions'] = "Reactions";

$string['errorfilemanager'] = '<b>ERROR</b> : The name of <b>{$a}.png</b> is not as indicated below';

$string['exportcsv'] = "CSV Export";
$string['exportods'] = "ODS Export";
$string['exportxls'] = "XLS Export";

$string['reactionreset'] = 'Reset <b>{$a}</b> course reactions';
$string['yes'] = "Yes";
$string['no'] = "No";
$string['confirmation'] = 'Are you sure you want to reset all reactions of <b>{$a}</b> course ?';