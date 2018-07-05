<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2005 Martin Dougiamas  http://dougiamas.com             //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

$parameters = array(
        'instanceid' => $id,
        'contextid'  => $contextid,
        'courseid'   => $courseid,
        'enablepix'  => $enablepix,
        'sesskey'    => sesskey(),
    );

try {
    $tabs = array(
        new tabobject(
            'overview',
            new moodle_url("{$CFG->wwwroot}/blocks/like/menu.php", $parameters),
            get_string('overview_title_tab', 'block_like')
        ),
        new tabobject(
            'export',
            new moodle_url("{$CFG->wwwroot}/blocks/like/export.php", $parameters),
            get_string('export_title_tab', 'block_like')
        )
    );
} catch (coding_exception $e) {
    echo 'Exception coding_exception (blocks/like/tabs.php) : ', $e->getMessage(), "\n";
} catch (moodle_exception $e) {
    echo 'Exception moodle_exception (blocks/like/tabs.php) : ', $e->getMessage(), "\n";

}

echo $OUTPUT->tabtree($tabs, $tab);
