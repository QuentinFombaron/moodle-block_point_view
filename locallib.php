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


defined('MOODLE_INTERNAL') || die;

function block_point_view_print_header_with_tabs($currenttab, $instanceid, $contextid, $courseid) {
    global $PAGE, $CFG, $OUTPUT;

    $parameters = array (
            'instanceid' => $instanceid,
            'contextid'  => $contextid,
            'courseid'   => $courseid,
            'sesskey'    => sesskey(),
    );

    $tabs = array (
            'overview' => new moodle_url("{$CFG->wwwroot}/blocks/point_view/menu.php", $parameters),
            'export' => new moodle_url("{$CFG->wwwroot}/blocks/point_view/export.php", $parameters),
    );

    $PAGE->set_url($tabs[$currenttab]);

    $title = get_string('menu', 'block_point_view');
    $PAGE->set_title($title);
    $PAGE->set_heading(get_string('pluginname', 'block_point_view'));
    $PAGE->navbar->add($title);
    $PAGE->set_pagelayout('report');

    echo $OUTPUT->header();
    echo $OUTPUT->heading($title, 2);
    echo $OUTPUT->container_start('block_point_view');

    $tabtree = array();
    foreach ($tabs as $tab => $url) {
        $tabtree[] = new tabobject( $tab, $url, get_string($tab . '_title_tab', 'block_point_view') );
    }

    echo $OUTPUT->tabtree($tabtree, $currenttab);
}

function block_point_view_print_footer_of_tabs() {
    global $OUTPUT;

    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();
}

function block_point_view_check_instance($instanceorid, $context, $errorcontext = '', $errorurl = '') {
    global $DB;
    if (is_object($instanceorid)) {
        $blockrecord = $instanceorid;
    } else {
        $blockrecord = $DB->get_record('block_instances', array('id' => $instanceorid));
    }
    if ($blockrecord === false || $blockrecord->parentcontextid != $context->id || $blockrecord->blockname != 'point_view') {
        throw new moodle_exception('invalidblockinstance', 'error', $errorurl,
                $errorcontext . ' / ' . get_string('pluginname', 'block_point_view'));
    }
}

/**
 *
 * @param block_point_view $blockinstance
 * @param int $contextid
 * @param array|null $subset Requested subset of pix. If none specified, all will be returned.
 * @return string[]
 */
function block_point_view_get_current_pix($blockinstance, $contextid, $subset = null) {
    global $CFG;

    if ($subset !== null) {
        $pixfiles = $subset;
    } else {
        $pixfiles = array(
                'easy',
                'better',
                'hard',
                'group_',
                'group_E',
                'group_B',
                'group_H',
                'group_EB',
                'group_EH',
                'group_BH',
                'group_EBH'
        );
    }

    $pix = array();

    $fs = get_file_storage();

    require_once(__DIR__ . '/lib.php');

    foreach ($pixfiles as $file) {
        if (isset($blockinstance->config->pixselect) && $blockinstance->config->pixselect == 'custom'
                && $fs->file_exists($contextid, 'block_point_view', 'point_views_pix', 0, '/', $file . '.png')) {
            $pix[$file] = block_point_view_pix_url($contextid, 'point_views_pix', $file);
        } else if ((!isset($blockinstance->config->pixselect) || $blockinstance->config->pixselect == 'admin')
                && get_config('block_point_view', 'enable_pix_admin')
                && $fs->file_exists(1, 'block_point_view', 'point_views_pix_admin', 0, '/', $file . '.png')) {
            $pix[$file] = block_point_view_pix_url(1, 'point_views_pix', $file);
        } else {
            $pix[$file] = $CFG->wwwroot . '/blocks/point_view/pix/' . $file . '.png';
        }
    }

    return $pix;
}

function block_point_view_get_difficulty_levels($courseid) {
    global $DB;

    // Load the correct block instance.
    $coursecontext = context_course::instance($courseid);
    $blockrecord = $DB->get_record('block_instances', array('blockname' => 'point_view',
            'parentcontextid' => $coursecontext->id), '*', MUST_EXIST);
    $blockinstance = block_instance('point_view', $blockrecord);

    // If difficulty tracks are disabled, do not put any track.
    if (!isset($blockinstance->config->enable_difficulties_checkbox)
           || !$blockinstance->config->enable_difficulties_checkbox) {
        return array();
    }

    $modules = get_fast_modinfo($courseid, -1)->cms;

    $difficultylevels = array();

    // Loop through modules/courses.
    foreach ($modules as $module) {
        if (isset($blockinstance->config->{'difficulty_' . $module->id})) {
            $difficulty = $blockinstance->config->{'difficulty_' . $module->id};
        } else {
            $difficulty = 0;
        }

        $difficultylevels[] = array(
                'id' => $module->id,
                'difficultyLevel' => $difficulty
        );
    }

    return $difficultylevels;
}

