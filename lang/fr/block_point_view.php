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
 * Strings for component 'block_point_view', language 'fr'
 *
 * @package    block_point_view
 * @copyright  2020 Quentin Fombaron, 2021 Astor Bizard
 * @author     Quentin Fombaron <q.fombaron@outlook.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Point de vue';

$string['adminpix'] = 'Défaut (site) :';
$string['blacktrack'] = 'Piste noire';
$string['bluetrack'] = 'Piste bleue';
$string['blockdisabled'] = '<h3 class="text-danger">Le block est désactivé</h3>';
$string['contentinputlabel'] = 'Contenu du bloc';
$string['contentinputlabel_help'] = 'Ce champ permet de modifier le texte visible dans le bloc. S\'il est vide, le bloc sera invisible pour les étudiants.';
$string['customemoji'] = 'Emoji personnalisés';
$string['customemoji_help'] = 'Des emoji personnalisés à utiliser comme réactions peuvent être définis ici.<h5 class="text-danger">Important :</h5>Nommer les fichiers <b><code>[nom_emoji].png</code></b> pour les emoji : <code>easy.png</code>, <code>better.png</code> et <code>hard.png</code>. Les groupes d\'emoji sont aussi nécessaires : <b><code>group_[initiales_des_emoji].png</code></b>, par exemple : <code>group_EB.png</code> pour le groupe des réactions Easy et Better. Ne pas oublier le fichier <b><code>group_.png</code></b> pour l\'image de \'Aucun vote\'. La taille recommandée des images est de 200x200 pour les emoji et 400x200 pour les groupes.<br/><br/> 11 fichiers attendus : <code>easy.png</code>, <code>better.png</code>, <code>hard.png</code>, <code>group_.png</code>, <code>group_E.png</code>, <code>group_B.png</code>, <code>group_H.png</code>, <code>group_EB.png</code>, <code>group_EH.png</code>, <code>group_BH.png</code> et <code>group_EBH.png</code>';
$string['custompix'] = 'Personnalisé :';
$string['header_activities'] = 'Configuration des Réactions et Pistes de difficulté';
$string['header_images'] = 'Configuration des Emoji';
$string['defaultpix'] = 'Défaut :';
$string['defaulttextbetter'] = 'Je m\'améliore !';
$string['defaulttextcontent'] = 'Ce plugin offre la possibilité de réagir et de donner des niveaux de difficulté aux activités.</br></br>Ce plugin est développé par <a href="mailto:q.fombaron@outlook.fr?subject=%22Point%20of%20view%22%20Moodle%20plugin%20-%20Feedback">Quentin Fombaron</a>.<br/><br/>Vous pouvez modifier ou effacer ce texte dans le menu de configuration du bloc.<br/><br/>';
$string['defaulttexteasy'] = 'Fastoche !';
$string['defaulttexthard'] = 'Dur dur...';
$string['deleteemojiconfirmation'] = 'Êtes-vous sûr de vouloir supprimer les emoji personnalisés pour ce bloc ?
Cela supprimera les emoji actuellement enregistrés et les fichiers dans la zone ci-dessous. Cette action est irréversible.';
$string['delete_custom_pix'] = 'Supprimer les emoji personnalisés';
$string['disableall'] = 'Tout désactiver dans <b>{$a}</b>';
$string['disable_type'] = 'Désactiver tou.te.s les <b>{$a}</b>';
$string['emojidesc'] = 'Description de l\'emoji';
$string['emojidesc_help'] = 'Texte personnalisé qui sera affiché au survol de la réaction';
$string['emojitouse'] = 'Emoji à utiliser';
$string['emojitouse_help'] = 'Choisir les emoji à utiliser comme réactions dans ce cours.<br>Vous pouvez ajouter vos propres emoji personnalisés en sélectionnant "Personnalisé".';
$string['enableall'] = 'Tout activer dans <b>{$a}</b>';
$string['enablecustompix'] = 'Utiliser des emoji personnalisés';
$string['enabledifficulties'] = 'Activer les <b>Pistes de difficulté</b>';
$string['enableforfuturemodules'] = 'Activer pour les futurs modules';
$string['enableforfuturemodules_help'] = 'Activer automatiquement les réactions pour les nouveaux modules créés dans ce cours.';
$string['enablepoint_views'] = 'Activer les <b>Réactions</b>';
$string['enable_disable_section'] = 'Tout activer/désactiver dans cette section';
$string['enable_disable_section_help'] = 'Activer ou désactiver les réactions pour tous les modules dans cette section.';
$string['enable_disable_type'] = 'Tout activer/désactiver pour ce type de module';
$string['enable_disable_type_help'] = 'Activer ou désactiver les réactions pour tous les modules de ce type dans ce cours.';
$string['enable_type'] = 'Activer tou.te.s les <b>{$a}</b>';
$string['errorfilemanager'] = '<b>ERREUR</b> : Le nom du fichier <b>{$a}.png</b> n\'est pas conforme.';
$string['errorfilemanagerempty'] = 'Veuillez fournir au moins un fichier.';
$string['greentrack'] = 'Piste verte';
$string['module'] = 'Module';
$string['noactivity'] = 'Aucune activité';
$string['nonetrack'] = 'Aucune piste';
$string['reactions'] = 'Réactions';
$string['reactionsdetails'] = 'Détail des réactions';
$string['reactionsresetsuccessfully'] = 'Les réactions ont été correctement réinitialisées.';
$string['reactionsunavailable'] = 'Vous ne pouvez pas ajouter ou retirer de réaction pour ce module.';
$string['redtrack'] = 'Piste rouge';
$string['resetcoursereactions'] = 'Réinitialiser les réactions du cours <b>{$a}</b>';
$string['resetreactions'] = 'Réinitialiser les réactions';
$string['resetreactions_help'] = 'Réinitialiser (supprimer) toutes les réactions des utilisateurs dans ce cours.';
$string['resetreactionsconfirmation'] = 'Êtes-vous sûr de vouloir réinitialiser (supprimer) toutes les réactions des utilisateurs dans <b>{$a}</b>? Cette action est irréversible.';
$string['showotherreactions'] = 'Montrer les réactions des autres utilisateurs';
$string['showotherreactions_help'] = 'Permettre aux étudiants de voir le nombre de réactions de chaque type par les autres utilisateurs pour chaque module.';
$string['totalreactions'] = 'Réactions totales : {$a}';

$string['point_view:access_overview'] = 'Voir le détail des réactions';
$string['point_view:addinstance'] = 'Ajouter une instance du bloc Point de Vue';
$string['point_view:myaddinstance'] = 'Ajouter une instance du bloc Point de Vue sur le tableau de bord';

$string['privacy:metadata:block_point_view'] = 'Point de Vue stocke le vote des utilisateurs sur chaque activité.';
$string['privacy:metadata:activity_votes_database:courseid'] = 'ID du Cours';
$string['privacy:metadata:activity_votes_database:cmid'] = 'ID de l\'activité du cours';
$string['privacy:metadata:activity_votes_database:userid'] = 'ID de l\'utilisateur';
$string['privacy:metadata:activity_votes_database:vote'] = 'Vote : 1 (Fastoche !), 2 (Je m\'améliore !), 3 (Dur dur...)';
