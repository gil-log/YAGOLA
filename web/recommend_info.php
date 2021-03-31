<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);

$recommend_flag = $decodedRequest->recommend_flag;

$sql = "";

if($recommend_flag == "food"){
    $sql = "SELECT f_order_count, f_name, f_price, f_img_link, f_seq, r_seq" .chr(10);
    $sql .= "FROM FOOD" .chr(10);
    $sql .= "WHERE f_order_count != 0" .chr(10);
    $sql .= "ORDER BY f_order_count DESC".chr(10);
    $sql .= "LIMIT 20";
}else{
    $sql = "SELECT r_seq, r_name, r_img_link, round(r_total_time / r_total_count) as r_time, r_order_count".chr(10);
    $sql .= "FROM RESTAURANT" .chr(10);
    $sql .= "WHERE r_order_count != 0" .chr(10);
    $sql .= "ORDER BY r_order_count DESC" .chr(10);
    $sql .= "LIMIT 20";
}

$result = $dbconn->excuteArray($sql);

echo (json_encode(array("recommend_flag" => $recommend_flag, "result" => $result)));

?>