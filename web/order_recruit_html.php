<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);

$r_seq = $decodedRequest->r_seq;
$currentPage = $decodedRequest->page;

$master_o_seq = $decodedRequest->o_seq;

$visitor_ip = $_SERVER["REMOTE_ADDR"];


$offset = $currentPage * 10;

$totalCountSql = "SELECT count(f_seq) as total_menu_count" . chr(10);
$totalCountSql .= "FROM FOOD" . chr(10);
$totalCountSql .= "WHERE r_seq = " . $r_seq . chr(10);

$totalCountResult = $dbconn->excuteArray($totalCountSql);

if ($totalCountResult) {
    $totalCount = $totalCountResult[0]['total_menu_count'];
}

$max_page = (ceil($totalCount / 10) - 1) > 0 ? (ceil($totalCount / 10) - 1) : 0;


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

$visitor_depart_seq = $_COOKIE['cookie_department'];

$visitor_department_sql = "SELECT d_name FROM DEPARTMENT" . chr(10);
$visitor_department_sql .= "WHERE d_seq = " . $visitor_depart_seq;

$visitor_department_info_result = $dbconn->excuteArray($visitor_department_sql);

$visitor_department_name = null;

if ($visitor_department_info_result == true) {
    $visitor_department_name = $visitor_department_info_result[0]['d_name'];
}

$food_sql = "SELECT f_seq, f_name, f_price, f_img_link, f_order_count FROM FOOD" . chr(10);
$food_sql .= "WHERE r_seq = " . $r_seq . chr(10);
$food_sql .= "LIMIT 10 OFFSET " . $offset;

$food_result = $dbconn->excuteArray($food_sql);

$masterInfoSql = "SELECT `ORDER`.o_flag, `ORDER`.o_end_stamp, `ORDER`.o_ins_stamp, `ORDER`.o_ip, `ORDER`.d_seq, `DEPARTMENT`.d_name" . chr(10);

$masterInfoSql .= "FROM" . chr(10);

$masterInfoSql .= "(SELECT o_flag, o_end_stamp, o_ins_stamp, o_ip, d_seq" . chr(10);
$masterInfoSql .= "FROM `ORDER`" . chr(10);
$masterInfoSql .= "WHERE `ORDER`.o_seq = " . $master_o_seq . ") `ORDER`" . chr(10);

$masterInfoSql .= "LEFT JOIN `DEPARTMENT`" . chr(10);
$masterInfoSql .= "ON `ORDER`.d_seq = `DEPARTMENT`.d_seq" . chr(10);

$masterInfoResult = $dbconn->excuteArray($masterInfoSql);

$master_o_flag = '';
$master_o_end_stamp = '';
$master_o_ins_stamp = '';
$master_o_ip = '';
$master_d_seq = '';
$master_d_name = '';

if ($masterInfoResult == true) {
    $master_o_flag = $masterInfoResult[0]['o_flag'];
    $master_o_end_stamp = $masterInfoResult[0]['o_end_stamp'];
    $master_o_ins_stamp = $masterInfoResult[0]['o_ins_stamp'];
    $master_o_ip = $masterInfoResult[0]['o_ip'];
    $master_d_seq = $masterInfoResult[0]['d_seq'];
    $master_d_name = $masterInfoResult[0]['d_name'];
}

$totalOrderInfoSql = "SELECT `FOOD`.f_name, `FOOD`.f_price, `ORDER`.f_seq, `ORDER`.o_amount, (`FOOD`.f_price * `ORDER`.o_amount) as sum_f_price" . chr(10);
$totalOrderInfoSql .= "FROM" . chr(10);
$totalOrderInfoSql .= "(SELECT f_seq, count(o_amount) as o_amount" . chr(10);

$totalOrderInfoSql .= "FROM `ORDER`" . chr(10);
$totalOrderInfoSql .= "WHERE o_flag = " . $master_o_seq . " OR o_seq = " . $master_o_seq .chr(10);
$totalOrderInfoSql .= "GROUP BY f_seq) `ORDER`" . chr(10);

