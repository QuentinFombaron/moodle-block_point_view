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
 * Like block export page
 *
 * @package    block_like
 * @copyright  [TODO]
 * @license    [TODO]
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/lib/csvlib.class.php');

try {
    require_login();
} catch (coding_exception $e) {
    echo 'Exception coding_exception (require_login() -> blocks/like/menu.php) : ', $e->getMessage(), "\n";
} catch (require_login_exception $e) {
    echo 'Exception require_login_exception (require_login() -> blocks/like/menu.php) : ', $e->getMessage(), "\n";
} catch (moodle_exception $e) {
    echo 'Exception moodle_exception (require_login() -> blocks/like/menu.php) : ', $e->getMessage(), "\n";
}

try {
    $id = required_param('instanceid', PARAM_INT);
    $contextid = required_param('contextid', PARAM_INT);
    $courseid = required_param('courseid', PARAM_INT);
    $enablepix = required_param('enablepix', PARAM_INT);
    $tab = optional_param('tab', 'export', PARAM_ALPHA);
    $format = optional_param('format', null, PARAM_ALPHA);

    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

    $context = CONTEXT_COURSE::instance($courseid);

    $block = $DB->get_record('block_instances', array('id' => $id), '*', MUST_EXIST);

    $config = unserialize(base64_decode($block->configdata));
    $blockcontext = CONTEXT_BLOCK::instance($id);

    $PAGE->set_course($course);
    $PAGE->set_url(
        '/blocks/like/export.php',
        array(
            'instanceid' => $id,
            'contextid' => $contextid,
            'courseid' => $courseid,
            'enablepix' => $enablepix,
            'sesskey' => sesskey()
        )
    );

    $PAGE->set_context($context);
    $PAGE->requires->css(new moodle_url($CFG->wwwroot . '/blocks/like/style/style.css'));
    $title = get_string('menu', 'block_like');
    $PAGE->set_title($title);
    $PAGE->set_heading(get_string('config_default_title', 'block_like'));
    $PAGE->navbar->add($title);
    $PAGE->set_pagelayout('report');

    echo $OUTPUT->header();
    echo $OUTPUT->heading($title, 2);
    echo $OUTPUT->container_start('block_like_export');

    require("tabs.php");

    echo html_writer::start_div('buttons');

    $parameters = ['contextid' => $contextid, 'courseid' => $courseid, 'instanceid' => $id, 'format' => 'csv'];
    $url = new moodle_url('/blocks/like/download.php', $parameters);
    $label = "Export CSV";
    $options = ['class' => 'exportCSVButton'];
    echo $OUTPUT->single_button($url, $label, 'post', $options);

    echo html_writer::tag('p', '&nbsp;');

    $parameters = ['contextid' => $contextid, 'courseid' => $courseid, 'instanceid' => $id, 'format' => 'ods'];
    $url = new moodle_url('/blocks/like/download.php', $parameters);
    $label = "Export ODS";
    $options = ['class' => 'exportODSButton'];
    echo $OUTPUT->single_button($url, $label, 'post', $options);

    echo html_writer::tag('p', '&nbsp;');

    $parameters = ['contextid' => $contextid, 'courseid' => $courseid, 'instanceid' => $id, 'format' => 'xls'];
    $url = new moodle_url('/blocks/like/download.php', $parameters);
    $label = "Export XLS";
    $options = ['class' => 'exportXLSButton'];
    echo $OUTPUT->single_button($url, $label, 'post', $options);

    echo html_writer::end_div();

    echo $OUTPUT->container_end();
    echo $OUTPUT->footer();

} catch (coding_exception $e) {
    echo 'Exception coding_exception (blocks/like/menu.php) : ', $e->getMessage(), "\n";
} catch (dml_exception $e) {
    echo 'Exception dml_exception (blocks/like/menu.php) : ', $e->getMessage(), "\n";
} catch (moodle_exception $e) {
    echo 'Exception moodle_exception (blocks/like/menu.php) : ', $e->getMessage(), "\n";
}