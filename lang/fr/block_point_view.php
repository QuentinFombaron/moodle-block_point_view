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

$string['pluginname'] = "Point de vue";

/* Configuration */

$string['blacktrack'] = "Piste noire";
$string['bluetrack'] = "Piste bleue";
$string['blockdisabled'] = "<h3 style=\"color: red\">Le block est désactivé</h3>";
$string['close_field'] = "<b>Fermer</b> l'onglet";
$string['colmodule'] = "Module";
$string['colreactions'] = "Réactions";
$string['colsection'] = "Section";
$string['confirmation'] = 'Êtes-vous sûr de vouloir réinitialiser toutes les réactions du cours  <b>{$a}</b> ?';
$string['contentinputlabel'] = "Contenu du bloc";
$string['config_header_activities'] = "Configuration des Réactions et Pistes de difficulté";
$string['config_header_images'] = "Configuration des Emojis";
$string['config_header_reset'] = "Réinitialisation des réactions";
$string['defaulttextbetter'] = "Je m'améliore !";
$string['defaulttextcontent'] = "Ce plugin offre la possibilité de réagir et de donner des niveaux de difficulté aux activités.</br></br>Il est important de savoir que vous testez la version Beta.</br>Ce plugin est développé par Quentin Fombaron (<a href='mailto:quentin.fombaron1@etu.univ-grenoble-alpes.fr?subject=Plugin%20\"Réactions\"%20Moodle%20-%20Commentaire'>CLIQUER ICI</a> pour m'envoyer un mail.</br>Merci d'avance pour vos retours et rapports de bugs.</br></br><i>28 Novembre 2018 (version 1.0.0)</i><br /><br />Vous pouvez modifier ou effacer ce texte dans le menu de configuration du bloc<br /><br />";
$string['defaulttexteasy'] = "Fastoche !";
$string['defaulttexthard'] = "Dur dur...";
$string['disableall'] = 'Tout désactiver dans <b>{$a}</b>';
$string['disable_type'] = 'Désactiver tous/toutes les <b>{$a}</b>';
$string['emojidesc'] = " Description de l'emoji";
$string['enableall'] = 'Tout activer dans <b>{$a}</b>';
$string['enablecustompix'] = "Utiliser des emojis personnalisés";
$string['enabledifficulties'] = "Activer les <b>Pistes de difficulté</b>";
$string['enableimgperso'] = "Activer les emojis personnalisés";
$string['enablepoint_views'] = "Activer les <b>Réactions</b>";
$string['enable_type'] = 'Activer tous/toutes les <b>{$a}</b>';
$string['errorfilemanager'] = '<b>ERREUR</b> : Le nom du fichier <b>{$a}.png</b> n\'est pas conforme à la spécification indiquée ci-dessous';
$string['exportcsv'] = "Export CSV";
$string['exportods'] = "Export ODS";
$string['exportxls'] = "Export XLS";
$string['greentrack'] = "Piste verte";
$string['go_to_save'] = "<b>Enregistrer</b>";
$string['no'] = "Non";
$string['noneactivity'] = 'Aucune activité';
$string['nonetrack'] = "Aucune piste";
$string['no_activities_config_message'] = "Aucune activité";
$string['pixcurrently'] = "Utilisés actuellement";
$string['pixreset'] = "Réinitialiser les emojis";
$string['pixresettext'] = "&nbsp;<i style='font-size: 0.8em'>(La configuration sera enregistrée et vous serez redirigé vers la page du cours)</i>";
$string['point_viewpix'] = "Emojis";
$string['point_viewpixdesc'] = "<h5 style='color:red'>Important :</h5> Nommer les fichiers <b><span style='font-family: Courier'>[emoji_nom].png</span></b> pour les emojis, par exemple: <span style='font-family: Courier'>easy.png</span>, <span style='font-family: Courier'>better.png</span> et <span style='font-family: Courier'>hard.png</span>. Les groupes d'emojis sont aussi necessaires : <b><span style='font-family: Courier'>group_[emojis_initials].png</span></b>, par exemple: <span style='font-family: Courier'>group_EB.png</span> pour le groupe des réactions Easy et Better. Ne pas oublier le fichier <b><span style='font-family: Courier'>group_.png</span></b> pour l'image de 'Aucun vote'. La taille recommandée des images est de 200x200 pour les emojis et 400x200 pour les groupes.<br/><br/> 11 fichiers attendus : <span style='font-family: Courier'>easy.png</span>, <span style='font-family: Courier'>better.png</span>, <span style='font-family: Courier'>hard.png</span>, <span style='font-family: Courier'>group_.png</span>, <span style='font-family: Courier'>group_E.png</span>, <span style='font-family: Courier'>group_B.png</span>, <span style='font-family: Courier'>group_H.png</span>, <span style='font-family: Courier'>group_EB.png</span>, <span style='font-family: Courier'>group_EH.png</span>, <span style='font-family: Courier'>group_BH.png</span> et <span style='font-family: Courier'>group_EBH.png</span>";
$string['reactionreset'] = 'Réinitialiser les réactions du cours <b>{$a}</b>';
$string['redtrack'] = "Piste rouge";
$string['textbetter'] = "<b>Je m'améliore !</b> texte";
$string['texteasy'] = "<b>Fastoche !</b> texte";
$string['texthard'] = "<b>Dur dur...</b> text";
$string['yes'] = "Oui";

