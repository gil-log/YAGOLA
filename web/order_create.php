<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);

$r_seq = $decodedRequest->r_seq;

$d_seq = $_COOKIE['cookie_department'];

$accessIp = $_SERVER["REMOTE_ADDR"];

$timestamp = time();

$f_seq = 1;
$o_flag = 0;
$o_amount = 0;
$o_end_stamp = 0;


/*echo "r_seq : " . $r_seq . " f_seq : " . $f_seq . " d_seq : " . $d_seq .chr(10);
echo " ip : " . $ip . " timestamp : " . $timestamp . " o_flag : " . $o_flag . " o_amount : " . $o_amount .chr(10);
echo " date : " . date("Y-m-d h:i:s", $timestamp);*/

$sql = "INSERT INTO `ORDER`(o_flag, o_amount, o_end_stamp, o_ins_stamp, o_ip, r_seq, f_seq, d_seq)" . chr(10);
$sql .= "VALUES(" .$o_flag.", ".$o_amount.", ".$o_end_stamp.", ".$timestamp.", '".$accessIp."', ".$r_seq.", ".$f_seq.", ".$d_seq.")";


$result = $dbconn->excute($sql);

if($result == true){
    ?>
    <h2 onmouseover="changeCursor(this, 'default')">주문 모집 글이 생성 되었어용 :)</h2>

    <h1 onmouseover="changeCursor(this, 'pointer')" title="창 닫기!" onclick="closeModal('modal_order_done')"><span style="color: #87302b"><i class="fas fa-utensils">닫기!</i></span></h1>
<?
} else{
 ?>
    <h2><i class="far fa-sad-tear"></i> 글 생성 실패... 거 미안하게 됐수다.. :( <i class="far fa-sad-tear"></i></h2>

<?
}


?>