<?php

require_once(__DIR__.'/../../config.php');

global $DB;

$sql = 'SELECT cmid FROM {block_like} WHERE userid = :userid';
$params = array('userid' => '2');
try {
    $response = $DB->get_records_sql($sql, $params);
} catch (dml_exception $e) {
    echo 'Exception : ',  $e->getMessage(), "\n";
}

$arraysql = array_values($response);
foreach ($arraysql as $id => $value) {
    echo json_encode($value->cmid);
}