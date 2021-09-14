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

        global $CFG, $PAGE, $COURSE, $OUTPUT;

        if (get_config('block_point_view', 'enable_point_views_admin')) {

            // Add block_point_view class to form element for styling,
            // as it is not done for the body element on block edition page.
            $mform->updateAttributes(array('class' => $mform->getAttribute('class') . ' block_point_view'));

            $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

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

            $mform->addHelpButton('config_text', 'howto_text', 'block_point_view');

            // Reaction activation checkbox.
            $this->add_checkbox_with_help($mform, 'config_enable_point_views_checkbox', 'enablepoint_views', 'howto_enable_point_views_checkbox');

            // Difficulties activation checkbox
            $this->add_checkbox_with_help($mform, 'config_enable_difficulties_checkbox', 'enabledifficulties', 'howto_enable_difficulties_group');

            // ----------------------------------------------------------------------------------------------------- //

            $mform->addElement('header', 'activities', get_string('config_header_activities', 'block_point_view'));

            $coursedata = block_point_view_get_course_data($COURSE->id);
            $activities = $coursedata['activities'];

            if (empty($activities)) {
                $this->add_warning_message($mform, get_string('noactivity', 'block_point_view'));
            }

            /* Enable/Disable by types */
            foreach ($coursedata['types'] as $type) {
                $this->add_enable_disable_buttons($mform, '',
                        'enableall' . $type, get_string('enable_type', 'block_point_view', get_string('modulenameplural', $type)),
                        'disableall' . $type, get_string('disable_type', 'block_point_view', get_string('modulenameplural', $type)),
                        'howto_type',
                        'data-type="' . $type . '"',
                        array('class' => 'reactions'));
            }

            $oldsection = '';
            $sectionid = 0;
            /* Enable/Disable by activity or section */
            foreach ($activities as $activity) {

                if ($activity['section'] != $oldsection) {

                    $sectionid++;
                    $sectionname = get_section_name($COURSE, $activity['section']);

                    $this->add_enable_disable_buttons($mform, '<h4>' . $sectionname . '</h4>',
                            'enableallsec' . $sectionid, get_string('enableall', 'block_point_view', $sectionname),
                            'disableallsec' . $sectionid, get_string('disableall', 'block_point_view', $sectionname),
                            'howto_manage_checkbox',
                            'data-section="sec' . $sectionid . '"',
                            array('class' => 'section-header'));

                    $oldsection = $activity['section'];
                }

                $icon = $OUTPUT->pix_icon('icon', $activity['modulename'], $activity['type'], array('class' => 'iconlarge activityicon'));

                $this->add_activity_config($mform, $activity['id'], $sectionid, $activity['type'], $icon . format_string($activity['name']));
            }
            // ----------------------------------------------------------------------------------------------------- //

            // Emojis images configuration.

            $this->add_emoji_selection($mform);

            // ----------------------------------------------------------------------------------------------------- //

            // Reaction reinitialisation.
            $mform->addElement(
                'header',
                'config_reset',
                get_string('config_header_reset', 'block_point_view')
                );

            $mform->addElement(
                    'static',
                    'config_reaction_reset_button',
                    '<button id="reset_reactions" class="btn btn-outline-warning" type="button">' .
                        get_string('resetreactions', 'block_point_view', format_string($COURSE->fullname)) .
                    '</button>'
                    );

            $envconf = array(
                'courseid' => $COURSE->id,
                'contextid' => $this->block->context->id
            );

            $trackcolors =  array(
                    '',
                    get_config('block_point_view', 'green_track_color_admin'),
                    get_config('block_point_view', 'blue_track_color_admin'),
                    get_config('block_point_view', 'red_track_color_admin'),
                    get_config('block_point_view', 'black_track_color_admin')
            );

            // AMD Call.
            $params = array($envconf, $trackcolors);

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
     * @param string $elementname
     * @param string $str
     * @param string $helpstr
     */
    private function add_checkbox_with_help(&$mform, $elementname, $str, $helpstr = '') {
        if (empty($helpstr)) {
            $helpstr = $str;
        }

        $group = array();

        $group[] =& $mform->createElement( 'advcheckbox', $elementname );

        $mform->addGroup( $group, $elementname . '_group', get_string($str, 'block_point_view'), '', false );

        $mform->addHelpButton( $elementname . '_group', $helpstr, 'block_point_view' );
    }

    /**
     *
     * @param MoodleQuickForm $mform
     */
    private function add_enable_disable_buttons(&$mform, $grouplabel, $enablename, $enablelabel, $disablename, $disablelabel, $helpstr, $dataattr = '', $attributes = array()) {
        global $OUTPUT;

        $templatecontext = new stdClass();
        $templatecontext->helpbutton = $OUTPUT->help_icon($helpstr, 'block_point_view');
        $templatecontext->enablename = $enablename;
        $templatecontext->enablelabel = $enablelabel;
        $templatecontext->disablename = $disablename;
        $templatecontext->disablelabel = $disablelabel;
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

        $group[] =& $mform->createElement( 'advcheckbox', 'config_moduleselectm' . $id, '', null,
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

        $mform->addElement('header', 'config_images', get_string('config_header_images', 'block_point_view'));

        $fs = get_file_storage();

        $pixfiles = array('easy', 'better', 'hard');

        $adminpixenabled = get_config('block_point_view', 'enable_pix_admin');
        $custompixexist = false;

        $pix = array('default' => array(), 'admin' => array(), 'custom' => array());
        foreach ($pixfiles as $file) {
            $defaultsrc = $CFG->wwwroot . '/blocks/point_view/pix/' . $file . '.png';
            $pix['default'][$file] = '<img src="' . $defaultsrc . '" class="pix-preview"/>';

            if ($adminpixenabled) {
                if ($fs->file_exists(1, 'block_point_view', 'point_views_pix_admin', 0, '/', $file . '.png')) {
                    $adminsrc = block_point_view_pix_url(1, 'point_views_pix_admin', $file);
                } else {
                    $adminsrc = $defaultsrc;
                }
                $pix['admin'][$file] = '<img src="' . $adminsrc . '" class="pix-preview"/>';
            }

            if ($fs->file_exists($this->block->context->id, 'block_point_view', 'point_views_pix', 0, '/', $file . '.png')) {
                $customsrc = block_point_view_pix_url($this->block->context->id, 'point_views_pix', $file);
                $custompixexist = true;
            } else {
                $customsrc = isset($adminsrc) ? $adminsrc : $defaultsrc;
            }
            $pix['custom'][$file] = '<img src="' . $customsrc . '" class="pix-preview"/>';
        }

        $pixselect = array();
        $pixselect[] = &$mform->createElement('radio', 'config_pixselect', '', 'Default: ' . implode('', $pix['default']), 'default');
        if ($adminpixenabled) {
            $pixselect[] = &$mform->createElement('radio', 'config_pixselect', '', 'Site default: ' . implode('', $pix['admin']), 'admin');
        }

        if ($custompixexist) {
            $custompixdisplay = '<span class="custom-pix-preview">' . implode('', $pix['custom']) . '</span>';
            $deletecustom = '<button id="delete_custom_pix" class="btn btn-outline-warning" type="button">' .
                                'Delete custom emoji' .
                            '</button>';
        } else {
            $custompixdisplay = '';
            $deletecustom = '';
        }
        $pixselect[] = &$mform->createElement('radio', 'config_pixselect', '', 'Custom: ' . $custompixdisplay, 'custom');

        $pixselect[] = &$mform->createElement('html', $deletecustom);

        $group = $mform->addGroup($pixselect, 'config_pixselectgroup', 'Emoji to use', '', false);
        $group->setAttributes(array('class' => 'pixselectgroup'));

        if ($adminpixenabled) {
            $mform->setDefault('config_pixselect', 'admin');
        } else {
            $mform->setDefault('config_pixselect', 'default');
        }

        // TODO Manage upgrade to new version (courses with custom emoji should be correctly set to "custom").
        // TODO Remove 'enable_pix_checkbox' completely during upgrade (update 'pixselect' accordingly).

        $mform->addHelpButton(
                'config_pixselectgroup',
                'howto_pix_preview_group',
                'block_point_view'
                );

        $mform->addElement(
                'filemanager',
                'config_point_views_pix',
                get_string('point_viewpix', 'block_point_view'),
                null,
                array('subdirs' => 0, 'maxfiles' => 11, 'accepted_types' => '.png')
                );

        $mform->disabledIf(
                'config_point_views_pix',
                'config_pixselect',
                'neq',
                'custom'
                );

        $mform->addElement(
                'static',
                'custompixnote',
                '',
                get_string('point_viewpixdesc', 'block_point_view')
                );

        foreach ($pixfiles as $file) {

            $mform->addElement('text',
                    'config_pix_text_'.$file,
                    '<img src="' . $CFG->wwwroot . '/blocks/point_view/pix/' . $file . '.png' . '" class="pix-preview"/>' .
                    get_string('emojidesc', 'block_point_view')
                    );

            $mform->setDefault('config_pix_text_'.$file, get_string('defaulttext'.$file, 'block_point_view'));

            $mform->setType('config_pix_text_'.$file, PARAM_RAW);

            $mform->addHelpButton(
                    'config_pix_text_' . $file,
                    'howto_pix_text_group',
                    'block_point_view'
                    );

        }
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
        parent::set_data($defaults);

        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }
        $this->block->config->text = $text;

    }
}