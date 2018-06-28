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
 * Like block menu page
 *
 * @package    block_like
 * @copyright  [TODO]
 * @license    [TODO]
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/like/lib.php');



try {
    $id = required_param('instanceid', PARAM_INT);
    $courseid = required_param('courseid', PARAM_INT);
    $page = optional_param('page', 0, PARAM_INT);
    $perpage = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);
    $group = optional_param('group', 0, PARAM_INT);
    $tab = optional_param('tab', 'overview', PARAM_ALPHA);


    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

    $context = CONTEXT_COURSE::instance($courseid);

    $block = $DB->get_record('block_instances', array('id' => $id), '*', MUST_EXIST);

    $blockcontext = CONTEXT_BLOCK::instance($id);

    try {
        require_login();
        require_capability('block/like:overview', $blockcontext);
        confirm_sesskey();
    } catch (coding_exception $e) {
        echo 'Exception coding_exception (require_login() -> blocks/like/menu.php) : ', $e->getMessage(), "\n";
    } catch (require_login_exception $e) {
        echo 'Exception require_login_exception (require_login() -> blocks/like/menu.php) : ', $e->getMessage(), "\n";
    } catch (moodle_exception $e) {
        echo 'Exception moodle_exception (require_login() -> blocks/like/menu.php) : ', $e->getMessage(), "\n";
    }

    $PAGE->set_course($course);
    $PAGE->set_url(
        '/blocks/like/menu.php',
        array(
            'instanceid' => $id,
            'courseid' => $courseid,
            'page' => $page,
            'perpage' => $perpage,
            'group' => $group,
            'sesskey' => sesskey()
        )
    );

    $activities = block_like_get_activities($courseid, $config);

    $PAGE->set_context($context);
    $PAGE->requires->css(new moodle_url($CFG->wwwroot . '/blocks/like/style/style.css'));
    $paramsamd = array(array_column($activities, 'id'));
    $PAGE->requires->js_call_amd('block_like/menu', 'init', $paramsamd);
    $title = get_string('menu', 'block_like');
    $PAGE->set_title($title);
    $PAGE->set_heading(get_string('config_default_title', 'block_like'));
    $PAGE->navbar->add($title);
    $PAGE->set_pagelayout('report');

    echo $OUTPUT->header();
    echo $OUTPUT->heading($title, 2);
    echo $OUTPUT->container_start('block_like');

    require("tabs.php");

    $sql = 'SELECT Base.cmid, Base.type, 
            IFNULL(COUNT(Base.cmid), 0) AS total,
            IFNULL(TableTypeOne.TotalTypeOne, 0) AS typeone,
            IFNULL(TableTypeTwo.TotalTypeTwo, 0) AS typetwo,
            IFNULL(TableTypeThree.TotalTypethree, 0) AS typethree
          FROM {block_like} AS Base
            NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypeOne FROM {block_like}
              WHERE vote = 1 GROUP BY cmid) AS TableTypeOne
            NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypeTwo FROM {block_like}
              WHERE vote = 2 GROUP BY cmid) AS TableTypeTwo
            NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypethree FROM {block_like}
              WHERE vote = 3 GROUP BY cmid) AS TableTypeThree
          GROUP BY cmid;';

    $params = array('userid' => $USER->id);

    try {
        $result = $DB->get_records_sql($sql, $params);
    } catch (dml_exception $e) {
        echo 'Exception : ', $e->getMessage(), "\n";
    }

    $table = new html_table();
    $table->head = array('Cours', 'Modules', '', 'Réactions', '', '');
    $table->attributes['class'] = 'generaltable';
    $table->rowclasses = array();
    $table->data = array();

    foreach ($activities as $index => $activity) {
        if ($activity['type'] != 'label' && !is_null($result[($activity['id'])]->cmid)) {
            array_push($table->rowclasses,
                'row_module' . $activity['id'],
                'row_module' . $activity['id'] . '_details'
            );
            $attributes = ['class' => 'iconlarge activityicon'];
            $icon = $OUTPUT->pix_icon('icon', $activity['modulename'], $activity['type'], $attributes);
            array_push($table->data,
                array(
                    get_section_name($COURSE, $activity['section']),
                    $icon . format_string($activity['name']),
                    html_writer::empty_tag(
                        'img',
                        array('src' => $CFG->wwwroot . '/blocks/like/pix/easy.png', 'class' => 'overview_img')).' '.
                    100 * intval($result[($activity['id'])]->typeone) / intval($result[($activity['id'])]->total).'% ',
                    html_writer::empty_tag(
                        'img',
                        array('src' => $CFG->wwwroot . '/blocks/like/pix/better.png', 'class' => 'overview_img')).' '.
                    100 * intval($result[($activity['id'])]->typetwo) / intval($result[($activity['id'])]->total).'% ',
                    html_writer::empty_tag(
                        'img',
                        array('src' => $CFG->wwwroot . '/blocks/like/pix/hard.png', 'class' => 'overview_img')).' '.
                    100 * intval($result[($activity['id'])]->typethree) / intval($result[($activity['id'])]->total).'% ',
                    '+'
                ),
                array('', '', '', '', '', ''));
        }
    }

    echo html_writer::table($table);

    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();
} catch (coding_exception $e) {
    echo 'Exception coding_exception (blocks/like/menu.php) : ', $e->getMessage(), "\n";
    die();
} catch (dml_exception $e) {
    echo 'Exception dml_exception (blocks/like/menu.php) : ', $e->getMessage(), "\n";
    die();
} catch (moodle_exception $e) {
    echo 'Exception moodle_exception (blocks/like/menu.php) : ', $e->getMessage(), "\n";
    die();
}

