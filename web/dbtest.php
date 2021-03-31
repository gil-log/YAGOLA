<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<?
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);
$sql = "SELECT * FROM DEPARTMENT";

$result = $dbconn->excuteArray($sql);

print_r($result);


$insSql = "INSERT INTO RESTAURANT(r_name, r_api_id, r_img_link, r_total_time, r_category)
VALUES('애슐리딜리버리-일산대화점', 502400, '/media/franchise_logos/애슐리_20201006_Franchise_crop_200x200.jpg', 70, 'wes')";

$dbconn->excute($insSql);

?>