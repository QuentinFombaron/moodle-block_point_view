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
 * Point of View block
 *
 *
 * @package    block_point_view
 * @copyright  2018 Quentin Fombaron
 * @author     Quentin Fombaron <quentin.fombaron1@etu.univ-grenoble-alpes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* Name */

$string['pluginname'] = "Point of view";

/* Configuration */

$string['blacktrack'] = "Black track";
$string['bluetrack'] = "Blue track";
$string['blockdisabled'] = "<h3 style=\"color: red\">The block is disabled</h3>";
$string['close_field'] = "<b>Close</b> field";
$string['colmodule'] = "Module";
$string['colreactions'] = "Reactions";
$string['colsection'] = "Section";
$string['confirmation'] = 'Are you sure you want to reset all reactions of <b>{$a}</b> course ?';
$string['contentinputlabel'] = "Block Content";
$string['config_header_activities'] = "Configuration of Reaction and Difficulty tracks";
$string['config_header_images'] = "Emojis configuration";
$string['config_header_reset'] = "Reset reactions";
$string['defaulttextbetter'] = "I'm getting better !";
$string['defaulttextcontent'] = "This plugin gives the possibility to react and to give difficulties levels to activities.<br /><br />It is important to know that you are testing the Beta version.<br />This plugin is developed by Quentin Fombaron (<a href='mailto:quentin.fombaron1@etu.univ-grenoble-alpes.fr?subject=\"Reaction\"%20Moodle%20plugin%20-%20Feedback'>CLICK HERE</a> to send me an email).<br />Thank you in advance for your returns and bug reports.<br /><br /><i>November 28<SUP>th</SUP> 2018 (version 1.0.0)</i><br /><br />You can edit or delete this text in the block configuration menu<br /><br />";
$string['defaulttexteasy'] = "Easy !";
$string['defaulttexthard'] = "So hard...";
$string['disableall'] = 'Disable all in <b>{$a}</b>';
$string['disable_type'] = 'Disable all <b>{$a}</b>';
$string['emojidesc'] = " Emoji description";
$string['enableall'] = 'Enable all in <b>{$a}</b>';
$string['enablecustompix'] = "Use custom Reaction emojis";
$string['enabledifficulties'] = "Enable <b>Difficulty tracks</b>";
$string['enableimgperso'] = "Enable emoji customisation";
$string['enablepoint_views'] = "Enable <b>Reactions</b>";
$string['enable_type'] = 'Enable all <b>{$a}</b>';
$string['errorfilemanager'] = '<b>ERROR</b> : The name of <b>{$a}.png</b> is not as indicated below';
$string['exportcsv'] = "CSV Export";
$string['exportods'] = "ODS Export";
$string['exportxls'] = "XLS Export";
$string['greentrack'] = "Green track";
$string['go_to_save'] = "<b>Save</b> changes";
$string['no'] = "No";
$string['noneactivity'] = 'No activity';
$string['nonetrack'] = "None track";
$string['no_activities_config_message'] = "No activity";
$string['pixcurrently'] = "Currently used";
$string['pixreset'] = "Reset emojis";
$string['pixresettext'] = "&nbsp;<i style='font-size: 0.8em'>(Configuration will be saved and you will be redirected in course page)</i>";
$string['point_viewpix'] = "Emojis";
$string['point_viewpixdesc'] = "<h5 style='color:red'>Important :</h5> Name the files <b><span style='font-family: Courier'>[emoji_name].png</span></b> for the emoji pictures, for instance: <span style='font-family: Courier'>easy.png</span>, <span style='font-family: Courier'>better.png</span> and <span style='font-family: Courier'>hard.png</span>. Also need groups of emojis <b><span style='font-family: Courier'>group_[emojis_initials].png</span></b>, for instance: <span style='font-family: Courier'>group_EB.png</span> for the group of Easy and Better reactions. Don't forget the file <b><span style='font-family: Courier'>group_.png</span></b>  for the none vote image. The recommended image size is 200x200 for emojis and 400x200 for groups.<br/><br/> 11 files expected : <span style='font-family: Courier'>easy.png</span>, <span style='font-family: Courier'>better.png</span>, <span style='font-family: Courier'>hard.png</span>, <span style='font-family: Courier'>group_.png</span>, <span style='font-family: Courier'>group_E.png</span>, <span style='font-family: Courier'>group_B.png</span>, <span style='font-family: Courier'>group_H.png</span>, <span style='font-family: Courier'>group_EB.png</span>, <span style='font-family: Courier'>group_EH.png</span>, <span style='font-family: Courier'>group_BH.png</span> et <span style='font-family: Courier'>group_EBH.png</span>";
$string['reactionreset'] = 'Reset <b>{$a}</b> course reactions';
$string['redtrack'] = "Red track";
$string['textbetter'] = "<b>I'm getting better !</b> text";
$string['texteasy'] = "<b>Easy !</b> text";
$string['texthard'] = "<b>So hard...</b> text";
$string['yes'] = "Yes";

/* Menu */

$string['export_title_tab'] = "Export";
$string['menu'] = "Reactions details";
$string['overview_title_tab'] = "Overview";

/* Help text */
$string['howto_enable_difficulties_group'] = "activation of the difficulty tracks";
$string['howto_enable_difficulties_group_help'] = "Enable or disable difficulty tracks in the course";
$string['howto_enable_point_views_checkbox'] = "activation of the reaction";
$string['howto_enable_point_views_checkbox_help'] = "Enable or disable reactions in the course";
$string['howto_enable_pix'] = "activation of custom emojis";
$string['howto_enable_pix_help'] = "Enable or disable custom emojis. You need to put the images in the file manager with specific names and extension (see below)";
$string['howto_manage_checkbox'] = "management of reactions on course activities";
$string['howto_manage_checkbox_help'] = "Enable or disable reactions on all activities of the course";
$string['howto_pix_preview_group'] = "currently used emojis";
$string['howto_pix_preview_group_help'] = "This is the emojis you are currently using in the course. You can reset them to the default ones with the \"<b>Reset emojis</b>\" button";
$string['howto_reaction_reset'] = "reset all reaction in the course";
$string['howto_reaction_reset_help'] = "Reset all reaction in the course, you need to be sure what you are doing";
$string['howto_text'] = "text visible in the block";
$string['howto_text_help'] = "This field allows to modify the text visible in the block";
$string['howto_text_group'] = "custom text description of emoji";
$string['howto_text_group_help'] = "Custom the text description displayed on top of the emoji";
$string['howto_type'] = "management of reactions on same type activities";
$string['howto_type_help'] = "Enable or disable reactions on all activities of same type";

/* Privacy */
$string['privacy:metadata:block_point_view'] = "The Point of View block stores the user's votes on each activities.";
$string['privacy:metadata:activity_votes_database:courseid'] = "Course ID";
$string['privacy:metadata:activity_votes_database:cmid'] = "Course activity ID";
$string['privacy:metadata:activity_votes_database:userid'] = "User ID";
$string['privacy:metadata:activity_votes_database:vote'] = "Vote : 1 (Easy !), 2 (I'm getting better !), 3 (So hard...)";