function block_point_view_get_modules_with_reactions($userid, $courseid) {
    global $DB;

    $coursecontext = context_course::instance($courseid);
    $blockrecord = $DB->get_record('block_instances', array('blockname' => 'point_view',
            'parentcontextid' => $coursecontext->id), '*', MUST_EXIST);
    $blockinstance = block_instance('point_view', $blockrecord);

    if (!isset($blockinstance->config->enable_point_views_checkbox)
            || !$blockinstance->config->enable_point_views_checkbox) {
        return array();
    }

    $cms = get_fast_modinfo($courseid, $userid)->cms;
    $moduleswithreactions = array();
    foreach ($cms as $cm) {
        if (isset($blockinstance->config->{'moduleselectm' . $cm->id})
        && $blockinstance->config->{'moduleselectm' . $cm->id} > 0
        && $cm->uservisible) {
            $moduleswithreactions[] = $cm->id;
        }
    }

    if (empty($moduleswithreactions)) {
        return array();
    }

    list($insql, $inparams) = $DB->get_in_or_equal($moduleswithreactions, SQL_PARAMS_NAMED);

    $sql = 'SELECT cm.id as cmid,
        COALESCE(tableeasy.totaleasy, 0) as totaleasy,
        COALESCE(tablebetter.totalbetter, 0) as totalbetter,
        COALESCE(tablehard.totalhard, 0) as totalhard,
        COALESCE(tableuser.vote, 0) as uservote
        FROM {course_modules} cm
        LEFT JOIN (SELECT cmid, COUNT(vote) as totaleasy FROM {block_point_view}
                WHERE vote = 1 GROUP BY cmid) as tableeasy ON tableeasy.cmid = cm.id
        LEFT JOIN (SELECT cmid, COUNT(vote) as totalbetter FROM {block_point_view}
                WHERE vote = 2 GROUP BY cmid) as tablebetter ON tablebetter.cmid = cm.id
        LEFT JOIN (SELECT cmid, COUNT(vote) as totalhard FROM {block_point_view}
                WHERE vote = 3 GROUP BY cmid) as tablehard ON tablehard.cmid = cm.id
        LEFT JOIN (SELECT cmid, vote FROM {block_point_view}
                WHERE userid = :userid) AS tableuser ON tableuser.cmid = cm.id
        WHERE cm.id ' . $insql . '
        AND cm.course = :courseid
        GROUP BY cm.id';

    // TODO optimize this loading time.
    // Maybe add some indexes to the table.

    $params = array_merge($inparams, array('userid' => $userid, 'courseid' => $courseid));

    return array_values($DB->get_records_sql($sql, $params)); // Takes < 0.1s on small DB.
}

function block_point_view_get_pix($courseid, $contextid) {
    global $DB;

    $coursecontext = context_course::instance($courseid);
    $blockrecord = $DB->get_record('block_instances', array('blockname' => 'point_view',
            'parentcontextid' => $coursecontext->id), '*', MUST_EXIST);
    $blockinstance = block_instance('point_view', $blockrecord);

    $pixparam = block_point_view_get_current_pix($blockinstance, $contextid);

    $pixparam['easytxt'] = format_string((isset($blockinstance->config->pix_text_easy)) ?
    $blockinstance->config->pix_text_easy
    : get_string('defaulttexteasy', 'block_point_view' ));

    $pixparam['bettertxt'] = format_string((isset($blockinstance->config->pix_text_better)) ?
    $blockinstance->config->pix_text_better
    : get_string('defaulttextbetter', 'block_point_view' ));

    $pixparam['hardtxt'] = format_string((isset($blockinstance->config->pix_text_hard)) ?
    $blockinstance->config->pix_text_hard
    : get_string('defaulttexthard', 'block_point_view' ));

    return $pixparam;
}