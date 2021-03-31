<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);

$s_flag = $decodedRequest->s_flag;
$s_criteria = $decodedRequest->s_criteria;
$s_keyword = $decodedRequest->s_keyword;
$page = $decodedRequest->page;

/*$s_flag = 'category';
$s_criteria = 'time';
$s_keyword = 'kor';
$page = 1;*/

$currentPage = $page + 1;

$page *= 20;


$sqlTotalCountRestaurant = '';
$sqlRestaurant = '';
$sqlTotalCountFood = '';
$sqlFood = '';

$img_prefix = "https://www.yogiyo.co.kr";

if($s_flag=='category'){

    $sqlTotalCountRestaurant = "SELECT count(r_seq) as total_count" .chr(10);
    $sqlTotalCountRestaurant .= "FROM RESTAURANT  WHERE r_seq != 1" .chr(10);
    $sqlTotalCountRestaurant .= "AND r_category = '" . $s_keyword ."'";

    if($s_criteria == 'time'){
        $sqlRestaurant = "SELECT r_seq, r_name, r_img_link, round(r_total_time / r_total_count) as r_time, r_order_count" .chr(10);
        $sqlRestaurant .= "FROM RESTAURANT  WHERE r_seq != 1" .chr(10);
        $sqlRestaurant .= "AND r_category = '". $s_keyword ."'" .chr(10);
        $sqlRestaurant .= "ORDER BY r_time ASC" .chr(10);
        $sqlRestaurant .= "LIMIT 20 OFFSET " . $page;

    } else if($s_criteria == 'count'){
        $sqlRestaurant = "SELECT r_seq, r_name, r_img_link, round(r_total_time / r_total_count) as r_time, r_order_count" .chr(10);
        $sqlRestaurant .= "FROM RESTAURANT  WHERE r_seq != 1" .chr(10);
        $sqlRestaurant .= "AND r_category = '". $s_keyword ."'" .chr(10);
        $sqlRestaurant .= "ORDER BY r_order_count DESC" .chr(10);
        $sqlRestaurant .= "LIMIT 20 OFFSET " . $page;

    }


} else if($s_flag=='keyword'){

    $sqlTotalCountRestaurant = "SELECT count(r_seq) as total_count" .chr(10);
    $sqlTotalCountRestaurant .= "FROM RESTAURANT  WHERE r_seq != 1" .chr(10);
    $sqlTotalCountRestaurant .= "AND r_name LIKE '%". $s_keyword ."%'";

    $sqlTotalCountFood = "SELECT count(DISTINCT f_seq) as total_count" .chr(10);
    $sqlTotalCountFood .= "FROM FOOD WHERE f_seq != 1" .chr(10);
    $sqlTotalCountFood .= "AND f_name LIKE '%". $s_keyword ."%'";

    if($s_criteria == 'time'){
        $sqlRestaurant = "SELECT r_seq, r_name, r_img_link, round(r_total_time / r_total_count) as r_time, r_order_count" .chr(10);
        $sqlRestaurant .= "FROM RESTAURANT  WHERE r_seq != 1" .chr(10);
        $sqlRestaurant .= "AND r_name LIKE '%". $s_keyword ."%'" .chr(10);
        $sqlRestaurant .= "ORDER BY r_time ASC" .chr(10);
        $sqlRestaurant .= "LIMIT 20 OFFSET " . $page;

        $sqlFood = "SELECT RESTAURANT_TOTAL.*" .chr(10);
        $sqlFood .= "FROM" .chr(10);
        $sqlFood .="(SELECT DISTINCT r_seq" . chr(10);
        $sqlFood .= "FROM FOOD WHERE f_seq != 1" .chr(10);
        $sqlFood .= "AND f_name LIKE '%".$s_keyword ."%'" .chr(10);
        $sqlFood .= ") RESTAURANT_SEARCH" .chr(10);
        $sqlFood .= "LEFT JOIN" .chr(10);
        $sqlFood .= "(SELECT r_seq, r_name, r_img_link, round(r_total_time / r_total_count) as r_time, r_order_count".chr(10);
        $sqlFood .= "FROM RESTAURANT  WHERE r_seq != 1" .chr(10);
        $sqlFood .= ") RESTAURANT_TOTAL" .chr(10);
        $sqlFood .= "ON RESTAURANT_SEARCH.r_seq = RESTAURANT_TOTAL.r_seq" .chr(10);
        $sqlFood .= "ORDER BY RESTAURANT_TOTAL.r_time ASC" .chr(10);
        $sqlFood .= "LIMIT 20 OFFSET " . $page;

    } else if($s_criteria == 'count'){
        $sqlRestaurant = "SELECT r_seq, r_name, r_img_link, round(r_total_time / r_total_count) as r_time, r_order_count" .chr(10);
        $sqlRestaurant .= "FROM RESTAURANT  WHERE r_seq != 1" .chr(10);
        $sqlRestaurant .= "AND r_name LIKE '%". $s_keyword ."%'" .chr(10);
        $sqlRestaurant .= "ORDER BY r_total_count DESC" .chr(10);
        $sqlRestaurant .= "LIMIT 20 OFFSET " . $page;

        $sqlFood = "SELECT RESTAURANT_TOTAL.*" .chr(10);
        $sqlFood .= "FROM" .chr(10);
        $sqlFood .="(SELECT DISTINCT r_seq" . chr(10);
        $sqlFood .= "FROM FOOD WHERE f_seq != 1" .chr(10);
        $sqlFood .= "AND f_name LIKE '%".$s_keyword ."%'" .chr(10);
        $sqlFood .= "ORDER BY f_order_count DESC" .chr(10);
        $sqlFood .= ") RESTAURANT_SEARCH" .chr(10);
        $sqlFood .= "LEFT JOIN" .chr(10);
        $sqlFood .= "(SELECT r_seq, r_name, r_img_link, round(r_total_time / r_total_count) as r_time, r_order_count, r_total_count".chr(10);
        $sqlFood .= "FROM RESTAURANT WHERE r_seq != 1" .chr(10);
        $sqlFood .= ") RESTAURANT_TOTAL" .chr(10);
        $sqlFood .= "ON RESTAURANT_SEARCH.r_seq = RESTAURANT_TOTAL.r_seq" .chr(10);
        $sqlFood .= "ORDER BY RESTAURANT_TOTAL.r_total_count DESC" .chr(10);
        $sqlFood .= "LIMIT 20 OFFSET " . $page;

    }
}



