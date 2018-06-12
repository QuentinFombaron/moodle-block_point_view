<?php

defined('MOODLE_INTERNAL') || die();

/* [TODO] Fix bug */
require_once(__DIR__.'/../../config.php');

try {
    require_login();
} catch (coding_exception $e) {
    echo 'Exception coding_exception (require_login() -> blocks/like/edit_form.php) : ',  $e->getMessage(), "\n";
} catch (require_login_exception $e) {
    echo 'Exception require_login_exception (require_login() -> blocks/like/edit_form.php) : ',  $e->getMessage(), "\n";
} catch (moodle_exception $e) {
    echo 'Exception moodle_exception (require_login() -> blocks/like/edit_form.php) : ',  $e->getMessage(), "\n";
}

class block_like_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_text', get_string('contentinputlabel', 'block_like'));
        $mform->setDefault('config_text', '');
        $mform->setType('config_text', PARAM_RAW);

        $mform->addElement('text', 'config_footer', get_string('footerinputlabel', 'block_like'));
        $mform->setDefault('config_footer', '');
        $mform->setType('config_footer', PARAM_RAW);
    }

}