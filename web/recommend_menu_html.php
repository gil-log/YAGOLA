<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);


$sql = "";

$count = 4;
$sql = "SELECT f_order_count, f_name, f_price, f_img_link, f_seq, r_seq" .chr(10);
$sql .= "FROM FOOD" .chr(10);
$sql .= "WHERE f_order_count != 0" .chr(10);
$sql .= "ORDER BY f_order_count DESC".chr(10);
$sql .= "LIMIT 20";

$result = $dbconn->excuteArray($sql);

foreach($result as $index => $recommendInfo){
    $count--;
    if($count <0){
        ?>
        <div class="content_next" onmouseover="changeCursor(this, 'pointer')" title="식당 정보를 확인 할래여 :)" onmouseenter="clearInterval(playMenuSlide)" onmouseleave="startSlide('menu', 3000)" onclick="clickContent('<? echo $recommendInfo['r_seq']; ?>')">
            <img src="<? echo $recommendInfo['f_img_link']; ?>">
            <p class="menu_name"><? echo $recommendInfo['f_name']; ?></p>
            <p class="menu_order_count">

                <span style="color:lightgreen">
                <i class="fas fa-won-sign">&nbsp;</i>
                </span>
                <? echo $recommendInfo['f_price']; ?>&nbsp;원

                &nbsp;&nbsp;

                <span style="color:#FFFF99">
                <i class="fas fa-file-invoice-dollar">&nbsp;</i>
                </span>
                <? echo $recommendInfo['f_order_count']; ?>&nbsp;건

            </p>
        </div>
        <?
    } else{
        ?>
        <div class="content" onmouseover="changeCursor(this, 'pointer')" title="식당 정보를 확인 할래여 :)" onmouseenter="clearInterval(playMenuSlide)" onmouseleave="startSlide('menu', 3000)" onclick="clickContent('<? echo $recommendInfo['r_seq']; ?>')">
            <img src="<? echo $recommendInfo['f_img_link']; ?>">
            <p class="menu_name"><? echo $recommendInfo['f_name']; ?></p>
            <p class="menu_order_count">

                <span style="color:lightgreen">
                <i class="fas fa-won-sign">&nbsp;</i>
                </span>
                <? echo $recommendInfo['f_price']; ?>&nbsp;원

                &nbsp;&nbsp;

                <span style="color:#FFFF99">
                <i class="fas fa-file-invoice-dollar">&nbsp;</i>
                </span>
                <? echo $recommendInfo['f_order_count']; ?>&nbsp;건

            </p>
        </div>

        <?
    }

}

?>