$totalOrderInfoSql .= "LEFT JOIN `FOOD`" . chr(10);
$totalOrderInfoSql .= "ON `ORDER`.f_seq = `FOOD`.f_seq" . chr(10);

$totalOrderInfoSql .= "WHERE `FOOD`.f_seq != 1" . chr(10);

$totalOrderInfoSql .= "ORDER BY o_amount DESC" . chr(10);

$totalOrderInfoResult = $dbconn->excuteArray($totalOrderInfoSql);


$commentSql = "SELECT c_seq, c_content, c_ins_stamp, c_ip" . chr(10);
$commentSql .= "FROM COMMENT" . chr(10);
$commentSql .= "WHERE o_seq = " . $master_o_seq . chr(10);
$commentSql .= "ORDER BY c_ins_stamp DESC";


$commentResult = $dbconn->excuteArray($commentSql);







?>

<div class="order_title">

    <a class="restaurant_title">
        <? echo $r_name; ?></a>

    <div class="restaurant_info_area">

        <p>
            <i class="fas fa-stopwatch">&nbsp;</i>
            <? echo $r_time; ?>분&nbsp;&nbsp;
            <i class="fas fa-file-invoice-dollar">&nbsp;&nbsp;</i><? echo $r_order_count; ?>건&nbsp;&nbsp;
            <i class="far fa-building"></i>&nbsp;&nbsp;<? echo $master_d_name; ?>&nbsp;&nbsp;
            <i class="fas fa-phone-alt">&nbsp;</i> <? echo $r_tel ?>
        </p>

        <?

        $master_status = '';
        if ($master_o_end_stamp == 0){
            $master_status = '모집';
        } else if($master_o_end_stamp == 1){
            $master_status = '도착';
        } else {
            $master_status = '마감';
        }
        if($master_o_ip != $visitor_ip){
            ?>

           <p>
           <h2 style="color:lightgreen;">
           <? echo $master_status; ?> </h2></p>

        <?
        } else {
        $master_next_status = '';
        $master_status_flag = '';
        $button_background_color = '';
        if($master_status == '모집'){
        $master_status_flag = 'dead';
        $master_next_status = '마감하기';
        $button_background_color = "#87302b";
        } else if($master_status == '마감'){
        $master_status_flag = 'arrival';
        $master_next_status = '도착하기';
        $button_background_color = "yellow";
        }

        ?>

                    <p><div class="order_button_area">
    <button type="button" class="recruit_btn" style="float:left; background-color: <? echo $button_background_color; ?>;" onclick="changeRecruitStatus('<? echo $master_o_seq; ?>', '<? echo $r_seq; ?>', '<? echo $master_status_flag; ?>')"><? echo $master_next_status; ?></button>

</div></p>


<button type="button" class="recruit_btn" style="float:right; background-color: black; color: white;" onclick="deleteContent('order', '<? echo $master_o_seq;?>')">삭제 하기</button>

<h2 style="color:lightgreen;">
           <? echo $master_status; ?> </h2>
        <?
        }
        ?>


    </div>

</div>

