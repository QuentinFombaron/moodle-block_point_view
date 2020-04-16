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

/**
 * block_point_view Class
 *
 *
 * @package    block_point_view
 * @copyright  2020 Quentin Fombaron
 * @author     Quentin Fombaron <q.fombaron@outlook.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_point_view extends block_base {
    /**
     * Block initializations
     *
     * @throws coding_exception
     */
    public function init() {

        $this->title = get_string('pluginname', 'block_point_view');

    }

    /**
     * We have global config/settings data.
     * @return bool
     */
    public function has_config() {

        return true;

    }

    /**
     * Enable to add the block only in a course
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
            'site-index' => true,
            'course-view' => true,
        );
    }

    /**
     * Content of Point of View block
     *
     * @return Object
     * @throws dml_exception
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_content() {
        global $USER, $CFG, $COURSE;

        if (get_config('block_point_view', 'enable_point_views_admin')) {

            if ($this->content !== null) {

                return $this->content;

            }

            $this->content = new stdClass();

            if (has_capability('block/point_view:view', $this->context)) {

                $filteropt = new stdClass;
                if ($this->content_is_trusted()) {
                    $filteropt->noclean = true;
                }
                $this->content = new stdClass;
                $this->content->footer = '';
                if (isset($this->config->text)) {
                    $this->config->text = file_rewrite_pluginfile_urls(
                        $this->config->text,
                        'pluginfile.php',
                        $this->context->id,
                        'block_point_view',
                        'content',
                        null
                    );
                    $format = FORMAT_HTML;
                    if (isset($this->config->format)) {
                        $format = $this->config->format;
                    }
                    $this->content->text = format_text($this->config->text, $format, $filteropt);
                } else {
                    $this->content->text = '<span>'.get_string('defaulttextcontent', 'block_point_view').'</span>';
                }

                $parameters = [
                    'instanceid' => $this->instance->id,
                    'contextid' => $this->context->id,
                    'courseid' => $COURSE->id,
                    'sesskey' => sesskey(),
                    'enablepix' => (isset($this->config->enable_pix_checkbox)) ? $this->config->enable_pix_checkbox : 0
                ];

                $url = new moodle_url('/blocks/point_view/menu.php', $parameters);

                $this->content->text .= html_writer::link(
                    $url,
                    '<img src="' . $CFG->wwwroot .
                    '/blocks/point_view/pix/overview.png" id="menu_point_view_img" class="block_point_view"/>'
                );

                unset($filteropt);

            } else {
                $this->content->text = '';
            }

            if (!$this->page->user_is_editing()) {

                $envconf = array(
                    'userid' => $USER->id,
                    'courseid' => $COURSE->id,
                    'contextid' => $this->context->id
                );

                $paramsamd = array($envconf);

                $this->page->requires->js_call_amd('block_point_view/script_point_view', 'init', $paramsamd);
            }
        } else if (!get_config(
                'block_point_view',
                'enable_point_views_admin')
            && has_capability('block/point_view:view', $this->context)) {

            $this->content->text = get_string('blockdisabled', 'block_point_view');

        }

        return $this->content;
    }

    /**
     * Is content trusted
     *
     * @return bool
     *
     * @throws coding_exception
     */
    public function content_is_trusted() {
        global $SCRIPT;

        if (!$context = context::instance_by_id($this->instance->parentcontextid, IGNORE_MISSING)) {
            return false;
        }
        if ($context->contextlevel == CONTEXT_USER) {
            if ($SCRIPT === '/my/index.php') {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Serialize and store config data
     *
     * Save data from file manager when user is saving configuration.
     * Delete file storage if user disable custom emojis.
     *
     * @param mixed $data
     * @param mixed $nolongerused
     */
    public function instance_config_save($data, $nolongerused = false) {

        $config = clone($data);

        $config->text = file_save_draft_area_files(
            $data->text['itemid'],
            $this->context->id,
            'block_point_view',
            'content',
            0,
            array('subdirs' => true),
            $data->text['text']
        );

        $config->format = $data->text['format'];

        if ($config->enable_pix_checkbox) {
            $config->point_views_pix = file_save_draft_area_files(
                $data->point_views_pix,
                $this->context->id,
                'block_point_view',
                'point_views_pix',
                0
            );
        } else {
            $fs = get_file_storage();
            $fs->delete_area_files($this->context->id, 'block_point_view', 'point_views_pix');
        }

        parent::instance_config_save($config, $nolongerused);

    }

    /**
     * Delete file storage.
     *
     * @return bool
     */
    public function instance_delete() {

        $fs = get_file_storage();

        $fs->delete_area_files($this->context->id, 'block_point_view');

        return true;

    }

    /**
     * Copy any block-specific data when copying to a new block instance.
     *
     * @param int $fromid the id number of the block instance to copy from
     * @return boolean
     */
    public function instance_copy($fromid) {

        $fromcontext = context_block::instance($fromid);

        $fs = get_file_storage();

        if (!$fs->is_area_empty($fromcontext->id, 'block_point_view', 'content', 0, false)) {

            $draftitemid = 0;

            file_prepare_draft_area(
                $draftitemid,
                $fromcontext->id,
                'block_point_view',
                'content',
                0,
                array('subdirs' => true)
            );

            file_save_draft_area_files(
                $draftitemid,
                $this->context->id,
                'block_point_view',
                'content',
                0,
                array('subdirs' => true)
            );

        }

        if (!$fs->is_area_empty($fromcontext->id, 'block_point_view', 'point_views_pix', 0, false)) {

            $draftitemid = 0;

            file_prepare_draft_area(
                $draftitemid,
                $fromcontext->id,
                'block_point_view',
                'point_views_pix',
                0,
                array('subdirs' => true)
            );

            file_save_draft_area_files(
                $draftitemid,
                $this->context->id,
                'block_point_view',
                'point_views_pix',
                0,
                array('subdirs' => true)
            );

        }

        return true;

    }
}