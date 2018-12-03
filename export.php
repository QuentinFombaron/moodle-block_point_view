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
 * Point of View block
 *
 *
 * @package    block_point_view
 * @copyright  2018 Quentin Fombaron
 * @author     Quentin Fombaron <quentin.fombaron1@etu.univ-grenoble-alpes.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/lib/csvlib.class.php');

try {
    require_login();
} catch (coding_exception $e) {
    echo 'Exception [coding_exception] (blocks/point_view/export.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (require_login_exception $e) {
    echo 'Exception [require_login_exception] (blocks/point_view/export.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (moodle_exception $e) {
    echo 'Exception [moodle_exception] (blocks/point_view/export.php -> require_login()) : ',
    $e->getMessage(), "\n";
}

confirm_sesskey();

try {

    $courseid = required_param('courseid', PARAM_INT);

    $context = CONTEXT_COURSE::instance($courseid);

    if (has_capability('block/point_view:access_menu', $context)) {

        $id = required_param('instanceid', PARAM_INT);

        $contextid = required_param('contextid', PARAM_INT);

        $enablepix = required_param('enablepix', PARAM_INT);

        $tab = optional_param('tab', 'export', PARAM_ALPHA);

        $format = optional_param('format', null, PARAM_ALPHA);

        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        $block = $DB->get_record('block_instances', array('id' => $id), '*', MUST_EXIST);

        $config = unserialize(base64_decode($block->configdata));

        $blockcontext = CONTEXT_BLOCK::instance($id);

        $PAGE->set_course($course);

        $PAGE->set_url(
            '/blocks/point_view/export.php',
            array(
                'instanceid' => $id,
                'contextid' => $contextid,
                'courseid' => $courseid,
                'enablepix' => $enablepix,
                'sesskey' => sesskey()
            )
        );

        $PAGE->set_context($context);

        $PAGE->requires->css(new moodle_url($CFG->wwwroot . '/blocks/point_view/styles.css'));

        $title = get_string('menu', 'block_point_view');

        $PAGE->set_title($title);

        $PAGE->set_heading(get_string('pluginname', 'block_point_view'));

        $PAGE->navbar->add($title);

        $PAGE->set_pagelayout('report');

        echo $OUTPUT->header();

        echo $OUTPUT->heading($title, 2);

        echo $OUTPUT->container_start('block_point_view');

        require("tabs.php");

        echo html_writer::start_div('export_buttons');

        /* CSV Export */

        $parameters = ['contextid' => $contextid, 'courseid' => $courseid, 'instanceid' => $id, 'format' => 'csv'];

        $url = new moodle_url('/blocks/point_view/download.php', $parameters);

        $label = get_string('exportcsv', 'block_point_view');

        $options = ['class' => 'exportCSVButton'];

        echo $OUTPUT->single_button($url, $label, 'post', $options);

        echo html_writer::tag('p', '&nbsp;');

        /* ODS Export */

        $parameters = ['contextid' => $contextid, 'courseid' => $courseid, 'instanceid' => $id, 'format' => 'ods'];

        $url = new moodle_url('/blocks/point_view/download.php', $parameters);

        $label = get_string('exportods', 'block_point_view');

        $options = ['class' => 'exportODSButton'];

        echo $OUTPUT->single_button($url, $label, 'post', $options);

        echo html_writer::tag('p', '&nbsp;');

        /* XLS Export */

        $parameters = ['contextid' => $contextid, 'courseid' => $courseid, 'instanceid' => $id, 'format' => 'xls'];

        $url = new moodle_url('/blocks/point_view/download.php', $parameters);

        $label = get_string('exportxls', 'block_point_view');

        $options = ['class' => 'exportXLSButton'];

        echo $OUTPUT->single_button($url, $label, 'post', $options);

        echo html_writer::end_div();

        echo $OUTPUT->container_end();

        echo $OUTPUT->footer();

    }

} catch (coding_exception $e) {

    echo 'Exception [coding_exception] (blocks/point_view/export.php) : ',
    $e->getMessage(), "\n";

} catch (dml_exception $e) {

    echo 'Exception [dml_exception] (blocks/point_view/export.php) : ',
    $e->getMessage(), "\n";

} catch (moodle_exception $e) {

    echo 'Exception [moodle_exception] (blocks/point_view/export.php) : ',
    $e->getMessage(), "\n";

}