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
 * Plugin local library.
 *
 * @package    block_point_view
 * @copyright  2021 Astor Bizard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Check consistency of given block instance with given parent context. Throws a moodle_exception on failed check.
 *
 * @param object|int $instanceorid Block instance or id.
 * @param context $context Parent context of the block.
 * @param string $errorcontext Information about the context in which a failed check occured.
 * @param string $errorurl URL to redirect to on a failed check.
 * @throws moodle_exception
 */
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
 * Find and return pix url.
 *
 * @param int $contextid Context in which to search the file.
 * @param string $filearea File area.
 * @param string $file File name.
 * @return string|boolean url if found, false if not.
 */
function block_point_view_pix_url($contextid, $filearea, $file) {
    $fs = get_file_storage();
    if ($fs->file_exists($contextid, 'block_point_view', $filearea, 0, '/', $file . '.png')) {
        return moodle_url::make_pluginfile_url($contextid, 'block_point_view', $filearea, 0, '/', $file)->out();
    } else {
        return false;
    }

}

/**
 * Get current pix for a block instance.
 * This method checks existence of selected pix, replacing them with default ones if the first do not exist.
 *
 * @param block_point_view $blockinstance Block instance.
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

    foreach ($pixfiles as $file) {
        $pix[$file] = false;
        if (isset($blockinstance->config->pixselect) && $blockinstance->config->pixselect == 'custom') {
            $pix[$file] = block_point_view_pix_url($blockinstance->context->id, 'point_views_pix', $file);
        }
        if (!$pix[$file]
                && get_config('block_point_view', 'enable_pix_admin')
                && (!isset($blockinstance->config->pixselect) || $blockinstance->config->pixselect != 'default')) {
            $pix[$file] = block_point_view_pix_url(1, 'point_views_pix_admin', $file);
        }
        if (!$pix[$file]) {
            $pix[$file] = $CFG->wwwroot . '/blocks/point_view/pix/' . $file . '.png';
        }
    }

    return $pix;
}

/**
 * Get current text for a given reaction.
 *
 * @param block_point_view $blockinstance Block instance.
 * @param string $reaction Reaction name.
 * @return string Current reaction text.
 */
function block_point_view_get_reaction_text($blockinstance, $reaction) {
    return format_string((isset($blockinstance->config->{'pix_text_' . $reaction})) ?
            $blockinstance->config->{'pix_text_' . $reaction}
            : get_string('defaulttext' . $reaction, 'block_point_view'));
}

/**
 * Retrieve difficulty settings for modules within a course.
 *
 * @param block_point_view $blockinstance Block instance.
 * @param int $courseid Course id.
 * @return array Difficulty settings for every course module. One entry for each module, empty array if difficulty tracks disabled.
 */
function block_point_view_get_difficulty_levels($blockinstance, $courseid) {

    // If difficulty tracks are disabled, do not put any track.
    if (!isset($blockinstance->config->enable_difficultytracks)
            || !$blockinstance->config->enable_difficultytracks) {
        return array();
    }

    $cms = get_fast_modinfo($courseid, -1)->cms;

    $difficultylevels = array();

    // Loop through modules.
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

/**
 * Retrieve reactions settings for modules within a course.
 *
 * @param block_point_view $blockinstance Block instance.
 * @param int $userid User id, as returned array contains information about user's current vote.
 * @param int $courseid Course id.
 * @return array Reactions settings for every course module. One entry for each module with reactions enabled.
 */
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

        // TODO optimize this loading time, maybe add some indexes to the table.

        $params = array_merge($inparams, array('userid' => $userid, 'courseid' => $courseid));

        return array_values($DB->get_records_sql($sql, $params)); // Takes < 0.1s on small DB.
    }
}

/**
 * Get difficulty tracks colors, as set in plugin administration configuration.
 */
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

/**
 * Call all required javascript for edit_form.
 *
 * @param int $blockcontextid Context id of the block.
 */
function block_point_view_require_edit_form_javascript($blockcontextid) {
    global $COURSE, $PAGE;
    $envconf = array(
            'courseid' => $COURSE->id,
            'contextid' => $blockcontextid
    );

    $trackcolors = block_point_view_get_track_colors();

    $params = array($envconf, $trackcolors);

    $PAGE->requires->js_call_amd('block_point_view/script_config_point_view', 'init', $params);
    $PAGE->requires->string_for_js('resetreactionsconfirmation', 'block_point_view', format_string($COURSE->fullname));
    $PAGE->requires->strings_for_js(array('deleteemojiconfirmation', 'reactionsresetsuccessfully'), 'block_point_view');
    $PAGE->requires->strings_for_js(array('ok', 'info'), 'moodle');
}
