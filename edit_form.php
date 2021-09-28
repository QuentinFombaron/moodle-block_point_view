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
global $CFG;
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot . '/blocks/point_view/lib.php');
require_once(__DIR__ . '/locallib.php');

try {
    require_login();
    confirm_sesskey();
} catch (coding_exception $e) {
    echo 'Exception [coding_exception] (blocks/point_view/edit_form.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (require_login_exception $e) {
    echo 'Exception [require_login_exception] (blocks/point_view/edit_form.php -> require_login()) : ',
    $e->getMessage(), "\n";
} catch (moodle_exception $e) {
    echo 'Exception [moodle_exception] (blocks/point_view/edit_form.php -> require_login()) : ',
    $e->getMessage(), "\n";
}

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

        global $CFG, $COURSE, $OUTPUT;

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
            $mform->addElement('selectyesno', 'config_enable_point_views', get_string('enablepoint_views', 'block_point_view'));

            $this->add_checkbox_with_help($mform, 'config_enable_point_views_new_modules', 'enableforfuturemodules', 1);
            $mform->disabledIf('config_enable_point_views_new_modules', 'config_enable_point_views', 'eq', 0);

            $this->add_checkbox_with_help($mform, 'config_show_other_users_reactions', 'showotherreactions', 1);
            $mform->disabledIf('config_show_other_users_reactions', 'config_enable_point_views', 'eq', 0);

            // Difficulties activation.
            $mform->addElement('selectyesno', 'config_enable_difficultytracks', get_string('enabledifficulties', 'block_point_view'));

            // ----------------------------------------------------------------------------------------------------- //

            $mform->addElement('header', 'activities_header', get_string('header_activities', 'block_point_view'));


            $modinfo = get_fast_modinfo($COURSE->id, -1);
            $cms = $modinfo->cms;
            $modtypes = array_keys($modinfo->instances);

            if (empty($cms)) {
                $this->add_warning_message($mform, get_string('noactivity', 'block_point_view'));
            }

            /* Enable/Disable by types */
            foreach ($modtypes as $type) {
                $this->add_enable_disable_buttons($mform, '',
                        $type,
                        get_string('modulenameplural', $type),
                        'enable_type', 'disable_type',
                        'enable_disable_type',
                        'data-type="' . $type . '"',
                        array('class' => 'reactions'));
            }

            $oldsection = '';
            $sectionid = 0;
            /* Enable/Disable by activity or section */
            foreach ($cms as $cm) {

                if ($cm->sectionnum != $oldsection) {

                    $sectionid++;
                    $sectionname = get_section_name($COURSE, $cm->sectionnum);

                    $this->add_enable_disable_buttons($mform, '<h4>' . $sectionname . '</h4>',
                            'sec' . $sectionid,
                            $sectionname,
                            'enableall', 'disableall',
                            'enable_disable_section',
                            'data-section="sec' . $sectionid . '"',
                            array('class' => 'section-header'));

                    $oldsection = $cm->sectionnum;
                }

                $icon = $OUTPUT->pix_icon('icon', $cm->get_module_type_name(), $cm->modname, array('class' => 'iconlarge activityicon'));

                $this->add_activity_config($mform, $cm->id, $sectionid, $cm->modname, $icon . $cm->get_formatted_name());
            }
            // ----------------------------------------------------------------------------------------------------- //

            // Emojis images configuration.

            $this->add_emoji_selection($mform);

            // ----------------------------------------------------------------------------------------------------- //

            // Reaction reinitialisation.
            $mform->addElement(
                'header',
                'reset_header',
                get_string('resetreactions', 'block_point_view')
                );

            $mform->addElement(
                    'static',
                    'config_reaction_reset_button',
                    '<button id="reset_reactions" class="btn btn-outline-warning" type="button">' .
                        get_string('resetcoursereactions', 'block_point_view', format_string($COURSE->fullname)) .
                    '</button>'
                    );

            $mform->addHelpButton('config_reaction_reset_button', 'resetreactions', 'block_point_view');

            $envconf = array(
                'courseid' => $COURSE->id,
                'contextid' => $this->block->context->id
            );

            $trackcolors = block_point_view_get_track_colors();

            // AMD Call.
            $params = array($envconf, $trackcolors);

            global $PAGE;
            $PAGE->requires->js_call_amd('block_point_view/script_config_point_view', 'init', $params);
            $PAGE->requires->string_for_js('resetreactionsconfirmation', 'block_point_view', format_string($COURSE->fullname));
            $PAGE->requires->strings_for_js(array('deleteemojiconfirmation', 'reactionsresetsuccessfully'), 'block_point_view');
            $PAGE->requires->strings_for_js(array('ok', 'info'), 'moodle');

        } else {
            $this->add_warning_message($mform, get_string('blockdisabled', 'block_point_view'));
        }
    }

    /**
     *
     * @param MoodleQuickForm $mform
     */
    private function add_checkbox_with_help(&$mform, $name, $str, $default = 0) {
        $mform->addElement('advcheckbox', $name, get_string($str, 'block_point_view'));
        $mform->addHelpButton($name, $str, 'block_point_view');
        $mform->setDefault($name, $default);
    }

    /**
     *
     * @param MoodleQuickForm $mform
     */
    private function add_enable_disable_buttons(&$mform, $grouplabel, $name, $label, $enablestr, $disablestr, $helpstr, $dataattr = '', $attributes = array()) {
        global $OUTPUT;

        $templatecontext = new stdClass();
        $templatecontext->helpbutton = $OUTPUT->help_icon($helpstr, 'block_point_view');
        $templatecontext->enablename = 'enableall' . $name;
        $templatecontext->enablelabel = get_string($enablestr, 'block_point_view', $label);
        $templatecontext->disablename = 'disableall' . $name;
        $templatecontext->disablelabel = get_string($disablestr, 'block_point_view', $label);
        $templatecontext->dataattr = $dataattr;
        $element = &$mform->addElement('static', '', $grouplabel,
                $OUTPUT->render_from_template('block_point_view/enabledisable', $templatecontext));
        if (!empty($attributes)) {
            $element->setAttributes($attributes);
        }
    }

    /**
     *
     * @param MoodleQuickForm $mform
     * @param int $id
     * @param int $sectionid
     * @param string $label
     */
    private function add_activity_config(&$mform, $id, $sectionid, $type, $label) {
        $group = array();

        $group[] =& $mform->createElement( 'advcheckbox', 'config_moduleselectm' . $id, get_string('reactions', 'block_point_view'), null,
                array(
                        'class' => 'reactions enablemodulereactions cbsec' . $sectionid . ' cb' . $type,
                        'data-section' => 'sec' . $sectionid,
                        'data-type' => $type
                ),
                array(0, $id)
        );

        $group[] =& $mform->createElement( 'html', '<span id="track_' . $id . '" class="block_point_view track selecttrack difficultytracks"></span>' );

        $group[] =& $mform->createElement( 'select', 'config_difficulty_' . $id, '',
                array(
                        get_string('nonetrack', 'block_point_view'),
                        get_string('greentrack', 'block_point_view'),
                        get_string('bluetrack', 'block_point_view'),
                        get_string('redtrack', 'block_point_view'),
                        get_string('blacktrack', 'block_point_view')
                ),
                array('class' => 'difficultytracks moduletrackselect', 'data-id' => $id)
        );

        $mform->addGroup( $group, 'config_activity_' . $id, $label, '', false );

    }

    /**
     *
     * @param MoodleQuickForm $mform
     * @param string $message
     */
    private function add_warning_message(&$mform, $message) {
        $warning = html_writer::tag( 'div', $message, ['class' => 'warning'] );
        $mform->addElement('static', '', '', $warning);
    }

    /**
     *
     * @param MoodleQuickForm $mform
     */
    private function add_emoji_selection(&$mform) {
        global $CFG;

        $mform->addElement('header', 'images_header', get_string('header_images', 'block_point_view'));

        $fs = get_file_storage();

        $pixfiles = array('easy', 'better', 'hard');

        $adminpixenabled = get_config('block_point_view', 'enable_pix_admin');
        $custompixexist = false;

        $pixsrc = array('default' => array(), 'admin' => array(), 'custom' => array());
        foreach ($pixfiles as $file) {
            $defaultsrc = $CFG->wwwroot . '/blocks/point_view/pix/' . $file . '.png';
            $pixsrc['default'][$file] = $defaultsrc;

            if ($adminpixenabled) {
                if ($fs->file_exists(1, 'block_point_view', 'point_views_pix_admin', 0, '/', $file . '.png')) {
                    $adminsrc = block_point_view_pix_url(1, 'point_views_pix_admin', $file);
                } else {
                    $adminsrc = $defaultsrc;
                }
                $pixsrc['admin'][$file] = $adminsrc;
            }

            if ($fs->file_exists($this->block->context->id, 'block_point_view', 'point_views_pix', 0, '/', $file . '.png')) {
                $customsrc = block_point_view_pix_url($this->block->context->id, 'point_views_pix', $file);
                $custompixexist = true;
            } else {
                $customsrc = isset($adminsrc) ? $adminsrc : $defaultsrc;
            }
            $pixsrc['custom'][$file] = $customsrc;
        }

        $pix = array();
        foreach ($pixsrc as $source => $srcs) {
            $pix[$source] = array();
            foreach ($srcs as $file => $src) {
                $pix[$source][$file] = '<img src="' . $src . '" class="pix-preview" data-reaction="' . $file . '" data-source="' . $source . '"/>';
            }
        }

        if (!$custompixexist) {
            $pix['custom'] = array();
        }

        $pixselect = array();
        $pixselect[] = $this->create_emoji_radioselect($mform, 'default', $pix);
        $pixselect[] = &$mform->createElement('html', '<span class="flex-fill"></span>');
        if ($adminpixenabled) {
            $pixselect[] = $this->create_emoji_radioselect($mform, 'admin', $pix);
            $pixselect[] = &$mform->createElement('html', '<span class="flex-fill"></span>');
        }
        $pixselect[] = $this->create_emoji_radioselect($mform, 'custom', $pix);
        if ($custompixexist) {
            $pixselect[] = &$mform->createElement('html',
                    '<button id="delete_custom_pix" class="btn btn-outline-warning" type="button">' .
                        get_string('delete_custom_pix', 'block_point_view') .
                    '</button>');
        }

        $group = $mform->addGroup($pixselect, 'pixselectgroup', get_string('emojitouse', 'block_point_view'), '', false);
        $group->setAttributes(array('class' => 'pixselectgroup'));

        if ($adminpixenabled) {
            $mform->setDefault('config_pixselect', 'admin');
        } else {
            $mform->setDefault('config_pixselect', 'default');
        }

        $mform->addHelpButton('pixselectgroup', 'emojitouse', 'block_point_view');

        $mform->addElement(
                'filemanager',
                'config_point_views_pix',
                get_string('customemoji', 'block_point_view'),
                null,
                array('subdirs' => 0, 'maxfiles' => 11, 'accepted_types' => '.png')
                );

        $mform->addHelpButton('config_point_views_pix', 'customemoji', 'block_point_view');

        $mform->disabledIf('config_point_views_pix', 'config_pixselect', 'neq', 'custom');

        $current = block_point_view_get_current_pix($this->block, $pixfiles);
        foreach ($pixfiles as $file) {

            $elementname = 'config_pix_text_' . $file;
            $defaulttext = get_string('defaulttext' . $file, 'block_point_view');

            $mform->addElement('text',
                    $elementname,
                    '<img src="' . $current[$file] . '" alt="' . $defaulttext . '" class="pix-preview currentpix" data-reaction="' . $file . '"/>' .
                    get_string('emojidesc', 'block_point_view')
                    );

            $mform->setDefault($elementname, $defaulttext);

            $mform->setType($elementname, PARAM_RAW);

            $mform->addHelpButton($elementname, 'emojidesc', 'block_point_view');

        }
    }

    private function create_emoji_radioselect($mform, $value, $pix) {
        return $mform->createElement('radio', 'config_pixselect', '',
                '<span class="pixlabel">' . get_string($value . 'pix', 'block_point_view') . '</span>' . implode('', $pix[$value]), $value);
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
                $errors['config_point_views_pix'] = 'Please provide at least one file.';
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
     * @param {array} $defaults
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

        if (!get_config('block_point_view', 'enable_pix_admin') && $this->block->config->pixselect == 'admin') {
            $this->block->config->pixselect = 'default';
        }

        parent::set_data($defaults);

        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }
        $this->block->config->text = $text;

    }
}