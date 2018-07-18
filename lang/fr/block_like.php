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

$string['pluginname'] = "Réactions et pistes de difficulté";
$string['config_default_title'] = "Réactions et pistes de difficulté";

$string['contentinputlabel'] = "Contenu du block";

$string['defaulttextcontent'] = "Ce plugin offre la possibilité de réagir et de donner des niveaux de difficulté aux activités.</br></br>Il est important de savoir que vous testez la version Beta.</br>Ce plugin est développé par Quentin Fombaron (<a href='mailto:quentin.fombaron1@etu.univ-grenoble-alpes.fr?subject=Plugin%20\"Réactions\"%20Moodle%20-%20Commentaire'>CLIQUER ICI</a> pour m'envoyer un mail.</br>Merci d'avance pour vos retours et rapports de bugs.</br></br><i>9 Juillet 2018 (version 1.0.0)</i><br /><br />";

$string['config_header_activities'] = "Configuration des Réactions et Pistes de difficulté";
$string['config_header_images'] = "Configuration des Emojis";
$string['config_header_reset'] = "Réinitialisation des réactions";
$string['no_activities_config_message'] = "Aucune activitié";

$string['enableall'] = 'Tout activer dans <b>{$a}</b>';
$string['disableall'] = 'Tout désactiver dans <b>{$a}</b>';
$string['go_to_save'] = "Aller à <b>Enregistrer</b>";
$string['close_field'] = "<b>Fermer</b> l'onglet";

$string['enable_type'] = 'Activer tous/toutes les <b>{$a}</b>';
$string['disable_type'] = 'Désactiver tous/toutes lesl <b>{$a}</b>';
$string['howto_type'] = 'Gestion des réactions sur les activités du même type';
$string['howto_type_help'] = 'Activer ou désactiver les réactions sur les activités du même type';

$string['enablelikes'] = "Activer les Réactions";
$string['enabledifficulties'] = "Activer les Pistes de difficulté";
$string['enablecustompix'] = "Utiliser des émojis personnalisés";
$string['likepix'] = "Emojis";
$string['likepixdesc'] = "<h5 style='color:red'>Important :</h5> Nommer les fichiers <b><span style='font-family: Courier'>[emoji_nom].png</span></b> pour les émojis, par exemple: <span style='font-family: Courier'>easy.png</span>, <span style='font-family: Courier'>better.png</span> et <span style='font-family: Courier'>hard.png</span>. Les groupes d'émojis sont aussi necessaires : <b><span style='font-family: Courier'>group_[emojis_initials].png</span></b>, par exemple: <span style='font-family: Courier'>group_EB.png</span> pour le groupe des réactions Easy et Better. Ne pas oublier le fichier <b><span style='font-family: Courier'>group_.png</span></b> pour l'image de 'Aucun vote'. La taille recommandée des images est de 200x200 pour les émojis et 400x200 pour les groupes.<br/><br/> 11 fichiers attendus : <span style='font-family: Courier'>easy.png</span>, <span style='font-family: Courier'>better.png</span>, <span style='font-family: Courier'>hard.png</span>, <span style='font-family: Courier'>group_.png</span>, <span style='font-family: Courier'>group_E.png</span>, <span style='font-family: Courier'>group_B.png</span>, <span style='font-family: Courier'>group_H.png</span>, <span style='font-family: Courier'>group_EB.png</span>, <span style='font-family: Courier'>group_EH.png</span>, <span style='font-family: Courier'>group_BH.png</span> et <span style='font-family: Courier'>group_EBH.png</span>";

$string['howto_text'] = "Texte visible dans le block";
$string['howto_text_help'] = "Ce champs offre la possibilité de modifier le texte sisible dans le block";

$string['nonetrack'] = "Aucune piste";
$string['greentrack'] = "Piste verte";
$string['bluetrack'] = "Piste bleue";
$string['redtrack'] = "Piste rouge";
$string['blacktrack'] = "Piste noire";

$string['menu'] = "Détails des réactions";
$string['overview_title_tab'] = "Vue d'ensemble";
$string['export_title_tab'] = "Export";

$string['texteasy'] = "<b>Fastoche !</b> texte";
$string['textbetter'] = "<b>Je m'améliore !</b> texte";
$string['texthard'] = "<b>Dur dur...</b> text";
$string['defaulttexteasy'] = "Fastoche !";
$string['defaulttextbetter'] = "Je m'améliore !";
$string['defaulttexthard'] = "Dur dur...";

$string['pixcurrently'] = "Utilisés actuellement";
$string['pixreset'] = "Réinitialiser les émojis";
$string['pixresettext'] = "&nbsp;<i style='font-size: 0.8em'>(La configuration sera enregistrée et vous serez redirigé vers la page du cours)</i>";

$string['blockdisabled'] = "<h3 style=\"color: red\">Le block est désactivé</h3>";

$string['emojidesc'] = " Description de l'émoji";

$string['noneactivity'] = 'Aucune activité';

$string['colsection'] = "Section";
$string['colmodule'] = "Module";
$string['colreactions'] = "Reactions";

$string['errorfilemanager'] = '<b>ERREUR</b> : Le nom du fichier <b>{$a}.png</b> n\'est pas conforme à la spécification indiqué ci-dessous';

$string['exportcsv'] = "Export CSV";
$string['exportods'] = "Export ODS";
$string['exportxls'] = "Export XLS";

$string['reactionreset'] = 'Réinitialiser les réactions du cours <b>{$a}</b>';
$string['yes'] = "Oui";
$string['no'] = "Non";
$string['confirmation'] = 'Êtes-vous sûr de réinitialiser toutes les réactions du cours  <b>{$a}</b> ?';