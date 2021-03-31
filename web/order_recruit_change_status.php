<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);

$o_seq = $decodedRequest->o_seq;
$r_seq = $decodedRequest->r_seq;
$o_status = $decodedRequest->o_status;

$timestamp = time();

$result = '';

$resultTime = 0;

$resultPartitions = 0;

if($o_status == 'dead'){
    $updateSql = "UPDATE `ORDER` SET o_end_stamp = " . $timestamp . chr(10);
    $updateSql .= "WHERE o_seq = " . $o_seq . chr(10);

    if($dbconn->excute($updateSql)){
        $result = 'success';
    } else {
        $result = 'fail';
    }

} else if($o_status == 'arrival'){

    $nowTimestamp = time();

    $deliveryStartTimestampSelectSql = "SELECT o_end_stamp FROM `ORDER` WHERE o_seq = " . $o_seq;

    $deliveryStartTimeResult = $dbconn->excuteArray($deliveryStartTimestampSelectSql);

    if($deliveryStartTimeResult[0]['o_end_stamp'] != null){
        $deliveryStartTimestamp = $deliveryStartTimeResult[0]['o_end_stamp'];
    }


    $start_date_time = new DateTime(date("h:i:s",$deliveryStartTimestamp));
    $since_start_date_time = $start_date_time->diff(new DateTime(date("h:i:s",$nowTimestamp)));

    $deliveryTimeMinutes = $since_start_date_time->h * 60;
    $deliveryTimeMinutes += $since_start_date_time->i;


/*    $deliveryTimestamp = $nowTimestamp - $deliveryStartTimestamp;

    $deliveryTimeHour = date("h", $deliveryTimestamp);

    $deliveryTimeMinutes = date("i", $deliveryTimestamp);

    $deliveryTimeMinutes += $deliveryTimeHour * 60;*/

    $orderPartitionsCountSql = "SELECT count(o_seq) as count_o_seq FROM `ORDER` WHERE o_flag = " . $o_seq . " OR o_seq = " . $o_seq;

    $orderPartitionsCountResult = $dbconn->excuteArray($orderPartitionsCountSql);

    $orderPartitionsCount = 0;

    if($orderPartitionsCountResult[0]['count_o_seq'] != null){
        $orderPartitionsCount += $orderPartitionsCountResult[0]['count_o_seq'];
    }

/*    $updateDeliveryInfoSql = "UPDATE RESTAURANT SET (r_order_count, r_total_time, r_total_count)" . chr(10);
    $updateDeliveryInfoSql .= "= (r_order_count + ". $orderPartitionsCount .", r_total_time + ". $deliveryTimeMinutes .", r_total_count + 1)" . chr(10);
    $updateDeliveryInfoSql .= "WHERE r_seq = " . $r_seq;*/

    $updateDeliveryInfoSql = "UPDATE RESTAURANT SET r_order_count = r_order_count + " . $orderPartitionsCount .chr(10);
    $updateDeliveryInfoSql .= ", r_total_time = r_total_time + " . $deliveryTimeMinutes . ", r_total_count = r_total_count + 1" . chr(10);
    $updateDeliveryInfoSql .= "WHERE r_seq = " . $r_seq;

    $dbconn->excute($updateDeliveryInfoSql);


    $updateSql = "UPDATE `ORDER` SET o_end_stamp = 1" . chr(10);
    $updateSql .= "WHERE o_seq = " . $o_seq . chr(10);

    $dbconn->excute($updateSql);


    $orderFoodListSql = "SELECT f_seq, count(f_seq) as count_f_seq" . chr(10);
    $orderFoodListSql .= "FROM `ORDER`" . chr(10);
    $orderFoodListSql .= "WHERE f_seq != 1 AND o_flag = " . $o_seq ." OR o_seq = " . $o_seq . chr(10);
    $orderFoodListSql .= "GROUP BY f_seq";

    $orderFoodList = $dbconn->excuteArray($orderFoodListSql);

    if($orderFoodList == true){
        foreach($orderFoodList as $index => $orderFood){
            $count_f_seq = $orderFood['count_f_seq'];
            $f_seq = $orderFood['f_seq'];
            $updateCountSql = "UPDATE `FOOD` SET f_order_count = f_order_count + " . $count_f_seq . chr(10);
            $updateCountSql .= "WHERE f_seq = " . $f_seq;

            $dbconn->excute($updateCountSql);
        }
    }

    $result = 'done';
    $resultPartitions = $orderPartitionsCount;
    $resultTime = $deliveryTimeMinutes;
}


echo json_encode( array(
    'result' => $result,
    'o_seq' => $o_seq,
    'r_seq' => $r_seq,
    'count' => $resultPartitions,
    'time' => $resultTime), JSON_UNESCAPED_UNICODE);

?>