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
 * @copyright  2020 Quentin Fombaron
 * @author     Quentin Fombaron <q.fombaron@outlook.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
global $CFG, $DB, $PAGE, $OUTPUT;
require_once($CFG->dirroot . '/lib/csvlib.class.php');
require_once(__DIR__ . '/locallib.php');

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

        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

        $PAGE->set_course($course);
        $PAGE->set_context($context);

        block_point_view_check_instance($id, $context, format_string($course->fullname));

        block_point_view_print_header_with_tabs('export', $id, $contextid, $courseid);

        echo html_writer::start_div('w-100 text-center');

        echo html_writer::start_div('d-inline-flex');

        $formats = array('csv', 'ods', 'xls');
        $params = array('contextid' => $contextid, 'courseid' => $courseid, 'instanceid' => $id);

        foreach ($formats as $format) {
            $params['format'] = $format;

            $url = new moodle_url('/blocks/point_view/download.php', $params);

            $label = get_string('export' . $format, 'block_point_view');

            echo $OUTPUT->single_button($url, $label, 'post', array('class' => 'mx-2'));
        }

        echo html_writer::end_div();

        echo html_writer::end_div();

        block_point_view_print_footer_of_tabs();
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