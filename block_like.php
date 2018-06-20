<?php// This file is part of Moodle - http://moodle.org///// Moodle is free software: you can redistribute it and/or modify// it under the terms of the GNU General Public License as published by// the Free Software Foundation, either version 3 of the License, or// (at your option) any later version.//// Moodle is distributed in the hope that it will be useful,// but WITHOUT ANY WARRANTY; without even the implied warranty of// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the// GNU General Public License for more details.//// You should have received a copy of the GNU General Public License// along with Moodle.  If not, see <http://www.gnu.org/licenses/>./** * ##Description## [TODO] * * * @package    block_like * @copyright  [TODO] * @author     [TODO] * @license    [TODO] */defined('MOODLE_INTERNAL') || die();require_once(__DIR__ . '/../../config.php');try {    require_login();} catch (coding_exception $e) {    echo 'Exception coding_exception (require_login() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";} catch (require_login_exception $e) {    echo 'Exception require_login_exception (require_login() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";} catch (moodle_exception $e) {    echo 'Exception moodle_exception (require_login() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";}class block_like extends block_base{    /**     * Block initializations     * @throws coding_exception     */    public function init() {        $this->title = get_string('pluginname', 'block_like');    }    /**     * We have global config/settings data.     * @return bool     */    public function has_config() {        return true;    }    /**     *     * @return Object     */    public function get_content() {        global $USER, $CFG, $DB, $COURSE;        /* CSS import */        try {            $this->page->requires->css(new moodle_url($CFG->wwwroot . '/blocks/like/style/style.css'));        } catch (coding_exception $e) {            echo 'Exception coding_exception (get_content() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";        } catch (moodle_exception $e) {            echo 'Exception moodle_exception (get_content() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";        }        /* Template generation */        if ($this->content !== null) {            return $this->content;        }        if (isset($this->config)) {            if (empty($this->config->text)) {                try {                    $this->content->text = get_string('defaulttext', 'block_like');                } catch (coding_exception $e) {                    echo 'Exception coding_exception (specialization() -> blocks/like/block_like.php) : ', $e->getMessage(), "\n";                }            } else {                $this->content->text = $this->config->text;            }        }        $sql = 'SELECT Base.cmid, Base.type,             IFNULL(COUNT(Base.cmid), 0) AS total,            IFNULL(TableTypeOne.TotalTypeOne, 0) AS typeone,            IFNULL(TableTypeTwo.TotalTypeTwo, 0) AS typetwo,            IFNULL(TableTypeThree.TotalTypethree, 0) AS typethree,            IFNULL(TableUser.UserVote, 0) AS uservote          FROM {block_like} AS Base            NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypeOne FROM {block_like}              WHERE vote = 1 GROUP BY cmid) AS TableTypeOne            NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypeTwo FROM {block_like}              WHERE vote = 2 GROUP BY cmid) AS TableTypeTwo            NATURAL LEFT JOIN (SELECT cmid, COUNT(vote) AS TotalTypethree FROM {block_like}              WHERE vote = 3 GROUP BY cmid) AS TableTypeThree            NATURAL LEFT JOIN (SELECT cmid, vote AS UserVote FROM mdl_block_like WHERE userid = :userid) AS TableUser          GROUP BY cmid;';        $params = array('userid' => $USER->id);        try {            $result = $DB->get_records_sql($sql, $params);        } catch (dml_exception $e) {            echo 'Exception : ', $e->getMessage(), "\n";        }        /* Parameters for the Javascript */        $likes = (!empty($result)) ? array_values($result) : array();        $moduleselect = array();        $minmaxidsql = 'SELECT MIN(id) AS minid, MAX(id) AS maxid FROM {course_modules} WHERE course = :course;';        $minmaxidparams = array('course' => $COURSE->id);        try {            $minmaxid = array_values($DB->get_records_sql($minmaxidsql, $minmaxidparams))[0];        } catch (dml_exception $e) {            echo 'Exception : ', $e->getMessage(), "\n";        }        for ($i = intval($minmaxid->minid); $i <= intval($minmaxid->maxid); $i++) {            if ($this->config->{'moduleselectm' . $i} != 0) {                array_push($moduleselect, $i);            }        }        $paramsamd = array($likes, $USER->id, $moduleselect);        $this->page->requires->js_call_amd('block_like/script', 'init', $paramsamd);        return $this->content;    }}