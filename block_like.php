<?php// This file is part of Moodle - http://moodle.org///// Moodle is free software: you can redistribute it and/or modify// it under the terms of the GNU General Public License as published by// the Free Software Foundation, either version 3 of the License, or// (at your option) any later version.//// Moodle is distributed in the hope that it will be useful,// but WITHOUT ANY WARRANTY; without even the implied warranty of// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the// GNU General Public License for more details.//// You should have received a copy of the GNU General Public License// along with Moodle.  If not, see <http://www.gnu.org/licenses/>./** * ##Description## [TODO] * * * @package    block_like * @copyright  [TODO] * @author     [TODO] * @license    [TODO] */defined('MOODLE_INTERNAL') || die();require_once(__DIR__ . '/../../config.php');require_once($CFG->dirroot . '/blocks/like/lib.php');try {    require_login();} catch (coding_exception $e) {    echo 'Exception coding_exception (require_login() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";} catch (require_login_exception $e) {    echo 'Exception require_login_exception (require_login() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";} catch (moodle_exception $e) {    echo 'Exception moodle_exception (require_login() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";}class block_like extends block_base{    /**     * Block initializations     * @throws coding_exception     */    public function init() {        $this->title = get_string('config_default_title', 'block_like');    }    /**     * We have global config/settings data.     * @return bool     */    public function has_config() {        return true;    }    /**     *     * @return Object     * @throws dml_exception     * @throws coding_exception     * @throws moodle_exception     */    public function get_content() {        global $PAGE, $USER, $CFG, $DB, $COURSE;        if (get_config('block_like', 'enable_likes_admin')) {            /* Template generation */            if ($this->content !== null) {                return $this->content;            }            if (isset($this->config)) {                if (empty($this->config->text)) {                    $this->content = new stdClass();                    try {                        $this->content->text = get_string('defaulttextcontent', 'block_like');                    } catch (coding_exception $e) {                        echo 'Exception coding_exception (specialization() -> blocks/like/block_like.php) : ',                        $e->getMessage(), "\n";                    }                } else {                    $this->content->text = $this->config->text;                }            } else {                $this->content = new stdClass();                $this->content->text = get_string('defaulttextcontent', 'block_like');            }            if (has_capability('block/like:overview', context_system::instance())) {                $parameters = [                    'instanceid' => $this->instance->id,                    'contextid' => $this->context->id,                    'courseid' => $COURSE->id,                    'sesskey' => sesskey(),                    'enablepix' => $this->config->enable_pix_checkbox                ];                $url = new moodle_url('/blocks/like/menu.php', $parameters);                $this->content->text .= html_writer::start_tag('div', array('class' => 'menu_like'));                $this->content->text .= html_writer::link(                    $url,                    '<img src="' . $CFG->wwwroot . '/blocks/like/pix/overview.png" id="menu_like_img"/>');                $this->content->text .= html_writer::end_tag('div');            }            /* CSS import */            try {                $this->page->requires->css(new moodle_url($CFG->wwwroot . '/blocks/like/style/style.css'));            } catch (coding_exception $e) {                echo 'Exception coding_exception (get_content() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";            } catch (moodle_exception $e) {                echo 'Exception moodle_exception (get_content() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";            }            if (!$PAGE->user_is_editing()) {                if ($this->config->enable_likes_checkbox) {                    $sql = 'SELECT Base.cmid,                    IFNULL(COUNT(Base.cmid), 0) AS total,                    IFNULL(TableTypeOne.TotalTypeOne, 0) AS typeone,                    IFNULL(TableTypeTwo.TotalTypeTwo, 0) AS typetwo,                    IFNULL(TableTypeThree.TotalTypethree, 0) AS typethree,                    IFNULL(TableUser.UserVote, 0) AS uservote                  FROM {block_like} AS Base                    NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypeOne FROM {block_like}                      WHERE vote = 1 GROUP BY cmid) AS TableTypeOne                    NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypeTwo FROM {block_like}                      WHERE vote = 2 GROUP BY cmid) AS TableTypeTwo                    NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypethree FROM {block_like}                      WHERE vote = 3 GROUP BY cmid) AS TableTypeThree                    NATURAL LEFT JOIN (SELECT cmid, vote AS UserVote FROM {block_like} WHERE userid = :userid) AS TableUser                    WHERE courseid = :courseid                  GROUP BY cmid;';                    $params = array('userid' => $USER->id, 'courseid' => $COURSE->id);                    try {                        $result = $DB->get_records_sql($sql, $params);                    } catch (dml_exception $e) {                        echo 'Exception : ', $e->getMessage(), "\n";                    }                    /* Parameters for the Javascript */                    $likes = (!empty($result)) ? array_values($result) : array();                }                try {                    $sqlid = $DB->get_records('course_modules', array('course' => $COURSE->id), null, 'id');                } catch (dml_exception $e) {                    echo 'Exception dml_exception (get_content() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";                }                $moduleselect = array();                $difficultylevels = array();                foreach ($sqlid as $row) {                    if (isset($this->config->{'moduleselectm' . $row->id})) {                        if ($this->config->{'moduleselectm' . $row->id} != 0 && $this->config->enable_likes_checkbox) {                            array_push($moduleselect, $row->id);                        }                        if ($this->config->enable_difficulties_checkbox) {                            $difficultylevels[$row->id] = $this->config->{'difficulty_' . $row->id};                        }                    }                }                $pixparam = array(                    'easy' => $CFG->wwwroot . '/blocks/like/pix/easy.png',                    'easytxt' => $this->config->text_easy,                    'better' => $CFG->wwwroot . '/blocks/like/pix/better.png',                    'bettertxt' => $this->config->text_better,                    'hard' => $CFG->wwwroot . '/blocks/like/pix/hard.png',                    'hardtxt' => $this->config->text_hard,                    'group_' => $CFG->wwwroot . '/blocks/like/pix/group_.png',                    'group_E' => $CFG->wwwroot . '/blocks/like/pix/group_E.png',                    'group_B' => $CFG->wwwroot . '/blocks/like/pix/group_B.png',                    'group_H' => $CFG->wwwroot . '/blocks/like/pix/group_H.png',                    'group_EB' => $CFG->wwwroot . '/blocks/like/pix/group_EB.png',                    'group_EH' => $CFG->wwwroot . '/blocks/like/pix/group_EH.png',                    'group_BH' => $CFG->wwwroot . '/blocks/like/pix/group_BH.png',                    'group_EBH' => $CFG->wwwroot . '/blocks/like/pix/group_EBH.png',                );                $pixfiles = array(                    'easy',                    'better',                    'hard',                    'group_',                    'group_E',                    'group_B',                    'group_H',                    'group_EB',                    'group_EH',                    'group_BH',                    'group_EBH'                );                $fs = get_file_storage();                if (get_config('block_like', 'enable_pix_admin')) {                    foreach ($pixfiles as $file) {                        if ($fs->file_exists(1, 'block_like', 'likes_pix_admin', 0, '/', $file . '.png')) {                            $pixparam[$file] = block_like_pix_url(1, 'likes_pix_admin', $file);                        }                    }                } else {                    $fs->delete_area_files(1, 'block_like');                }                if ($this->config->enable_pix_checkbox) {                    foreach ($pixfiles as $file) {                        if ($fs->file_exists($this->context->id, 'block_like', 'likes_pix', 0, '/', $file . '.png')) {                            $pixparam[$file] = block_like_pix_url($this->context->id, 'likes_pix', $file);                        }                    }                } else {                    $fs->delete_area_files($this->context->id, 'block_like');                }                $envconf = array(                    'greentrack' => get_config('block_like', 'green_track_color_admin'),                    'bluetrack' => get_config('block_like', 'blue_track_color_admin'),                    'redtrack' => get_config('block_like', 'red_track_color_admin'),                    'blacktrack' => get_config('block_like', 'black_track_color_admin'),                    'userid' => $USER->id,                    'courseid' => $COURSE->id                );                $paramsamd = array($likes, $moduleselect, $difficultylevels, $pixparam, $envconf);                $this->page->requires->js_call_amd('block_like/script_like', 'init', $paramsamd);            }        } else {            $this->content->text = get_string('blockdisabled', 'block_like');        }        return $this->content;    }    /**     * Serialize and store config data.     *     * @param mixed $data     * @param mixed $nolongerused     */    public function instance_config_save($data, $nolongerused = false) {        $fs = get_file_storage();        $config = clone $data;        if ($config->enable_pix_checkbox) {            $config->likes_pix = file_save_draft_area_files(                $data->likes_pix,                $this->context->id,                'block_like',                'likes_pix',                0            );        } else {            $fs->delete_area_files($this->context->id, 'block_like');        }        parent::instance_config_save($config, $nolongerused);    }    /**     * @return bool     */    public function instance_delete() {        $fs = get_file_storage();        $fs->delete_area_files($this->context->id, 'block_like');        return true;    }    /**     * Copy any block-specific data when copying to a new block instance.     * @param int $fromid the id number of the block instance to copy from     * @return boolean     */    public function instance_copy($fromid) {        $fromcontext = context_block::instance($fromid);        $fs = get_file_storage();        if (!$fs->is_area_empty($fromcontext->id, 'block_like', 'likes_pix', 0, false)) {            $draftitemid = 0;            file_prepare_draft_area($draftitemid, $fromcontext->id, 'block_like', 'likes_pix', 0, array('subdirs' => true));            file_save_draft_area_files($draftitemid, $this->context->id, 'block_like', 'likes_pix', 0, array('subdirs' => true));        }        return true;    }}