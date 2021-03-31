<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$sql = "";

$count = 5;
$sql = "SELECT r_seq, r_name, r_img_link, round(r_total_time / r_total_count) as r_time, r_order_count".chr(10);
$sql .= "FROM RESTAURANT" .chr(10);
$sql .= "WHERE r_order_count != 0" .chr(10);
$sql .= "ORDER BY r_order_count DESC" .chr(10);
$sql .= "LIMIT 20";


$result = $dbconn->excuteArray($sql);

$img_prefix = "https://www.yogiyo.co.kr";

foreach($result as $index => $recommendInfo){
    $count--;
    if($count <0){
        ?>
        <div class="content_next" onmouseover="changeCursor(this, 'pointer')" title="식당 정보를 확인 할래여 :)" onmouseenter="clearInterval(playRestaurantSlide)" onmouseleave="startSlide('restaurant', 3000)" onclick="clickContent('<? echo $recommendInfo['r_seq']; ?>')">
            <img src="<? echo $img_prefix.$recommendInfo['r_img_link']; ?>">
            <p class="menu_name"><? echo $recommendInfo['r_name']; ?></p>
            <p class="menu_order_count">

                <span style="color:#bf601b">
                <i class="fas fa-stopwatch">&nbsp;</i>
                </span>
                <? echo $recommendInfo['r_time']; ?>&nbsp;분

                &nbsp;&nbsp;

                <span style="color:#FFFF99">
                <i class="fas fa-file-invoice-dollar">&nbsp;</i>
                </span>
                <? echo $recommendInfo['r_order_count']; ?>&nbsp;건

            </p>
        </div>
        <?
    } else{
        ?>
        <div class="content" onmouseover="changeCursor(this, 'pointer')" title="식당 정보를 확인 할래여 :)" onmouseenter="clearInterval(playRestaurantSlide)" onmouseleave="startSlide('restaurant', 3000)" onclick="clickContent('<? echo $recommendInfo['r_seq']; ?>')">
            <img src="<? echo $img_prefix.$recommendInfo['r_img_link']; ?>">
            <p class="menu_name"><? echo $recommendInfo['r_name']; ?></p>
            <p class="menu_order_count">

                <span style="color:#bf601b">
                <i class="fas fa-stopwatch">&nbsp;</i>
                </span>
                <? echo $recommendInfo['r_time']; ?>&nbsp;분

                &nbsp;&nbsp;

                <span style="color:#FFFF99">
                <i class="fas fa-file-invoice-dollar">&nbsp;</i>
                </span>
                <? echo $recommendInfo['r_order_count']; ?>&nbsp;건

            </p>
        </div>

        <?
    }

}

?>