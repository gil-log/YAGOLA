<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);

$o_seq = $decodedRequest->o_seq;
$r_seq = $decodedRequest->r_seq;
$f_seq = $decodedRequest->f_seq;
$d_seq = $decodedRequest->d_seq;
$o_ip = $decodedRequest->o_ip;

$timestamp = time();

$cookie_d_seq = $_COOKIE['cookie_department'];

$visitor_ip = $_SERVER["REMOTE_ADDR"];

$responseMessage = '';

if($visitor_ip != $o_ip){
    $isAlreadyChooseSql = "SELECT o_seq" . chr(10);
    $isAlreadyChooseSql .= "FROM `ORDER`" . chr(10);
    $isAlreadyChooseSql .= "WHERE o_flag = " . $o_seq .chr(10);
    $isAlreadyChooseSql .= "AND o_ip = '" .$visitor_ip ."'";

    $isAlreadyChooseResult = $dbconn->excuteArray($isAlreadyChooseSql);

    //echo "ㅋㅌㅊㅌㅋㅊ" . $o_ip . "  " . $visitor_ip .$isAlreadyChooseResult[0]['o_seq'];

    if(isset($isAlreadyChooseResult[0]['o_seq'])){
        $visitor_o_seq = $isAlreadyChooseResult[0]['o_seq'];

        $updateSql = "UPDATE `ORDER` SET f_seq = " . $f_seq . chr(10);
        $updateSql .= "WHERE o_seq = " . $visitor_o_seq;

        if($dbconn->excute($updateSql)){
            $responseMessage = '선택 메뉴로 변경 되었습니다.';
        } else {
            $responseMessage = '실패 했어여.. 미안해여.. :(';
        }

    } else {
        $insertSql = "INSERT INTO `ORDER`(o_flag, o_amount, o_end_stamp, o_ins_stamp, o_ip, r_seq, f_seq, d_seq)" . chr(10);
        $insertSql .= "VALUES(". $o_seq . ", " . 1 .", " . 0 . ", " . $timestamp . ", '" . $visitor_ip . "', " . $r_seq . ", " . $f_seq . ", " . $d_seq . ")";

        if($dbconn->excute($insertSql)){
            $responseMessage = '선택 메뉴가 신청 되었습니다.';
        } else {
            $responseMessage = '실패 했어여.. 미안해여.. :(';
        }

    }

} else{
    $updateSql = "UPDATE `ORDER` SET f_seq = " . $f_seq . chr(10);
    $updateSql .= "WHERE o_seq = " . $o_seq;

    if($dbconn->excute($updateSql)){
        $responseMessage = '선택 메뉴로 변경 되었습니다.';
    } else {
        $responseMessage = '실패 했어여.. 미안해여.. :(';
    }
}

echo json_encode( array('ip'=> $visitor_ip, 'result' => $responseMessage, 'o_seq' => $o_seq, 'r_seq' => $r_seq), JSON_UNESCAPED_UNICODE );
?>