<?

require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$timestamp = time();

$todayDate = date("Y-m-d", $timestamp);

$todayStartTimeStamp = strtotime(date("Y-m-d 00:00:00", $timestamp));

$monthAgoTimestamp= strtotime(date("Y-m-d", strtotime("-1 month", $timestamp)));

$monthAgoDate = date("Y-m-d", strtotime("-1 month", $timestamp));

$dailySql = "SELECT RESTAURANT.r_name, ROUND(RESTAURANT.r_total_time / RESTAURANT.r_total_count) as r_time, DAILY_ORDER.count" . chr(10);
$dailySql .= "FROM `ORDER`," . chr(10);
$dailySql .= "(SELECT o_flag, (count(o_flag) + 1) as count, r_seq" . chr(10);
$dailySql .= "FROM `ORDER`" . chr(10);
$dailySql .= "WHERE o_flag != 0 AND" . chr(10);
$dailySql .= "o_ins_stamp >= ". $todayStartTimeStamp . chr(10);
$dailySql .= "GROUP BY (o_flag)" . chr(10);
$dailySql .= ") DAILY_ORDER" . chr(10);
$dailySql .= "LEFT JOIN RESTAURANT" . chr(10);
$dailySql .= "ON DAILY_ORDER.r_seq = RESTAURANT.r_seq" . chr(10);
$dailySql .= "GROUP BY r_name" . chr(10);
$dailySql .= "ORDER BY count DESC" . chr(10);
$dailySql .= "LIMIT 5" . chr(10);

$monthSql = "SELECT RESTAURANT.r_name, ROUND(RESTAURANT.r_total_time / RESTAURANT.r_total_count) as r_time, MONTH_ORDER.count" . chr(10);
$monthSql .= "FROM `ORDER`," . chr(10);
$monthSql .= "(SELECT o_flag, (count(o_flag) + 1) as count, r_seq" . chr(10);
$monthSql .= "FROM `ORDER`" . chr(10);
$monthSql .= "WHERE o_flag != 0 AND" . chr(10);
$monthSql .= "o_ins_stamp >= " . $monthAgoTimestamp. chr(10);
$monthSql .= "GROUP BY (o_flag)" . chr(10);
$monthSql .= ") MONTH_ORDER" . chr(10);
$monthSql .= "LEFT JOIN RESTAURANT" . chr(10);
$monthSql .= "ON MONTH_ORDER.r_seq = RESTAURANT.r_seq" . chr(10);
$monthSql .= "GROUP BY r_name" . chr(10);
$monthSql .= "ORDER BY count DESC" . chr(10);
$monthSql .= "LIMIT 5" . chr(10);

$dailyResult = $dbconn->excuteArray($dailySql);

$monthResult = $dbconn->excuteArray($monthSql);

if($dailyResult[0]['r_name'] == null) $dailyResult = null;

if($monthResult[0]['r_name'] == null) $monthResult = null;

echo json_encode(array( 'daily'=> $dailyResult, 'monthly'=> $monthResult ), JSON_UNESCAPED_UNICODE);

?>