/* Menu */
$string['export_title_tab'] = "Export";
$string['menu'] = "Détails des réactions";
$string['overview_title_tab'] = "Vue d'ensemble";

/* Help text */
$string['howto_enable_difficulties_group'] = "l'activation des pistes de difficulté";
$string['howto_enable_difficulties_group_help'] = "Activer ou désactiver les pistes de difficulté dans le cours";
$string['howto_enable_point_views_checkbox'] = "l'activation des réactions";
$string['howto_enable_point_views_checkbox_help'] = "Activer ou désactiver les réactions dans le cours";
$string['howto_enable_pix'] = "l'activation d'emojis personnalisés";
$string['howto_enable_pix_help'] = "Activer ou désactiver la personnalisation des emojis. Vous devez fournir les images dans la zone de fichier avec des noms et extensions biens précis (voir ci dessous)";
$string['howto_manage_checkbox'] = "l'activation des réactions sur les activité du cours";
$string['howto_manage_checkbox_help'] = "Activer ou désactiver les réactions sur toutes les activités du cours";
$string['howto_pix_preview_group'] = "les emojis actuellement utilisés";
$string['howto_pix_preview_group_help'] = "Ce sont les emojis actuellement utilisés dans le cours. Vous pouvez rétablir ceux par défaut avec le bouton \"Réinitialiser les emojis\"";
$string['howto_reaction_reset'] = "la remise à zéro des réactions du cours";
$string['howto_reaction_reset_help'] = "Remettre à zéro toutes les réactions du cours, soyez sûr de ce que vous faites";
$string['howto_text'] = "le texte visible dans le block";
$string['howto_text_help'] = "Ce champs offre la possibilité de modifier le texte visible dans le block";
$string['howto_text_group'] = "le texte personnalisé des emojis";
$string['howto_text_group_help'] = "Personnaliser la description affichée au dessus des emojis";
$string['howto_type'] = 'la gestion des réactions sur les activités du même type';
$string['howto_type_help'] = 'Activer ou désactiver les réactions sur les activités du même type';

/* Privacy */
$string['privacy:metadata:block_point_view'] = "Point de Vue stocke le vote des utilisateurs sur chaque activité.";
$string['privacy:metadata:activity_votes_database:courseid'] = "ID du Cours";
$string['privacy:metadata:activity_votes_database:cmid'] = "ID de l'activité du cours";
$string['privacy:metadata:activity_votes_database:userid'] = "ID de l'utilisateur";
$string['privacy:metadata:activity_votes_database:vote'] = "Vote : 1 (Fastoche !), 2 (Je m'améliore !), 3 (Dur dur...)";
