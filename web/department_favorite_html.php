<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);

$d_seq = $decodedRequest->d_seq;

$d_name = '';

$sql = "SELECT d_name FROM DEPARTMENT" . chr(10);
$sql .= "WHERE d_seq = " . $d_seq;

$result = $dbconn->excuteArray($sql);
if($result == true){
    $d_name = $result[0]['d_name'];

    echo $d_name;
    ?>
    <span style="color: #87302b">
    <i class="far fa-times-circle">
    </i>
    </span>

<?
}
?>


