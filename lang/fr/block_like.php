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

$string['contentinputlabel'] = "Contenu du block";

$string['defaulttext'] = "Ce plugin offre la possibilité de réagir aux activités.</br></br>Il est important de savoir que vous testez la version Alpha.</br>Ce plugin est développé par Quentin Fombaron (<a href='mailto:quentin.fombaron1@etu.univ-grenoble-alpes.fr?subject=Plugin%20\"Like\"%20Moodle%20-%20Commentaire'>CLIQUER ICI</a> pour m'envoyer un mail.</br>Merci d'avance pour vos retours et rapports de bugs.</br></br><i>18 Juin 2018 (version 0.6.0)</i>";

$string['config_header_activities'] = "Sur quelles activités les likes sont activés";
$string['no_activities_config_message'] = "Aucune activité";

$string['enableall'] = "Tout activer dans ";
$string['disableall'] = "Tout désactiver dans ";
$string['go_to_save'] = "Aller à <b>Sauvegarder</b>";
$string['close_field'] = "<b>Fermer</b> l'onglet";
$string['manage'] = "Gérer les ";


$manage = array('books', 'chats', 'files', 'forums', 'glossaries', 'pages', 'quizs', 'resources', 'urls', 'vpls', 'wikis');

foreach ($manage as $type) {
    $string['enable'.$type] = "Activer tous les ".ucfirst($type);
    $string['disable'.$type] = "Désactiver tous les ".ucfirst($type);
}

