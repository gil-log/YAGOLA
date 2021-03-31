<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);

$r_seq = $decodedRequest->r_seq;
$currentPage = $decodedRequest->page;

$offset = $currentPage * 6;

$totalCountSql = "SELECT count(f_seq) as total_menu_count" . chr(10);
$totalCountSql .= "FROM FOOD" . chr(10);
$totalCountSql .= "WHERE r_seq = " . $r_seq . chr(10);

$totalCountResult = $dbconn->excuteArray($totalCountSql);

if ($totalCountResult) {
    $totalCount = $totalCountResult[0]['total_menu_count'];
}

$max_page = (ceil($totalCount / 6) - 1) > 0 ? (ceil($totalCount / 6) - 1) : 0;


$sql = "";

$sql = "SELECT r_seq, r_name, r_tel, r_order_count, round(r_total_time / r_total_count) as r_time" . chr(10);
$sql .= "FROM RESTAURANT" . chr(10);
$sql .= "WHERE r_seq = " . $r_seq;

$result = $dbconn->excuteArray($sql);

$r_seq = $result[0]['r_seq'];
$r_tel = $result[0]['r_tel'];
$r_name = $result[0]['r_name'];
$r_order_count = $result[0]['r_order_count'];
$r_time = $result[0]['r_time'];

$depart_seq = $_COOKIE['cookie_department'];

$department_sql = "SELECT d_name FROM DEPARTMENT" . chr(10);
$department_sql .= "WHERE d_seq = " . $depart_seq;

$department_info_result = $dbconn->excuteArray($department_sql);

$department_name = null;

if ($department_info_result == true) {
    $department_name = $department_info_result[0]['d_name'];
}

$food_sql = "SELECT f_name, f_price, f_img_link, f_order_count FROM FOOD" . chr(10);
$food_sql .= "WHERE r_seq = " . $r_seq . chr(10);
$food_sql .= "LIMIT 6 OFFSET " . $offset;

$food_result = $dbconn->excuteArray($food_sql);

?>

<div class="order_title">

    <a class="restaurant_title">
        <? echo $r_name; ?></a>

    <div class="restaurant_info_area">

        <p>
            <i class="fas fa-stopwatch">&nbsp;</i>
            <? echo $r_time; ?>분&nbsp;&nbsp;
            <i class="fas fa-file-invoice-dollar">&nbsp;&nbsp;</i><? echo $r_order_count; ?>건&nbsp;&nbsp;

            <i class="fas fa-phone-alt">&nbsp;</i> <? echo $r_tel ?>
        </p>
    </div>

</div>

<div class="menu_area">

    <h2>메뉴 목록</h2>


    <div class="order_menu_area" id="order_menu_area">
        <button class="menu_pri_btn"
                onclick="orderNextMenu(<? echo $r_seq; ?>, <? echo $currentPage - 1 > 0 ? $currentPage - 1 : 0; ?>)"><
        </button>
        <button class="menu_next_btn"
                onclick="orderNextMenu(<? echo $r_seq; ?>, <? echo $currentPage + 1 <= $max_page ? $currentPage + 1 : $max_page; ?>)">
            >
        </button>


        <?
        foreach ($food_result as $index => $menuInfo) {
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
    </div>
</div>

<div class="department_area">

    <h2>모집 부서</h2>
    <a class="restaurant_title">
        <? echo $department_name; ?></a>
    <!--    <input type="text" id="order_department" placeholder="모집 부서 골라" value="<? /* if($department_name == true) echo ;*/ ?>">
-->
</div>


<div class="order_button_area">
    <button type="button" class="order_cancel_btn" onclick="cancelModal('modal_order')">취소</button>
    <button type="button" class="order_ok_btn" onclick="createOrder(<? echo $r_seq; ?>)">모집</button>
</div>
