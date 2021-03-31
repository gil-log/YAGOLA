<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);

$type = $decodedRequest->type;
$target = $decodedRequest->target;

$sql = '';

$result = 'fail';
$message = '실패하였습니다.';
$modal = '';
$o_seq = '';
$r_seq = '';


if($type == 'order'){

    $sql = "DELETE FROM `ORDER` WHERE o_seq = " . $target;

    if($dbconn->excute($sql)){
        $result = 'success';
        $message = '모집 글이 삭제되었습니다.';
        $modal = 'modal_recruit';
    }

} else if ($type == 'comment'){

    $oSeqSql = "SELECT o_seq FROM COMMENT WHERE c_seq = " . $target;

    $o_seq_result = $dbconn->excuteArray($oSeqSql);

    $o_seq = $o_seq_result[0]['o_seq'];

    $r_seq_sql = "SELECT r_seq FROM `ORDER` WHERE o_seq = " . $o_seq;

    $r_seq_result = $dbconn->excuteArray($r_seq_sql);

    $r_seq = $r_seq_result[0]['r_seq'];

    $sql = "DELETE FROM `COMMENT` WHERE c_seq = " . $target;

    if($dbconn->excute($sql)){
        $result = 'success';
        $message = '댓글이 삭제되었습니다.';
    }
}

echo json_encode( array(
    'result' => $result,
    'message' => $message,
    'modal' => $modal,
    'o_seq' => $o_seq,
    'r_seq' => $r_seq), JSON_UNESCAPED_UNICODE );

?>