<div class="menu_area">



    <div class="order_menu_area_recruit" id="order_menu_area">

    <h2 style="height: 10px;"><?echo (($max_page+1) * 10) . "개 메뉴 중 " . (($currentPage+1)*10) . "개 까지 봤오여 :)" ?></h2>


        <button class="menu_pri_btn"
                onclick="orderRecruitNextMenu(<? echo $r_seq; ?>, <? echo $currentPage - 1 > 0 ? $currentPage - 1 : 0; ?>, '<? echo $master_o_seq; ?>', '<? echo $visitor_depart_seq; ?>', '<? echo $master_o_ip; ?>')">
            <
        </button>
        <button class="menu_next_btn"
                onclick="orderRecruitNextMenu(<? echo $r_seq; ?>, <? echo $currentPage + 1 <= $max_page ? $currentPage + 1 : $max_page; ?>, '<? echo $master_o_seq; ?>',  '<? echo $visitor_depart_seq; ?>', '<? echo $master_o_ip; ?>')">
            >
        </button>


        <?
        foreach ($food_result as $index => $menuInfo) {
            ?>
            <div class="order_menu_content_recruit" onclick="clickRecruitMenu('<? echo $master_o_seq; ?>', '<? echo $r_seq; ?>', '<? echo $menuInfo['f_seq']?>', '<? echo $visitor_depart_seq; ?>', '<? echo $master_o_ip; ?>')">
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

<div class="order_total_area">

    <h2 style="text-align: center;">주문 총 합</h2>

    <div style="width:350px; height:150px; overflow:auto">

    <table class="order_total_table">
        <tr style="background-color: #87302b; text-align: center">
            <td>메뉴</td>
            <td>인원</td>
            <td>가격</td>
            <td>총합</td>
        </tr>



    <?

    if($totalOrderInfoResult[0]['f_name'] != null){
        $total_price = 0;
        foreach($totalOrderInfoResult as $index => $totalOrderInfoOneFood){
            $f_name = $totalOrderInfoOneFood['f_name'];
            $f_price = $totalOrderInfoOneFood['f_price'];
            $f_seq = $totalOrderInfoOneFood['f_seq'];
            $o_amount = $totalOrderInfoOneFood['o_amount'];
            $sum_f_price = $totalOrderInfoOneFood['sum_f_price'];

            $total_price += $sum_f_price;

            //echo $f_name . "  " . $o_amount . " 명 " . $sum_f_price . "원  ";

            ?>
            <tr>
                <td><? echo $f_name; ?></td>
                <td><? echo $o_amount; ?></td>
                <td><? echo $f_price; ?></td>
                <td><? echo $sum_f_price; ?></td>

            </tr>

            <?
        }
        //echo "  총 합계 금액 : " . $total_price . "원";

        ?>

    </table>
    </div>
        <a style="font-size: 3vh;">최종 금액 : <? echo $total_price; ?></a>

        <?


    } else {

        echo "아직 주문이 없네용 :)";

        ?>
    </table>
    </div>
        <?
    }




    ?>



    </div>
    <!--<a class="restaurant_title">bxx</a>-->
        <!--    <input type="text" id="order_department" placeholder="모집 부서 골라" value="<? /* if($department_name == true) echo ;*/ ?>">
-->


    <div class="order_comment_area" >
        <h2 style="text-align: center;">문의 사항</h2>
        <div style="overflow:auto; overflow-y:scroll; width:400px; height:180px; background-color: white;">
        <?


        if($commentResult[0]['c_seq'] != null){
            foreach($commentResult as $index => $commentInfo){
                $c_seq = $commentInfo['c_seq'];
                $c_content = $commentInfo['c_content'];
                $c_ins_stamp = $commentInfo['c_ins_stamp'];
                $c_ip = $commentInfo['c_ip'];
                $c_ins_date = date("h:i", $c_ins_stamp);


                if ($c_ip == $master_o_ip){


                    if($visitor_ip == $c_ip){

                    } else {

                    }


                    ?>

                    <span style="color: #7e302c" <? if($visitor_ip == $c_ip){?>onmouseover="changeCursor(this, 'pointer')" title="댓글 삭제 하기" onclick="deleteContent('comment', '<? echo $c_seq; ?>')" <?}?>><a>모집자 : </a><a><? echo $c_content; ?></a><a> 작성 : <? echo $c_ins_date ?></a></span>
                    <br>

        <?


                } else {
                    ?>

                    <a <? if($visitor_ip == $c_ip){?>onmouseover="changeCursor(this, 'pointer')" title="댓글 삭제 하기" onclick="deleteContent('comment', '<? echo $c_seq; ?>')" <?}?>><? if($visitor_ip == $c_ip) echo "내 댓글 : "; else echo " 방문자 : " ?></a><a><? echo $c_content; ?></a><a> 작성 : <? echo $c_ins_date ?></a><br>

                    <?



                }


            }
        }


        ?>
        </div>
        <input type="text" style="width:380px; text-align: left;" placeholder="문의 사항을 입력해주세요 :)" id = "input_recruit_question" onkeyup="enterQuestion('input_recruit_question','<? echo $master_o_seq; ?>' ,'<?echo $visitor_ip;?>', '<? echo $r_seq; ?>');">

    </div>


</div>




