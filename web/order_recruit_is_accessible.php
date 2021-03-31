<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);

$o_seq = $decodedRequest->o_seq;
$r_seq = $decodedRequest->r_seq;
$page = $decodedRequest->page;

$visitor_ip = $_SERVER["REMOTE_ADDR"];

$sql = "SELECT o_end_stamp FROM `ORDER` WHERE o_seq = " . $o_seq;

$result = $dbconn->excuteArray($sql);

$isAccessible = 'no';

$comment = '';

if($result[0]['o_end_stamp'] != null){
    $o_end_stamp = $result[0]['o_end_stamp'];

    if($o_end_stamp == 0){
        $isAccessible = 'ok';
        $comment = '모집 중';
    } else{

        $isParticipantSql = "SELECT DISTINCT(o_ip) FROM `ORDER` WHERE o_flag = " . $o_seq ." OR o_seq = " .$o_seq;

        $participantsResult = $dbconn->excuteArray($isParticipantSql);

        $arrParticipantsIP = array();

        if($participantsResult[0]['o_ip'] != null){

            foreach($participantsResult as $index => $participant){
                $ip = $participant['o_ip'];
                array_push($arrParticipantsIP, $ip);
            }
        }

        if ($o_end_stamp == 1){

            $comment = '도착 완료 주문 입니다.';

            if(in_array($visitor_ip, $arrParticipantsIP)){
                $isAccessible = 'ok';
            }

        } else{

            $comment = '마감된 주문 입니다.';

            if(in_array($visitor_ip, $arrParticipantsIP)){
                $isAccessible = 'ok';
            }

        }
    }

}

echo json_encode( array( 'result' => $isAccessible, 'comment' => $comment, 'o_seq' => $o_seq, 'r_seq' => $r_seq, 'page' => $page ), JSON_UNESCAPED_UNICODE);

?>