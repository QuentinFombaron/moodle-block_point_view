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

class block_like_edit_form extends block_edit_form
{

    protected function specific_definition($mform) {
        global $CFG, $COURSE, $OUTPUT, $PAGE;

        try {
            $PAGE->requires->css(new moodle_url($CFG->wwwroot . '/blocks/like/style/style.css'));
        } catch (coding_exception $e) {
            echo 'Exception coding_exception (get_content() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";
            die();
        } catch (moodle_exception $e) {
            echo 'Exception moodle_exception (get_content() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";
            die();
        }

        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_text', get_string('contentinputlabel', 'block_like'));
        $mform->setDefault('config_text', '');
        $mform->setType('config_text', PARAM_RAW);

        /* -------------------------------------------------------------------------------------------------------------- */

        $config = new stdClass();
        $config->moduletype = array('book', 'chat', 'file', 'forum', 'glossary', 'page', 'quiz', 'resource',
            'url', 'vpl', 'wiki');

        try {
            $activities = block_like_get_activities($COURSE->id, $config);
        } catch (moodle_exception $e) {
            echo 'Exception moodle_exception (specific_definition() -> blocks/like/edit_form.php) : ', $e->getMessage(), "\n";
        }

        try {
            $mform->addElement('header', 'activities', HTML_WRITER::link("#",
                get_string('config_header_activities', 'block_like')));
            $mform->addElement('button', 'go_to_save', get_string('go_to_save', 'block_like'));
            $mform->addElement('button', 'close_field', get_string('close_field', 'block_like'));
        } catch (coding_exception $e) {
            echo 'Exception coding_exception (specific_definition() -> blocks/like/edit_form.php) : ', $e->getMessage(), "\n";

        }

        /* IF there is no activities */
        if (empty($activities)) {
            try {
                $warningstring = get_string('no_activities_config_message', 'block_like');
            } catch (coding_exception $e) {
                echo 'Exception coding_exception (specific_definition() -> blocks/like/edit_form.php) : ', $e->getMessage(), "\n";

            }
            $activitieswarning = HTML_WRITER::tag('div', $warningstring, ['class' => 'warning']);
            $mform->addElement('static', '', '', $activitieswarning);
        } else {
            $oldsection = "";
            $sectionid = 1;

            $moduleidmax = 0;
            $managetypesparams = array();
            foreach ($activities as $index => $activity) {
                if (!in_array($activity['type'].'s', $managetypesparams)) {
                    array_push($managetypesparams, $activity['type'].'s');
                }
                /* Get the higher module ID */
                $moduleidmax = max($moduleidmax, $activity['id']);
            }

            block_like_manage_types($mform, $managetypesparams);

            $mform->addElement('html', '<br>');

            foreach ($activities as $index => $activity) {
                if ($oldsection != $activity['section']) {
                    $oldsection = $activity['section'];
                    $sectionid++;
                    $sectionname = get_section_name($COURSE, $oldsection);

                    $enabledisable = array();

                    try {
                        $enabledisable[] =& $mform->createElement('html', '<br>');
                        $enabledisable[] =& $mform->createElement('button', 'enable_' . $sectionid,
                            get_string('enableall', 'block_like') . $sectionname);
                        $enabledisable[] =& $mform->createElement('button', 'disable_' . $sectionid,
                            get_string('disableall', 'block_like') . $sectionname);
                    } catch (coding_exception $e) {
                        echo 'Exception coding_exception (specific_definition() -> blocks/like/edit_form.php) : ',
                        $e->getMessage(), "\n";
                    }

                    $mform->addGroup($enabledisable, 'manage_checkbox_' . $sectionid, '<br><h4>'.(string)$sectionname.'</h4>',
                        array(' '), false);
                }

                $attributes = ['class' => 'iconlarge activityicon'];
                $icon = $OUTPUT->pix_icon('icon', $activity['modulename'], $activity['type'], $attributes);

                $activityarray = array($mform->createElement('advcheckbox', 'config_moduleselectm' . $activity['id'],
                    '', null, array('group' => $sectionid, 'class' => $activity['type'] . ' check_section_' . $sectionid),
                    array(0, $activity['id'])));

                $mform->addGroup($activityarray, 'config_activity_' . $activity['id'],
                    $icon . format_string($activity['name']), array(' '), false);
            }

            /* Imports */
            $params = array($sectionid, $config->moduletype, $moduleidmax);
            $PAGE->requires->js_call_amd('block_like/module', 'init', $params);
        }
    }
}