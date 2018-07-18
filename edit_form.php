<?php

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot . '/blocks/like/lib.php');

try {
    require_login();
} catch (coding_exception $e) {
    echo 'Exception coding_exception (require_login() -> blocks/like/edit_form.php) : ', $e->getMessage(), "\n";
} catch (require_login_exception $e) {
    echo 'Exception require_login_exception (require_login() -> blocks/like/edit_form.php) : ', $e->getMessage(), "\n";
} catch (moodle_exception $e) {
    echo 'Exception moodle_exception (require_login() -> blocks/like/edit_form.php) : ', $e->getMessage(), "\n";
}

class block_like_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        global $CFG, $COURSE, $OUTPUT, $PAGE;

        try {
            if (get_config('block_like', 'enable_likes_admin')) {
                $PAGE->requires->css(new moodle_url($CFG->wwwroot . '/blocks/like/style/style.css'));

                $mform->addElement(
                    'header',
                    'config_header',
                    get_string('blocksettings', 'block')
                );
                $mform->addElement('text',
                    'config_text',
                    get_string('contentinputlabel', 'block_like')
                );
                $mform->setDefault('config_text', '');
                $mform->setType('config_text', PARAM_RAW);

                $mform->addHelpButton(
                    'config_text',
                    'howto_text',
                    'block_like'
                );
                $enablelikes = array();
                $enablelikes[] =& $mform->createElement(
                    'advcheckbox',
                    'config_enable_likes_checkbox',
                    '',
                    null,
                    array(),
                    array(0, 1)
                );
                $mform->addGroup(
                    $enablelikes,
                    'config_enable_likes_group',
                    get_string('enablelikes', 'block_like'),
                    array(' '),
                    false
                );
                $enabledifficulties = array();
                $enabledifficulties[] =& $mform->createElement(
                    'advcheckbox',
                    'config_enable_difficulties_checkbox',
                    '',
                    null,
                    array(),
                    array(0, 1)
                );
                $mform->addGroup(
                    $enabledifficulties,
                    'config_enable_difficulties_group',
                    get_string('enabledifficulties', 'block_like'),
                    array(' '),
                    false
                );

                /* ----------------------------------------------------------------------------------------------------- */

                $coursedata = block_like_get_course_data($COURSE->id);

                $activities = $coursedata['activities'];

                $mform->addElement(
                    'header',
                    'activities',
                    HTML_WRITER::link("#", get_string('config_header_activities', 'block_like'))
                );

                /* IF there is no activities */
                if (empty($activities)) {

                    $warningstring = get_string('no_activities_config_message', 'block_like');

                    $activitieswarning = HTML_WRITER::tag(
                        'div',
                        $warningstring, ['class' => 'warning']
                    );
                    $mform->addElement('static', '', '', $activitieswarning);
                } else {
                    $oldsection = "";
                    $sectionid = 1;

                    /* Enable/Disable by types */
                    block_like_manage_types($mform, $coursedata['types']);

                    $mform->addElement('html', '<br>');

                    $difficulties = array(
                        get_string('nonetrack', 'block_like'),
                        get_string('greentrack', 'block_like'),
                        get_string('bluetrack', 'block_like'),
                        get_string('redtrack', 'block_like'),
                        get_string('blacktrack', 'block_like')
                    );

                    /* Enable/Disable by activity or section */
                    foreach ($activities as $index => $activity) {
                        if ($oldsection != $activity['section']) {
                            $oldsection = $activity['section'];
                            $sectionid++;
                            $sectionname = get_section_name($COURSE, $oldsection);

                            $enabledisable = array();

                            $enabledisable[] =& $mform->createElement(
                                'html',
                                '<br>'
                            );
                            $enabledisable[] =& $mform->createElement(
                                'button',
                                'enable_' . $sectionid,
                                get_string('enableall', 'block_like', $sectionname)
                            );
                            $enabledisable[] =& $mform->createElement(
                                'button',
                                'disable_' . $sectionid,
                                get_string('disableall', 'block_like', $sectionname)
                            );


                            $mform->addGroup(
                                $enabledisable,
                                'manage_checkbox_' . $sectionid, '<br><h4>' . (string)$sectionname . '</h4>',
                                array(' '),
                                false
                            );
                        }

                        $attributes = ['class' => 'iconlarge activityicon'];
                        $icon = $OUTPUT->pix_icon('icon', $activity['modulename'], $activity['type'], $attributes);

                        $activityoption = array();
                        $activityoption[] =& $mform->createElement(
                            'advcheckbox',
                            'config_moduleselectm' . $activity['id'],
                            '',
                            null,
                            array('group' => $sectionid, 'class' => $activity['type'] . ' check_section_' . $sectionid),
                            array(0, $activity['id'])
                        );

                        $activityoption[] = &$mform->createElement(
                            'select',
                            'config_difficulty_' . $activity['id'],
                            '',
                            $difficulties,
                            array('class' => 'selectDifficulty')
                        );

                        $mform->addGroup($activityoption, 'config_activity_' . $activity['id'],
                            $icon . format_string($activity['name']), array(' '), false);
                    }

                    /* Shortcuts */
                    $mform->addElement(
                        'button',
                        'go_to_save',
                        get_string('go_to_save', 'block_like')
                    );
                    $mform->addElement(
                        'button',
                        'close_field',
                        get_string('close_field', 'block_like')
                    );

                    /* ----------------------------------------------------------------------------------------------------- */

                    /* Emojis images configuration */
                    $mform->addElement(
                        'header',
                        'config_images',
                        HTML_WRITER::link("#", get_string('config_header_images', 'block_like'))
                    );

                    $fs = get_file_storage();

                    $pixfiles = array('easy', 'better',  'hard');
                    $easyimg = $betterimg = $hardimg = null;
                    $pixpreview = array();

                    foreach ($pixfiles as $file) {
                        if ($fs->file_exists($this->block->context->id, 'block_like', 'likes_pix', 0, '/', $file.'.png')) {
                            ${$file.'img'} = block_like_pix_url($this->block->context->id, 'likes_pix', $file);
                        } else if ($fs->file_exists(1, 'block_like', 'likes_pix_admin', 0, '/', $file.'.png')) {
                            ${$file.'img'} = block_like_pix_url(1, 'likes_pix_admin', $file);
                        } else {
                            ${$file.'img'} = $CFG->wwwroot . '/blocks/like/pix/'.$file.'.png';
                        }
                    }

                    $pixpreview[] =& $mform->createElement(
                        'static',
                        'config_current_pix',
                        '',
                        '<img src="' . $easyimg . '" style="width: 30px"/>
                        <img src="' . $betterimg . '" style="width: 30px"/>
                        <img src="' . $hardimg . '" style="width: 30px"/>
                        &nbsp;&nbsp;'
                    );

                    $pixpreview[] =& $mform->createElement(
                        'button',
                        'config_reset_pix',
                        get_string('pixreset', 'block_like')
                    );

                    $pixpreview[] =& $mform->createElement(
                        'static',
                        'config_reset_pix_text',
                        '',
                        get_string('pixresettext', 'block_like')
                    );

                    $mform->addGroup($pixpreview, 'config_pix_preview_group',
                        get_string('pixcurrently', 'block_like'),
                        array(' '),
                        false
                    );

                    $mform->disabledIf(
                        'config_reset_pix',
                        'config_enable_pix_checkbox',
                        'notchecked'
                    );

                    $enableperso = array();
                    $enableperso[] =& $mform->createElement(
                        'advcheckbox',
                        'config_enable_pix_checkbox',
                        '',
                        null,
                        array(),
                        array(0, 1)
                    );

                    $mform->addGroup(
                        $enableperso,
                        'config_enable_pix',
                        get_string('enablecustompix', 'block_like'),
                        array(' '),
                        false
                    );

                    $mform->addElement(
                        'filemanager',
                        'config_likes_pix',
                        get_string('likepix', 'block_like'),
                        null,
                        array('subdirs' => 0, 'maxfiles' => 11, 'accepted_types' => '.png')
                    );

                    $mform->disabledIf(
                        'config_likes_pix',
                        'config_enable_pix_checkbox',
                        'notchecked'
                    );

                    $mform->addElement(
                        'static',
                        '',
                        '',
                        get_string('likepixdesc', 'block_like')
                    );

                    foreach ($pixfiles as $file) {
                        ${$file.'text'} = array();

                        ${$file.'text'}[] =& $mform->createElement('text',
                            'config_text_'.$file,
                            get_string('text'.$file, 'block_like')
                        );
                        $mform->setDefault('config_text_'.$file, get_string('defaulttext'.$file, 'block_like'));
                        $mform->setType('config_text_'.$file, PARAM_RAW);

                        $mform->addGroup(${$file.'text'}, 'config_'.$file.'_text_group',
                            html_writer::empty_tag(
                                'img',
                                array(
                                    'src' => ${$file.'img'},
                                    'style' => 'width:30px'
                                )
                            ) . get_string('emojidesc', 'block_like'),
                            array(' '),
                            false
                        );
                    }

                    /* ----------------------------------------------------------------------------------------------------- */

                    /* Reaction reinitialisation */
                    $mform->addElement(
                        'header',
                        'config_reset',
                        HTML_WRITER::link("#", get_string('config_header_reset', 'block_like'))
                    );

                    $reinit = array();
                    $reinit[] =& $mform->createElement(
                        'button',
                        'config_reaction_reset_button',
                        get_string('reactionreset', 'block_like', $COURSE->fullname)
                    );

                    $mform->addGroup($reinit, 'config_reaction_reset',
                        '',
                        array(' '),
                        false
                    );

                    $reinitconfirm = array();
                    $reinitconfirm[] =& $mform->createElement(
                        'button',
                        'config_reset_yes',
                        get_string('yes', 'block_like')
                    );

                    $reinitconfirm[] =& $mform->createElement(
                        'button',
                        'config_reset_no',
                        get_string('no', 'block_like')
                    );

                    $reinitconfirm[] =& $mform->createElement(
                        'static',
                        'config_reset_pix_text',
                        '',
                        get_string('pixresettext', 'block_like')
                    );

                    $mform->addGroup($reinitconfirm,
                        'config_reset_confirm',
                        get_string('confirmation', 'block_like', $COURSE->fullname),
                        array(' '),
                        false
                    );
                }

                $trackcolor = array(
                    'greentrack' => get_config('block_like', 'green_track_color_admin'),
                    'bluetrack' => get_config('block_like', 'blue_track_color_admin'),
                    'redtrack' => get_config('block_like', 'red_track_color_admin'),
                    'blacktrack' => get_config('block_like', 'black_track_color_admin'),
                );

                /* Imports */
                $params = array(range(2, $sectionid), $coursedata['types'], $coursedata['ids'], $trackcolor, $COURSE->id);
                $PAGE->requires->js_call_amd('block_like/script_config_like', 'init', $params);
            } else {
                $mform->addElement(
                    'static', '', '', get_string('blockdisabled', 'block_like')
                );
            }
        } catch (coding_exception $e) {
            echo 'Exception coding_exception (specific_definition() -> blocks/like/edit_form.php) : ', $e->getMessage(), "\n";
        } catch (moodle_exception $e) {
            echo 'Exception moodle_exception (specific_definition() -> blocks/like/edit_form.php) : ', $e->getMessage(), "\n";
        }
    }

    public function validation($data, $files) {
        global $USER;

        $errors = array();
        if ($data['config_enable_pix_checkbox']) {
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
            try {
                $draftfiles = $fs->get_area_files(
                    $usercontext->id,
                    'user',
                    'draft',
                    $data['config_likes_pix'],
                    'filename',
                    false
                );
            } catch (coding_exception $e) {
                echo '';
            }

            foreach ($draftfiles as $file) {
                $pathinfo = pathinfo($file->get_filename());
                if (!in_array($pathinfo['filename'], $expected, true)) {
                    if (!isset($errors['config_likes_pix'])) {
                        $errors['config_likes_pix'] = '';
                    }
                    try {
                        $errors['config_likes_pix'] .= get_string(
                            'errorfilemanager',
                            'block_like',
                            $pathinfo['filename']
                        ) . '<br />';
                    } catch (coding_exception $e) {
                        echo '';
                    }
                }
            }
        }
        return $errors;
    }

    public function set_data($defaults) {
        if (!empty($this->block->config) && is_object($this->block->config)) {
            $draftid = file_get_submitted_draft_itemid('config_likes_pix');
            file_prepare_draft_area(
                $draftid,
                $this->block->context->id,
                'block_like',
                'likes_pix',
                0,
                array(
                    'subdirs' => 0,
                    'maxfiles' => 20,
                    'accepted_types' => array('.png')
                )
            );
            $defaults->config_likes_pix = $draftid;
            $this->block->config->likes_pix = $draftid;
        }
        parent::set_data($defaults);
    }
}