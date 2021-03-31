<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);

$text = $decodedRequest->text;
$o_seq = $decodedRequest->o_seq;
$v_ip = $decodedRequest->v_ip;
$r_seq = $decodedRequest->r_seq;

$timestamp = time();

$sql = "INSERT INTO COMMENT(c_content, c_ins_stamp, c_ip, o_seq)" . chr(10);
$sql .= "VALUES('". $text ."', ". $timestamp .", '" . $v_ip. "'," . $o_seq .")";

$response = '';

if($dbconn->excute($sql) == true){
    $response = '댓글 작성 성공 :)';
} else {
    $response = '댓글 작성 실패 :(';
}

echo json_encode(array('result' => $response, 'o_seq' => $o_seq, 'r_seq' => $r_seq));

?>