$total_count_restaurant = $dbconn->excuteArray($sqlTotalCountRestaurant);
/*echo "!!!!!!!!!!restaurant_count : " .chr(10);
print_r($total_count_restaurant) . chr(10);
echo "!!!!!!!!!!!!!!!!!!!!!!!!" .chr(10);*/

$max_page = ceil($total_count_restaurant[0]['total_count'] / 20);
$current_page_max_range = ceil($currentPage/10) * 10;

$result_restaurant = $dbconn->excuteArray($sqlRestaurant);

/*echo "!!!!!!!!!!result_restaurant : " .chr(10);
print_r($result_restaurant);
echo "!!!!!!!!!!!!!!!!!!!!!!!!" .chr(10);*/


?>
<span class="page_location" onmouseover="changeCursor(this, 'default')"> <img class="page_location_img"src="images/logo_background.png"> 야골라 / 검색 </span>


<?


if($s_flag=='category'){
    ?>

    <div class="search_title" onmouseover="changeCursor(this, 'default')" title="일치하는 식당 리스트에오 :)">
        <h2><i class="fas fa-store"></i>
            식당 골라
            <i class="fas fa-store"></i>
        </h2>
    </div>

    <div class="search_wrapper">
    <?

    foreach($result_restaurant as $index => $restaurantInfo){
        $r_seq = $restaurantInfo['r_seq'];
        $r_img_link = $restaurantInfo['r_img_link'];
        $r_name = $restaurantInfo['r_name'];
        $r_time = $restaurantInfo['r_time'];
        $r_order_count = $restaurantInfo['r_order_count'];

    ?>





    <div class="search_content" onmouseover="changeCursor(this, 'pointer')" title="식당 정보를 확인 할래여 :)" onclick="clickContent('<? echo $r_seq; ?>')">
        <img src="<? echo $img_prefix.$r_img_link; ?>">
        <p class="menu_name"><? echo $r_name; ?></p>
        <p class="menu_order_count">

                <span style="color:#bf601b">
                <i class="fas fa-stopwatch">&nbsp;</i>
                </span>
            <? echo $r_time; ?>&nbsp;분

            &nbsp;&nbsp;

                <span style="color:#FFFF99">
                <i class="fas fa-file-invoice-dollar">&nbsp;</i>
                </span>
            <? echo $r_order_count; ?>&nbsp;건

        </p>
    </div>


<?

    }
    ?>

    </div>

    <div class="search_paging_area">
        <div class="search_paging_button_area">

            <?

            if($max_page>10){


            ?>
            <button class="search_paging_button"
                    onclick="nextSearchPage('category', '<? echo $s_criteria; ?>', '<? echo $s_keyword; ?>', '<? 0 ?>')"><<</button>

            <?

            }

            ?>

            <button class="search_paging_button" id="search_paging_previous"
                    onclick="nextSearchPage('category', '<? echo $s_criteria; ?>', '<? echo $s_keyword; ?>', '<? echo $currentPage - 1 > 0 ? $currentPage - 2 : 0  ?>')"><</button>

            <?

            for($i=$current_page_max_range-9; $i <= $current_page_max_range; $i++){
                if($i == $currentPage){
                    ?>
                    <button class="search_paging_button_checked"
                            onclick="nextSearchPage('category', '<? echo $s_criteria; ?>', '<? echo $s_keyword; ?>', '<? echo $i-1; ?>')"><? echo $i;?></button>
                    <?
                } else {
                    ?>
                    <button class="search_paging_button"
                            onclick="nextSearchPage('category', '<? echo $s_criteria; ?>', '<? echo $s_keyword; ?>', '<? echo $i-1; ?>')"><? echo $i;?></button>
                    <?
                }

                if($i == $max_page) break;

            }





            ?>

            <button class="search_paging_button"
                    onclick="nextSearchPage('category', '<? echo $s_criteria; ?>', '<? echo $s_keyword; ?>', '<? echo $currentPage + 1 < $max_page ? $currentPage : $max_page-1; ?>')">></button>


            <?

            if($max_page>10){


                ?>
                <button class="search_paging_button"
                        onclick="nextSearchPage('category', '<? echo $s_criteria; ?>', '<? echo $s_keyword; ?>', '<? echo $current_page_max_range < $max_page ? $current_page_max_range : $max_page - 1; ?>')">>></button>

                <?

            }

            ?>

        </div>
    </div>












    <div id="modal_order" class="my_modal" style="height: 750px;">
        <div class="my_modal_title">
            <img src="images/logo_header.png">
        </div>

        <a class="far fa-window-close modal_close_btn"></a>

        <div class="bodyHeightDiv">
            <div class="modalProgramTitle">
                <!--<img src="images/logo_header.png" style="width: 100%;">-->
            </div>
            <div class="masterModalBody" id="masterModalBody" style="background-color: #3498DB;">
                <div id="AXSearchTarget" style=""></div>
                <div style="padding:5px;">
                    <div id="AXGridTarget" style="height:500px;">

                        <div id="order_area">


                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>




        <?

} else if($s_flag == 'keyword'){
    $sqlTotalCountFood = $dbconn->excuteArray($sqlTotalCountFood);

/*    echo "!!!!!!!!!!food_count : " .chr(10);
    print_r($sqlTotalCountFood) . chr(10);
    echo "!!!!!!!!!!!!!!!!!!!!!!!!" .chr(10);*/



    $result_food = $dbconn->excuteArray($sqlFood);
/*    echo "!!!!!!!!!!result_food : " .chr(10);
    print_r($result_food);
    echo "!!!!!!!!!!!!!!!!!!!!!!!!" .chr(10);*/



    if($result_restaurant[0]['r_seq'] != null && $result_food[0]['r_seq'] != null){





    ?>

    <div class="search_title" onmouseover="changeCursor(this, 'default')" title="일치하는 식당 리스트에오 :)">
        <h2 ><i class="fas fa-store"></i>
            식당 골라
            <i class="fas fa-store"></i>
        </h2>




        <div class="search_wrapper">
            <?

            foreach($result_restaurant as $index => $restaurantInfo){
                $r_seq = $restaurantInfo['r_seq'];
                $r_img_link = $restaurantInfo['r_img_link'];
                $r_name = $restaurantInfo['r_name'];
                $r_time = $restaurantInfo['r_time'];
                $r_order_count = $restaurantInfo['r_order_count'];

                ?>





                <div class="search_content" onmouseover="changeCursor(this, 'pointer')" title="식당 정보를 확인 할래여 :)" onclick="clickContent('<? echo $r_seq; ?>')">
                    <img src="<? echo $img_prefix.$r_img_link; ?>">
                    <p class="menu_name"><? echo $r_name; ?></p>
                    <p class="menu_order_count" style="font-size: 2vh;">

                <span style="color:#bf601b">
                <i class="fas fa-stopwatch">&nbsp;</i>
                </span>
                        <? echo $r_time; ?>&nbsp;분

                        &nbsp;&nbsp;

                <span style="color:#FFFF99">
                <i class="fas fa-file-invoice-dollar">&nbsp;</i>
                </span>
                        <? echo $r_order_count; ?>&nbsp;건

                    </p>
                </div>


                <?

            }
            ?>

        </div>

        <div class="search_paging_area">
            <div class="search_paging_button_area">

                <?

                if($max_page>10){


                    ?>
                    <button class="search_paging_button"
                            onclick="nextSearchPage('keyword', '<? echo $s_criteria; ?>', '<? echo $s_keyword; ?>', '<? 0 ?>')"><<</button>

                    <?

                }

                ?>

                <button class="search_paging_button" id="search_paging_previous"
                        onclick="nextSearchPage('keyword', '<? echo $s_criteria; ?>', '<? echo $s_keyword; ?>', '<? echo $currentPage - 1 > 0 ? $currentPage - 2 : 0  ?>')"><</button>

                <?

                for($i=$current_page_max_range-9; $i <= $current_page_max_range; $i++){
                    if($i == $currentPage){
                        ?>
                        <button class="search_paging_button_checked"
                                onclick="nextSearchPage('keyword', '<? echo $s_criteria; ?>', '<? echo $s_keyword; ?>', '<? echo $i-1; ?>')"><? echo $i;?></button>
                        <?
                    } else {
                        ?>
                        <button class="search_paging_button"
                                onclick="nextSearchPage('keyword', '<? echo $s_criteria; ?>', '<? echo $s_keyword; ?>', '<? echo $i-1; ?>')"><? echo $i;?></button>
                        <?
                    }

                    if($i == $max_page) break;

                }





                ?>

                <button class="search_paging_button"
                        onclick="nextSearchPage('keyword', '<? echo $s_criteria; ?>', '<? echo $s_keyword; ?>', '<? echo $currentPage + 1 < $max_page ? $currentPage : $max_page-1; ?>')">></button>


                <?

                if($max_page>10){


                    ?>
                    <button class="search_paging_button"
                            onclick="nextSearchPage('keyword', '<? echo $s_criteria; ?>', '<? echo $s_keyword; ?>', '<? echo $current_page_max_range < $max_page ? $current_page_max_range : $max_page - 1; ?>')">>></button>

                    <?

                }

                ?>

            </div>
        </div>


        <h2 onmouseover="changeCursor(this, 'default')" title="해당 메뉴가 포함된 식당 리스트에오 :)"><i class="fas fa-utensils"></i>
            그 메뉴 있더라 골라
            <i class="fas fa-utensils"></i>
        </h2>










        <div class="search_wrapper">
            <?

            $result_food = $dbconn->excuteArray($sqlFood);

            if($result_food != null){
                foreach($result_food as $index => $restaurantInfo){
                    $r_seq = $restaurantInfo['r_seq'];
                    $r_img_link = $restaurantInfo['r_img_link'];
                    $r_name = $restaurantInfo['r_name'];
                    $r_time = $restaurantInfo['r_time'];
                    $r_order_count = $restaurantInfo['r_order_count'];

                    ?>





                    <div class="search_content" onmouseover="changeCursor(this, 'pointer')" title="식당 정보를 확인 할래여 :)" onclick="clickContent('<? echo $r_seq; ?>')">
                        <img src="<? echo $img_prefix.$r_img_link; ?>">
                        <p class="menu_name"><? echo $r_name; ?></p>
                        <p class="menu_order_count" style="font-size: 2vh;">

                <span style="color:#bf601b">
                <i class="fas fa-stopwatch">&nbsp;</i>
                </span>
                            <? echo $r_time; ?>&nbsp;분

                            &nbsp;&nbsp;

                <span style="color:#FFFF99">
                <i class="fas fa-file-invoice-dollar">&nbsp;</i>
                </span>
                            <? echo $r_order_count; ?>&nbsp;건

                        </p>
                    </div>


                    <?

                }
            }


            ?>

        </div>
    </div>



<?

    } else{
        ?>


        <h2><i class="fas fa-utensils"></i>
            미안합니당.. 없어용 :(
            <i class="fas fa-utensils"></i>
        </h2>




<?
    }
        ?>


















    <div id="modal_order" class="my_modal" style="height: 750px;">
        <div class="my_modal_title">
            <img src="images/logo_header.png">
        </div>

        <a class="far fa-window-close modal_close_btn"></a>

        <div class="bodyHeightDiv">
            <div class="modalProgramTitle">
                <!--<img src="images/logo_header.png" style="width: 100%;">-->
            </div>
            <div class="masterModalBody" id="masterModalBody" style="background-color: #3498DB;">
                <div id="AXSearchTarget" style=""></div>
                <div style="padding:5px;">
                    <div id="AXGridTarget" style="height:500px;">

                        <div id="order_area">


                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
<?

}

?>
<div id="modal_order_done" class="my_modal" style="height: 250px;">
    <div class="my_modal_title">
        <img src="images/logo_header.png">
    </div>

    <a class="far fa-window-close modal_close_btn"></a>

    <div class="bodyHeightDiv">
        <div class="modalProgramTitle">
            <!--<img src="images/logo_header.png" style="width: 100%;">-->
        </div>
        <div class="masterModalBody" id="masterModalBody" style="background-color: #3498DB;">
            <div id="AXSearchTarget" style=""></div>
            <div style="padding:5px;">
                <div id="AXGridTarget" style="height:500px;">

                    <div id="order_done_area">

                        <h2></h2>


                    </div>


                </div>
            </div>
        </div>
    </div>
</div>



<div id="main_modal" class="my_modal">
    <div class="my_modal_title">
        <img src="images/logo_header.png">
    </div>

    <a class="far fa-window-close modal_close_btn"></a>

    <div class="bodyHeightDiv">
        <div class="modalProgramTitle">
            <!--<img src="images/logo_header.png" style="width: 100%;">-->
        </div>
        <div class="masterModalBody" id="masterModalBody" style="background-color: #3498DB;">
            <div id="AXSearchTarget" style=""></div>
            <div style="padding:5px;">
                <div id="AXGridTarget" style="height:500px;">

                    <div id="department_area">

                        <p class="department_system" onmouseover="changeCursor(this, 'default')">어느 그룹에 계신가요?</p>

                        <div class="department_element" onmouseover="changeCursor(this, 'pointer')" title="연구 & 개발 그룹 선택" onclick="selectGroup(1);">
                            <img src="images/dept_rnd.png" class="group_img">

                            <p class="group_name">연구 & 개발</p>
                        </div>

                        <div class="department_element" onmouseover="changeCursor(this, 'pointer')" title="엔터프라이즈 그룹 선택" onclick="selectGroup(2);">
                            <img src="images/dept_enter.png" class="group_img">

                            <p class="group_name">엔터프라이즈</p>
                        </div>

                        <div class="department_element" onmouseover="changeCursor(this, 'pointer')" title="서비스 컨설팅 그룹 선택" onclick="selectGroup(3);">
                            <img src="images/dept_consult.png" class="group_img">

                            <p class="group_name">서비스 컨설팅</p>
                        </div>


                    </div>

                    <div id="department_list_area" style="display: none;">

                        <div class="department_element">
                            <p class="group_name_selected" id="selected_group_name"></p>
                            <img class="department_img" id="selected_group_img" src="images/dept_rnd.png">
                        </div>

                        <p class="department_system">부서를 선택해주세요.</p>

                    </div>

                    <div id="department_done_area" style="display: none;">


                    </div>

                </div>
            </div>
        </div>
    </div>
</div>