<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);

$r_seq = $decodedRequest->r_seq;
$currentPage = $decodedRequest->page;

$offset = $currentPage * 6;

$totalCountSql = "SELECT count(f_seq) as total_menu_count" .chr(10);
$totalCountSql .= "FROM FOOD" . chr(10);
$totalCountSql .= "WHERE r_seq = " . $r_seq;

$totalCountResult = $dbconn->excuteArray($totalCountSql);

if($totalCountResult){
    $totalCount = $totalCountResult[0]['total_menu_count'];
}

$max_page = (ceil($totalCount/6) - 1) > 0 ? (ceil($totalCount/6) - 1) : 0;


$food_sql = "SELECT f_name, f_price, f_img_link, f_order_count FROM FOOD" .chr(10);
$food_sql .= "WHERE r_seq = " . $r_seq . chr(10);
$food_sql .= "LIMIT 6 OFFSET " . $offset;

$food_result = $dbconn->excuteArray($food_sql);

?>

    <button class="menu_pri_btn" onclick="orderNextMenu(<? echo $r_seq; ?>, <? echo $currentPage - 1 > 0 ? $currentPage - 1 : 0; ?>)"><</button>
    <button class="menu_next_btn" onclick="orderNextMenu(<? echo $r_seq; ?>, <? echo $currentPage + 1 <= $max_page ? $currentPage + 1 : $max_page; ?>)">></button>


<?


    foreach($food_result as $index => $menuInfo){

        ?>
        <div class="order_menu_content">
            <img src="<? echo $menuInfo['f_img_link']; ?>">
            <a class="order_menu_name"><? echo $menuInfo['f_name']; ?></a>
            <p class="order_menu_order_count">

            <span style="color:lightgreen">
            <i class="fas fa-won-sign"></i>
            </span>
                <? echo $menuInfo['f_price']; ?>&nbsp;원



            <span style="color:#FFFF99">
            <i class="fas fa-file-invoice-dollar"></i>
            </span>
                <? echo $menuInfo['f_order_count']; ?>&nbsp;건

            </p>
        </div>

        <?

    }
    ?>