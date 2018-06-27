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

    $PAGE->set_context($context);
    $PAGE->requires->css(new moodle_url($CFG->wwwroot . '/blocks/like/style/style.css'));
    $paramsamd = array();
    $PAGE->requires->js_call_amd('block_like/menu', 'init', $paramsamd);
    $title = get_string('menu', 'block_like');
    $PAGE->set_title($title);
    $PAGE->set_heading(get_string('config_default_title', 'block_like'));
    $PAGE->navbar->add($title);
    $PAGE->set_pagelayout('report');

    try {
        require_login($course, false);
        require_capability('block/like:overview', $blockcontext);
        confirm_sesskey();
    } catch (coding_exception $e) {
        echo 'Exception coding_exception (require_login() -> blocks/like/menu.php) : ', $e->getMessage(), "\n";
    } catch (require_login_exception $e) {
        echo 'Exception require_login_exception (require_login() -> blocks/like/menu.php) : ', $e->getMessage(), "\n";
    } catch (moodle_exception $e) {
        echo 'Exception moodle_exception (require_login() -> blocks/like/menu.php) : ', $e->getMessage(), "\n";
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading($title, 2);
    echo $OUTPUT->container_start('block_like');

    require("tabs.php");

    $table = new html_table();
    $table->head = array('Modules', 'Maximum grade', 'Grade to pass', '');
    $table->attributes['class'] = 'generaltable';
    $table->data = array();
    $table->rowclasses = array(
        'row_1', 'row_1_details',
        'row_2', 'row_2_details',
        'row_3', 'row_3_details',
        'row_4', 'row_4_details'
    );
    $md = range(1, 8);

    foreach ($md as $mds) {
        array_push($table->data, array('1', '2', '3', 'V'));
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

