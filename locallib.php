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
 * Reaction image
 *
 * @param int $contextid
 * @param string $filearea
 * @param string $react
 * @return string
 */
function block_point_view_pix_url($contextid, $filearea, $react) {

    return strval(moodle_url::make_pluginfile_url(
            $contextid,
            'block_point_view',
            $filearea,
            0,
            '/',
            $react)
            );

}

/**
 *
 * @param block_point_view $blockinstance
 * @param int $contextid
 * @param array|null $subset Requested subset of pix. If none specified, all will be returned.
 * @return string[]
 */
function block_point_view_get_current_pix($blockinstance, $subset = null) {
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

    $blockcontextid = $blockinstance->context->id;
    foreach ($pixfiles as $file) {
        if (isset($blockinstance->config->pixselect) && $blockinstance->config->pixselect == 'custom'
                && $fs->file_exists($blockcontextid, 'block_point_view', 'point_views_pix', 0, '/', $file . '.png')) {
            $pix[$file] = block_point_view_pix_url($blockcontextid, 'point_views_pix', $file);
        } else if ((!isset($blockinstance->config->pixselect) || $blockinstance->config->pixselect == 'admin')
                && get_config('block_point_view', 'enable_pix_admin')
                && $fs->file_exists(1, 'block_point_view', 'point_views_pix_admin', 0, '/', $file . '.png')) {
            $pix[$file] = block_point_view_pix_url(1, 'point_views_pix_admin', $file);
        } else {
            $pix[$file] = $CFG->wwwroot . '/blocks/point_view/pix/' . $file . '.png';
        }
    }

    return $pix;
}

/**
 *
 * @param block_point_view $blockinstance
 * @param string $reaction
 * @return string
 */
function block_point_view_get_reaction_text($blockinstance, $reaction) {
    return format_string((isset($blockinstance->config->{'pix_text_' . $reaction})) ?
            $blockinstance->config->{'pix_text_' . $reaction}
            : get_string('defaulttext' . $reaction, 'block_point_view'));
}

/**
 *
 * @param block_point_view $blockinstance
 * @param int $courseid
 * @return array
 */
function block_point_view_get_difficulty_levels($blockinstance, $courseid) {

    // If difficulty tracks are disabled, do not put any track.
    if (!isset($blockinstance->config->enable_difficultytracks)
            || !$blockinstance->config->enable_difficultytracks) {
        return array();
    }

    $cms = get_fast_modinfo($courseid, -1)->cms;

    $difficultylevels = array();

    // Loop through modules/courses.
    foreach ($cms as $cm) {
        if (isset($blockinstance->config->{'difficulty_' . $cm->id})) {
            $difficulty = $blockinstance->config->{'difficulty_' . $cm->id};
        } else {
            $difficulty = 0;
        }

        $difficultylevels[] = array(
                'id' => $cm->id,
                'difficultyLevel' => $difficulty
        );
    }

    return $difficultylevels;
}

function block_point_view_get_modules_with_reactions($blockinstance, $userid, $courseid) {
    global $DB;

    if (!isset($blockinstance->config->enable_point_views)
            || !$blockinstance->config->enable_point_views) {
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

    $params = array_merge($inparams, array('userid' => $userid, 'courseid' => $courseid));

    if (isset($blockinstance->config->show_other_users_reactions)
            && !$blockinstance->config->show_other_users_reactions
            && !has_capability('block/point_view:access_overview', $blockinstance->context)) {

        $sql = 'SELECT cm.id as cmid, COALESCE(bpv.vote, 0) as uservote
            FROM {course_modules} cm
            LEFT JOIN (SELECT cmid, vote FROM {block_point_view} WHERE userid = :userid) bpv ON bpv.cmid = cm.id
            WHERE cm.course = :courseid AND cm.id ' . $insql . '
            GROUP BY cm.id';

        $result = array_values($DB->get_records_sql($sql, $params));
        foreach ($result as &$cmrow) {
            $cmrow->totaleasy = $cmrow->uservote == 1 ? 1 : 0;
            $cmrow->totalbetter = $cmrow->uservote == 2 ? 1 : 0;
            $cmrow->totalhard = $cmrow->uservote == 3 ? 1 : 0;
        }
        return $result;
    } else {
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
}

function block_point_view_get_track_colors() {
    return array(
            '',
            get_config('block_point_view', 'green_track_color_admin'),
            get_config('block_point_view', 'blue_track_color_admin'),
            get_config('block_point_view', 'red_track_color_admin'),
            get_config('block_point_view', 'black_track_color_admin')
    );
}


/**
 * User data string for the overview table
 *
 * @param array $userids
 * @param stdClass $users
 * @return string
 */
function block_point_view_format_users($userids, $users) {
    global $OUTPUT;
    $string = '';

    foreach ($userids as $userid) {
        $user = $users[$userid];
        $string .= $OUTPUT->user_picture($user) . fullname( $user ) . '<br>';

    }

    return $string;
}