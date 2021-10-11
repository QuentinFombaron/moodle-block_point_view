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
 * Point of View block configuration form definition.
 *
 * @package    block_point_view
 * @copyright  2020 Quentin Fombaron, 2021 Astor Bizard
 * @author     Quentin Fombaron <q.fombaron@outlook.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/locallib.php');

/**
 * block_point_view_edit_form Class
 *
 *
 * @package    block_point_view
 * @copyright  2020 Quentin Fombaron
 * @author     Quentin Fombaron <q.fombaron@outlook.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_point_view_edit_form extends block_edit_form {

    /**
     * Configuration page
     *
     * @param MoodleQuickForm $mform
     */
    protected function specific_definition($mform) {

        global $COURSE, $OUTPUT;

        if (get_config('block_point_view', 'enable_point_views_admin')) {

            // Add block_point_view class to form element for styling,
            // as it is not done for the body element on block edition page.
            $mform->updateAttributes(array('class' => $mform->getAttribute('class') . ' block_point_view'));

            $mform->addElement('header', 'general_header', get_string('blocksettings', 'block'));

            // Block content.

            $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true, 'context' => $this->block->context);
            $mform->addElement(
                'editor',
                'config_text',
                get_string('contentinputlabel', 'block_point_view'),
                null,
                $editoroptions
            );
            $mform->setType('config_text', PARAM_RAW);

            $mform->addHelpButton('config_text', 'contentinputlabel', 'block_point_view');

            // Reaction activation.
            $mform->addElement('selectyesno', 'config_enable_point_views',
                    get_string('enablepoint_views', 'block_point_view'));

            $this->add_checkbox_with_help($mform, 'config_enable_point_views_new_modules', 'enableforfuturemodules', 1);
            $mform->disabledIf('config_enable_point_views_new_modules', 'config_enable_point_views', 'eq', 0);

            $this->add_checkbox_with_help($mform, 'config_show_other_users_reactions', 'showotherreactions', 1);
            $mform->disabledIf('config_show_other_users_reactions', 'config_enable_point_views', 'eq', 0);

            // Difficulty tracks activation.
            $mform->addElement('selectyesno', 'config_enable_difficultytracks',
                    get_string('enabledifficulties', 'block_point_view'));

            // Reactions and difficulty tracks configuration by activity.

            $mform->addElement('header', 'activities_header', get_string('header_activities', 'block_point_view'));

            $modinfo = get_fast_modinfo($COURSE->id, -1);
            $cms = $modinfo->cms;
            $modtypes = array_keys($modinfo->instances);

            if (empty($cms)) {
                $this->add_warning_message($mform, get_string('noactivity', 'block_point_view'));
            }

            // Enable/Disable by activity module type.
            foreach ($modtypes as $type) {
                $this->add_enable_disable_buttons($mform, '',
                        $type,
                        'enable_type', 'disable_type',
                        get_string('modulenameplural', $type),
                        'enable_disable_type',
                        'data-type="' . $type . '"',
                        array('class' => 'reactions'));
            }

            $oldsection = '';
            $sectionid = 0;
            // Enable/Disable by activity or section.
            foreach ($cms as $cm) {

                if ($cm->sectionnum != $oldsection) {

                    $sectionid++;
                    $sectionname = get_section_name($COURSE, $cm->sectionnum);

                    $this->add_enable_disable_buttons($mform, '<h4>' . $sectionname . '</h4>',
                            'sec' . $sectionid,
                            'enableall', 'disableall',
                            $sectionname,
                            'enable_disable_section',
                            'data-section="sec' . $sectionid . '"',
                            array('class' => 'section-header'));

                    $oldsection = $cm->sectionnum;
                }

                $icon = $OUTPUT->pix_icon('icon', $cm->get_module_type_name(), $cm->modname,
                        array('class' => 'iconlarge activityicon'));

                $this->add_activity_config($mform, $cm->id, $sectionid, $cm->modname, $icon . $cm->get_formatted_name());
            }

            // Emoji configuration.

            $this->add_emoji_selection($mform);

            // Reaction reinitialisation.
            $mform->addElement('header', 'reset_header', get_string('resetreactions', 'block_point_view'));

            $mform->addElement('static', 'reaction_reset_button',
                    $this->get_action_button('reset_reactions', 'resetcoursereactions', format_string($COURSE->fullname)));

            $mform->addHelpButton('reaction_reset_button', 'resetreactions', 'block_point_view');

            // Call javascript from a static function in locallib, because Moodle linter won't let us call global $PAGE from here
            // (and $this->page actually contains the course page, not the edit form page).
            block_point_view_require_edit_form_javascript($this->block->context->id);

        } else {
            $this->add_warning_message($mform, get_string('blockdisabled', 'block_point_view'));
        }
    }

    /**
     * Helper function to add an advcheckbox element with a help button to a form.
     *
     * @param MoodleQuickForm $mform
     * @param string $name Checkbox element name.
     * @param string $str String identifier for label and help button (in block_point_view component).
     * @param mixed $default Default value for the checkbox.
     */
    protected function add_checkbox_with_help(&$mform, $name, $str, $default = 0) {
        $mform->addElement('advcheckbox', $name, get_string($str, 'block_point_view'));
        $mform->addHelpButton($name, $str, 'block_point_view');
        $mform->setDefault($name, $default);
    }

    /**
     * Helper function to add two buttons (enable/disable) to a form.
     *
     * @param MoodleQuickForm $mform
     * @param string $grouplabel The label to put before the two buttons.
     * @param string $name The name of the buttons (their "id" will be "enableall<$name>" and "disableall<$name>").
     * @param string $enablestr String identifier for enable button label (in block_point_view component).
     * @param string $disablestr String identifier for disable button label (in block_point_view component).
     * @param string $a Additional data to pass to get_string for enable and disable button labels.
     * @param string $helpstr String identifier for help button (in block_point_view component).
     * @param string $dataattr Data attributes for both buttons.
     * @param array $attributes Attributes to be added to the form element containing both buttons.
     */
    protected function add_enable_disable_buttons(&$mform, $grouplabel, $name,
            $enablestr, $disablestr, $a, $helpstr, $dataattr = '', $attributes = array()) {

        global $OUTPUT;

        $templatecontext = new stdClass();
        $templatecontext->helpbutton = $OUTPUT->help_icon($helpstr, 'block_point_view');
        $templatecontext->enablename = 'enableall' . $name;
        $templatecontext->enablelabel = get_string($enablestr, 'block_point_view', $a);
        $templatecontext->disablename = 'disableall' . $name;
        $templatecontext->disablelabel = get_string($disablestr, 'block_point_view', $a);
        $templatecontext->dataattr = $dataattr;
        $element = &$mform->addElement('static', '', $grouplabel,
                $OUTPUT->render_from_template('block_point_view/enabledisable', $templatecontext));
        if (!empty($attributes)) {
            $element->setAttributes($attributes);
        }
    }

    /**
     * Helper function to create settings for one course module in the form (reactions checkbox and difficulty track select).
     *
     * @param MoodleQuickForm $mform
     * @param int $cmid Course module id.
     * @param int $sectionid Section id this module belongs to.
     * @param string $type Course module type name.
     * @param string $label Label for form elements (likely, course module name and icon).
     */
    protected function add_activity_config(&$mform, $cmid, $sectionid, $type, $label) {
        $group = array();

        // Checkbox for reactions.
        $group[] =& $mform->createElement( 'advcheckbox', 'config_moduleselectm' . $cmid,
                get_string('reactions', 'block_point_view'), null,
                array(
                        'class' => 'reactions enablemodulereactions cbsec' . $sectionid . ' cb' . $type,
                        'data-section' => 'sec' . $sectionid,
                        'data-type' => $type
                ),
                array(0, $cmid)
        );

        // Difficulty track.
        $group[] =& $mform->createElement( 'html',
                '<span id="track_' . $cmid . '" class="block_point_view track selecttrack difficultytracks"></span>' );

        // Difficulty track select.
        $group[] =& $mform->createElement( 'select', 'config_difficulty_' . $cmid, '',
                array(
                        get_string('nonetrack', 'block_point_view'),
                        get_string('greentrack', 'block_point_view'),
                        get_string('bluetrack', 'block_point_view'),
                        get_string('redtrack', 'block_point_view'),
                        get_string('blacktrack', 'block_point_view')
                ),
                array('class' => 'difficultytracks moduletrackselect', 'data-id' => $cmid)
        );

        $mform->addGroup( $group, 'config_activity_' . $cmid, $label, '', false );

    }

    /**
     * Helper function to add a warning to a form.
     * @param MoodleQuickForm $mform
     * @param string $message
     */
    protected function add_warning_message(&$mform, $message) {
        $warning = html_writer::tag( 'div', $message, ['class' => 'warning'] );
        $mform->addElement('static', '', '', $warning);
    }

    /**
     * Add all form controls for emoji selection to the form.
     * @param MoodleQuickForm $mform
     */
    protected function add_emoji_selection(&$mform) {
        global $CFG;

        $mform->addElement('header', 'images_header', get_string('header_images', 'block_point_view'));

        $pixfiles = array('easy', 'better', 'hard');

        $adminpixenabled = get_config('block_point_view', 'enable_pix_admin');
        $custompixexist = false;

        // List existing pix. Three options:
        // - default pix (in blocks/point_view/pix),
        // - admin pix (in block administration settings),
        // - custom pix (in block configuration).
        $pix = array('default' => array(), 'admin' => array(), 'custom' => array());
        foreach ($pixfiles as $file) {
            $defaultsrc = $CFG->wwwroot . '/blocks/point_view/pix/' . $file . '.png';
            $pix['default'][$file] = $defaultsrc;

            if ($adminpixenabled) {
                $adminsrc = block_point_view_pix_url(1, 'point_views_pix_admin', $file);
                if (!$adminsrc) {
                    $adminsrc = $defaultsrc;
                }
                $pix['admin'][$file] = $adminsrc;
            }

            $customsrc = block_point_view_pix_url($this->block->context->id, 'point_views_pix', $file);
            if ($customsrc) {
                $custompixexist = true;
            } else {
                $customsrc = isset($adminsrc) ? $adminsrc : $defaultsrc;
            }
            $pix['custom'][$file] = $customsrc;
        }

        if ($custompixexist) {
            $deletecustombutton = $this->get_action_button('delete_custom_pix', 'delete_custom_pix');
        } else {
            $pix['custom'] = array();
            $deletecustombutton = null;
        }

        // Create select for the three options.
        $pixselect = array();
        $pixselect[] = &$mform->createElement('html', '<div class="pixselectgroup">');
        $this->create_emoji_radioselect($mform, $pixselect, 'default', $pix);
        if ($adminpixenabled) {
            $this->create_emoji_radioselect($mform, $pixselect, 'admin', $pix);
        }
        $this->create_emoji_radioselect($mform, $pixselect, 'custom', $pix, $deletecustombutton);
        $pixselect[] = &$mform->createElement('html', '</div>');

        $mform->addGroup($pixselect, 'pixselectgroup', get_string('emojitouse', 'block_point_view'), '', false);
        $mform->setDefault('config_pixselect', $adminpixenabled ? 'admin' : 'default');
        $mform->addHelpButton('pixselectgroup', 'emojitouse', 'block_point_view');

        // Create file manager for custom emoji.
        $mform->addElement(
                'filemanager',
                'config_point_views_pix',
                get_string('customemoji', 'block_point_view'),
                null,
                array('subdirs' => 0, 'maxfiles' => 11, 'accepted_types' => '.png')
                );

        $mform->addHelpButton('config_point_views_pix', 'customemoji', 'block_point_view');
        $mform->disabledIf('config_point_views_pix', 'config_pixselect', 'neq', 'custom');

        // Create fields for custom reaction text.
        $current = block_point_view_get_current_pix($this->block, $pixfiles);
        foreach ($pixfiles as $file) {

            $elementname = 'config_pix_text_' . $file;
            $defaulttext = get_string('defaulttext' . $file, 'block_point_view');

            $mform->addElement('text',
                    $elementname,
                    html_writer::empty_tag('img', array(
                            'src' => $current[$file],
                            'class' => 'pix-preview currentpix',
                            'alt' => $defaulttext,
                            'data-reaction' => $file
                    )) .
                    get_string('emojidesc', 'block_point_view')
                    );

            $mform->setDefault($elementname, $defaulttext);
            $mform->setType($elementname, PARAM_RAW);
            $mform->addHelpButton($elementname, 'emojidesc', 'block_point_view');

        }
    }

    /**
     * Helper function to create a radio select element for emoji and add it to a form.
     *
     * @param MoodleQuickForm $mform
     * @param Html_Common[] $group Array to which the element should be added.
     * @param string $value
     * @param string[][] $pix Array of pix sources.
     * @param string|null $additionallegend Optional html to add after the emoji.
     */
    protected function create_emoji_radioselect(&$mform, &$group, $value, $pix, $additionallegend = null) {
        $group[] = $mform->createElement('radio', 'config_pixselect', '',
                get_string($value . 'pix', 'block_point_view'), $value, array('class' => 'pr-2 m-r-0 w-100'));

        $legend = '<label for="id_config_pixselect_' . $value . '" class="d-inline-block">';
        foreach ($pix[$value] as $file => $src) {
            $legend .= html_writer::empty_tag('img', array(
                    'src' => $src,
                    'class' => 'pix-preview',
                    'data-reaction' => $file,
                    'data-source' => $value
            ));
        }
        $legend .= '</label>';
        if ($additionallegend !== null) {
            $legend = '<span>' . $legend . $additionallegend . '</span>';
        }
        $group[] = $mform->createElement('html', $legend);
    }

    /**
     * Helper function to create an action button.
     *
     * @param string $id Button id.
     * @param string $str String identifier for the button label (in block_point_view component).
     * @param string|null $a Additional data to pass to get_string for button label.
     * @return string HTML fragment for the button.
     */
    protected function get_action_button($id, $str, $a = null) {
        return '<button id="' . $id . '" class="btn btn-outline-warning" type="button">' .
                   get_string($str, 'block_point_view', $a) .
               '</button>';
    }

    /**
     * Validation of filemanager files
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {

        global $USER;

        $errors = array();

        if (isset($data['config_pixselect']) && $data['config_pixselect'] == 'custom') {

            $fs = get_file_storage();

            $usercontext = context_user::instance($USER->id);

            $expected = array(
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

            $draftfiles = $fs->get_area_files(
                $usercontext->id,
                'user',
                'draft',
                $data['config_point_views_pix'],
                'filename',
                false
                );

            if (empty($draftfiles)) {
                $errors['config_point_views_pix'] = get_string('errorfilemanagerempty', 'block_point_view');
            }

            foreach ($draftfiles as $file) {

                $pathinfo = pathinfo($file->get_filename());

                if (!in_array($pathinfo['filename'], $expected, true)) {

                    if (!isset($errors['config_point_views_pix'])) {
                        $errors['config_point_views_pix'] = '';
                    }

                    $errors['config_point_views_pix'] .= get_string(
                        'errorfilemanager',
                        'block_point_view',
                        $pathinfo['filename']
                        ) . '<br />';
                }
            }
        }

        return $errors;
    }

    /**
     * File manager and Editor data
     *
     * @param array $defaults
     */
    public function set_data($defaults) {

        $text = '';
        if (!empty($this->block->config) && is_object($this->block->config)) {
            $text = $this->block->config->text;
            $draftideditor = file_get_submitted_draft_itemid('config_text');
            if (empty($text)) {
                $currenttext = '';
            } else {
                $currenttext = $text;
            }
            $defaults->config_text['text'] = file_prepare_draft_area(
                $draftideditor,
                $this->block->context->id,
                'block_point_view',
                'content',
                0,
                array('subdirs' => true),
                $currenttext
            );
            $defaults->config_text['itemid'] = $draftideditor;
            $defaults->config_text['format'] = $this->block->config->format;

            $draftidpix = file_get_submitted_draft_itemid('config_point_views_pix');

            file_prepare_draft_area(
                $draftidpix,
                $this->block->context->id,
                'block_point_view',
                'point_views_pix',
                0,
                array(
                    'subdirs' => 0,
                    'maxfiles' => 20,
                    'accepted_types' => array('.png')
                )
                );

            $defaults->config_point_views_pix = $draftidpix;

            $this->block->config->point_views_pix = $draftidpix;
        }

        unset($this->block->config->text);

        if (!get_config('block_point_view', 'enable_pix_admin')
                && isset($this->block->config->pixselect)
                && $this->block->config->pixselect == 'admin') {
            $this->block->config->pixselect = 'default';
        }

        parent::set_data($defaults);

        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }
        $this->block->config->text = $text;

    }
}
