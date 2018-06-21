<?php

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../config.php');
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
                'checkbox',
                'config_enable_likes_checkbox',
                ''
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
                'checkbox',
                'config_enable_difficulties_checkbox',
                ''
            );
            $mform->addGroup(
                $enabledifficulties,
                'config_enable_difficulties_group',
                get_string('enabledifficulties', 'block_like'),
                array(' '),
                false
            );

            /* -------------------------------------------------------------------------------------------------------------- */

            $config = new stdClass();
            $config->moduletype = array('book', 'chat', 'file', 'forum', 'glossary', 'page', 'quiz', 'resource',
                'url', 'vpl', 'wiki');

            $mform->addElement(
                'header',
                'activities',
                HTML_WRITER::link("#", get_string('config_header_activities', 'block_like'))
            );
            $activities = block_like_get_activities($COURSE->id, $config);


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

                /* Get the lower et higher module ID of the course */
                $moduleids = array();
                $managetypesparams = array();
                foreach ($activities as $index => $activity) {
                    if (!in_array($activity['type'], $managetypesparams)) {
                        array_push($managetypesparams, $activity['type']);
                    }
                    array_push($moduleids, $activity['id']);
                }

                /* Enable/Disable by types */
                block_like_manage_types($mform, $managetypesparams);

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
                            get_string('enableall', 'block_like') . $sectionname
                        );
                        $enabledisable[] =& $mform->createElement(
                            'button',
                            'disable_' . $sectionid,
                            get_string('disableall', 'block_like') . $sectionname
                        );


                        $mform->addGroup(
                            $enabledisable,
                            'manage_checkbox_' . $sectionid, '<br><h4>' . (string)$sectionname . '</h4>',
                            array(' '),
                            false
                        );
                        block_like_hide(
                            $mform,
                            array('enable_' . $sectionid, 'disable_' . $sectionid),
                            'config_enable_likes_checkbox'
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

                    $activityoption[] = & $mform->createElement(
                        'select',
                        'config_difficulty_' . $activity['id'],
                        '',
                        $difficulties,
                        array('class' => 'selectDifficulty')
                    );

                    $mform->addGroup($activityoption, 'config_activity_' . $activity['id'],
                        $icon . format_string($activity['name']), array(' '), false);

                    block_like_hide(
                        $mform,
                        array('config_moduleselectm' . $activity['id']),
                        'config_enable_likes_checkbox'
                    );

                    block_like_hide(
                        $mform,
                        array('config_difficulty_' . $activity['id']),
                        'config_enable_difficulties_checkbox'
                    );
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

                /* -------------------------------------------------------------------------------------------------------------- */

                /* Emojis images configuration */
                $mform->addElement(
                    'header',
                    'config_images',
                    HTML_WRITER::link("#", get_string('config_header_images', 'block_like'))
                );
                $enableperso = array();
                $enableperso[] =& $mform->createElement(
                    'checkbox',
                    'config_enable_pix_checkbox',
                    ''
                );
                $mform->addGroup(
                    $enableperso,
                    'config_enable_pix',
                    get_string('enableimgperso', 'block_like'),
                    array(' '),
                    false
                );
                $mform->addElement('filemanager',
                    'config_likes_pix',
                    get_string('likepix', 'block_like'),
                    null,
                    array('subdirs' => 0, 'maxfiles' => 20, 'accepted_types' => '.png'));
                $mform->disabledIf(
                    'config_likes_pix_disableif',
                    'config_enable_pix_checkbox',
                    'notchecked'
                );
                $mform->addElement(
                    'static',
                    '',
                    '',
                    get_string('likepixdesc', 'block_like')
                );
            }
        } catch (coding_exception $e) {
            echo 'Exception coding_exception (specific_definition() -> blocks/like/edit_form.php) : ', $e->getMessage(), "\n";
        } catch (moodle_exception $e) {
            echo 'Exception moodle_exception (specific_definition() -> blocks/like/edit_form.php) : ', $e->getMessage(), "\n";
        }

        /* Imports */
        $params = array($sectionid, $managetypesparams, $moduleids);
        $PAGE->requires->js_call_amd('block_like/module', 'init', $params);
    }
}