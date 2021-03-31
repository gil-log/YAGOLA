<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);

$d_flag = $decodedRequest->d_flag;

$timestamp = time();
$todayStartTimeStamp = strtotime(date("Y-m-d 00:00:00", $timestamp));
$todayEndTimeStamp = strtotime(date("Y-m-d 23:59:59", $timestamp));

$cookie_d_seq = $_COOKIE['cookie_department'];
$d_name = '부서 미지정';

$accessIp = $_SERVER["REMOTE_ADDR"];

if ($cookie_d_seq != null) {
    $sql = "SELECT d_name FROM DEPARTMENT" . chr(10);
    $sql .= "WHERE d_seq = " . $cookie_d_seq;

    $result = $dbconn->excuteArray($sql);
    if ($result == true) {
        $d_name = $result[0]['d_name'];
    }

}

$recruitingSql = "SELECT `ORDER`.o_seq, `ORDER`.o_ip, `ORDER`.r_seq, `ORDER`.d_seq, `ORDER`.o_end_stamp, `ORDER`.o_ins_stamp, `RESTAURANT`.r_name, `DEPARTMENT`.d_name, `DEPARTMENT`.d_flag" . chr(10);

$recruitingSql .= "FROM" . chr(10);

$recruitingSql .= "(SELECT o_seq, o_ip, r_seq, d_seq, o_end_stamp, o_ins_stamp" . chr(10);
$recruitingSql .= "FROM `ORDER`" . chr(10);
$recruitingSql .= "WHERE o_flag = 0" . chr(10);

if($d_flag != 0){
    $recruitingSql .= " AND d_seq IN(SELECT d_seq FROM DEPARTMENT WHERE d_flag = " .$d_flag .")" .chr(10);
}

$recruitingSql .= "AND" . chr(10);
$recruitingSql .= "o_ins_stamp BETWEEN " . $todayStartTimeStamp . " AND " . $todayEndTimeStamp . ") `ORDER`" . chr(10);

$recruitingSql .= "LEFT JOIN `RESTAURANT`" . chr(10);
$recruitingSql .= "ON `ORDER`.r_seq = `RESTAURANT`.r_seq" . chr(10);

$recruitingSql .= "LEFT JOIN `DEPARTMENT`" . chr(10);
$recruitingSql .= "ON `ORDER`.d_seq = `DEPARTMENT`.d_seq" . chr(10);

$recruitingSql .= "ORDER BY `DEPARTMENT`.d_flag";

$recruitingResult = $dbconn->excuteArray($recruitingSql);

$recruitingCount = 0;

if ($recruitingResult == true) {
    $recruitingCount = count($recruitingResult);
}

?>

<span class="page_location" onmouseover="changeCursor(this, 'default')" xmlns="http://www.w3.org/1999/html"> <img class="page_location_img" src="images/logo_background.png"> 야골라 / 주문 모집 </span>

<input type="hidden" id="d_flag" value="<? echo $d_flag; ?>">

<div class="department_category_area">


    <table class="department_category_table">
        <tr>
            <td width="10%;" >
                <div class="department_category_detail_area" onmouseover="changeCursor(this, 'pointer')" title="주문 모집 글 전체 보기" onclick="getDepartmentHtml('0')">
                    <img src="images/dept_logo.png">
                </div>
            </td>
            <td width="10%;" >
                <div class="department_category_detail_area" onmouseover="changeCursor(this, 'pointer')" title="연구 & 개발 소속 부서 주문 모집 글 찾기" onclick="getDepartmentHtml('1')">
                    <img src="images/dept_rnd.png">
                </div>
            </td>
            <td width="10%;">
                <div class="department_category_detail_area" onmouseover="changeCursor(this, 'pointer')" title="엔터프라이즈 소속 부서 주문 모집 글 찾기" onclick="getDepartmentHtml('2')">
                    <img src="images/dept_enter.png">
                </div>
            </td>
            <td width="10%;">
                <div class="department_category_detail_area" onmouseover="changeCursor(this, 'pointer')" title="서비스 컨설팅 소속 부서 주문 모집 글 찾기" onclick="getDepartmentHtml('3')">
                    <img src="images/dept_consult.png">
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <a class="dept_category_name">전체 보기</a>
            </td>
            <td>
                <a class="dept_category_name">연구 & 개발</a>
            </td>
            <td>
                <a class="dept_category_name">엔터프라이즈</a>
            </td>
            <td>
                <a class="dept_category_name">서비스 컨설팅</a>
            </td>
        </tr>

    </table>

    <div class="dept_choose_area" onmouseover="changeCursor(this, 'default')" title="설정된 나의 부서에오 :)">
        <div class="my_dept_area"><a>나의 부서 :</a></div>
        <div class="my_dept_area" onmouseover="changeCursor(this, 'pointer')" title="설정 부서를 바꿀 수 있어여 :)" id="my_dept" onclick="deleteFavoriteDepartment('<? echo $cookie_d_seq; ?>')"><a
                id="favorite_dept"><? echo $d_name; ?> <span style="color: #87302b"><i
                        class="far fa-times-circle"></span></i></a></div>
    </div>


    <br>
    <br>

    <h2 onmouseover="changeCursor(this, 'default')"><i class="fab fa-slideshare"></i>&nbsp;주문 모집 목록&nbsp;<i class="fab fa-slideshare"></i></h2>

    <div id="order_list_area">



        <?
        if ($recruitingCount != 0) {

?>

        <table class="order_list_table">
            <thead onmouseover="changeCursor(this, 'default')">
                <td></td>
                <td>부서</td>
                <td>가게</td>
                <td>구분</td>
                <td>인원</td>
                <td>일자</td>
            </thead>

            <?

            $myDeptList = array();

            foreach ($recruitingResult as $index => $favoriteDepartment){
                $d_seq = $favoriteDepartment['d_seq'];
                if($cookie_d_seq == $d_seq){

                    array_push($myDeptList, $d_seq);

                    $o_seq = $favoriteDepartment['o_seq'];
                    $o_ip = $favoriteDepartment['o_ip'];
                    $r_seq = $favoriteDepartment['r_seq'];
                    $o_ins_stamp = $favoriteDepartment['o_ins_stamp'];
                    $o_end_stamp = $favoriteDepartment['o_end_stamp'];
                    $r_name = $favoriteDepartment['r_name'];
                    $d_name = $favoriteDepartment['d_name'];
                    $d_flag = $favoriteDepartment['d_flag'];

                    $o_ins_date = date("m-d h:i", $o_ins_stamp);

                    $countPeopleSql = "SELECT count(o_seq) as count_people" . chr(10);
                    $countPeopleSql .= "FROM `ORDER`" . chr(10);
                    $countPeopleSql .= "GROUP BY o_flag" . chr(10);
                    $countPeopleSql .= "HAVING o_flag = " . $o_seq . chr(10);

                    $countPeopleResult = $dbconn->excuteArray($countPeopleSql);

                    $count_people = 1;
                    if ($countPeopleResult == true) {
                        $count_people += $countPeopleResult[0]['count_people'];
                    }

                    $recruitKind = '';
                    if ($o_end_stamp == 0) {
                        $recruitKind = '모집';
                    } else if ($o_end_stamp == 1) {
                        $recruitKind = '도착';
                    } else {
                        $recruitKind = '마감';
                    }

                    ?>

                    <tr onmouseover="changeCursor(this, 'pointer')" title="주문 모집 글로 이동하기!" onclick="clickOrder('<? echo $o_seq ?>', '<? echo $r_seq ?>', '0')">

                        <?if($accessIp == $o_ip){
                            ?>
                            <td class="order_list_table_mine">내 글</td>

                            <td class="order_list_table_mine"><? echo $d_name; ?></td>
                            <td class="order_list_table_mine"><? echo $r_name; ?></td>
                            <td class="order_list_table_mine"><? echo $recruitKind; ?></td>
                            <td class="order_list_table_mine"><? echo $count_people; ?></td>
                            <td class="order_list_table_mine"><? echo $o_ins_date; ?></td>
                        <?
                        } else{
                            ?>

                            <td class="order_list_table_mine">우리 부서</td>
                            <td class="order_list_table_mine"><? echo $d_name; ?></td>
                            <td class="order_list_table_mine"><? echo $r_name; ?></td>
                            <td class="order_list_table_mine"><? echo $recruitKind; ?></td>
                            <td class="order_list_table_mine"><? echo $count_people; ?></td>
                            <td class="order_list_table_mine"><? echo $o_ins_date; ?></td>
                        <?
                        }
                        ?>
                    </tr>



                    <?
                }
            }









            foreach ($recruitingResult as $index => $recruitContent) {
                $o_seq = $recruitContent['o_seq'];
                $o_ip = $recruitContent['o_ip'];
                $r_seq = $recruitContent['r_seq'];
                $d_seq = $recruitContent['d_seq'];
                $o_ins_stamp = $recruitContent['o_ins_stamp'];
                $o_end_stamp = $recruitContent['o_end_stamp'];
                $r_name = $recruitContent['r_name'];
                $d_name = $recruitContent['d_name'];
                $d_flag = $recruitContent['d_flag'];

                $o_ins_date = date("m-d h:i", $o_ins_stamp);

                $countPeopleSql = "SELECT count(o_seq) as count_people" . chr(10);
                $countPeopleSql .= "FROM `ORDER`" . chr(10);
                $countPeopleSql .= "GROUP BY o_flag" . chr(10);
                $countPeopleSql .= "HAVING o_flag = " . $o_seq . chr(10);

                $countPeopleResult = $dbconn->excuteArray($countPeopleSql);

                $count_people = 1;
                if ($countPeopleResult == true) {
                    $count_people += $countPeopleResult[0]['count_people'];
                }

                $recruitKind = '';
                if ($o_end_stamp == 0) {
                    $recruitKind = '모집';
                } else if ($o_end_stamp == 1) {
                    $recruitKind = '도착';
                } else {
                    $recruitKind = '마감';
                }


                if(!in_array($d_seq, $myDeptList)){



                ?>

                    <tr onmouseover="changeCursor(this, 'pointer')" title="주문 모집 글로 이동하기!" onclick="clickOrder('<? echo $o_seq ?>', '<? echo $r_seq ?>', '0')">
                        <td><? if($accessIp == $o_ip) echo "내꺼"; ?></td>
                        <td><? echo $d_name; ?></td>
                        <td><? echo $r_name; ?></td>
                        <td><? echo $recruitKind; ?></td>
                        <td><? echo $count_people; ?></td>
                        <td><? echo $o_ins_date; ?></td>
                    </tr>

<!--                <div class="order_list_content <?/* echo $d_flag; */?>" onclick="clickOrder('<?/* echo $o_seq */?>', '<?/* echo $r_seq */?>', '0')">
                    <?/* if($accessIp == $o_ip) echo "<작성 글>" */?>
                    <a style="font-size: 3vh;"> 가게 명 : <?/* echo $r_name; */?><a>  <p> 부서명 : <?/* echo $d_name; */?></p>&nbsp;&nbsp;<a> 구분
                                : <?/* echo $recruitKind; */?></a>&nbsp;<a> 인원 : <?/* echo $count_people; */?></a> <a> 일자 : <?/* echo $o_ins_date; */?></a>
                </div>

                <br>-->
                <?
                }

            }

            ?>
            </table>
            <?
        } else {
            ?>
            <div class="order_list_content">
                <a> 모집 글이 없어용 :( </a>
            </div>

            <?
        }


        ?>


    </div>


</div>


<div id="modal_recruit" class="my_modal" style=" width: 800px; height: 750px;">
<!--    <div class="my_modal_title" style="width: 60%; align-content: center;">
        <img src="images/logo_header.png">
    </div>-->

    <a class="far fa-window-close modal_close_btn"></a>

    <div class="bodyHeightDiv">
        <div class="modalProgramTitle">
            <!--<img src="images/logo_header.png" style="width: 100%;">-->
        </div>
        <div class="masterModalBody" id="masterModalBody" style="background-color: #3498DB;">
            <div id="AXSearchTarget" style=""></div>
            <div style="padding:5px;">
                <div id="AXGridTarget" style="height:500px;">

                    <div id="recruit_area">


                    </div>


                </div>
            </div>
        </div>
    </div>
</div>


<div id="modal_recruit_done" class="my_modal" style="height: 250px;">
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

                    <div id="recruit_done_area">

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

                        <p class="department_system">어느 그룹에 계신가요?</p>

                        <div class="department_element" onclick="selectGroup(1);">
                            <img src="images/dept_rnd.png" class="group_img">

                            <p class="group_name">연구 & 개발</p>
                        </div>

                        <div class="department_element" onclick="selectGroup(2);">
                            <img src="images/dept_enter.png" class="group_img">

                            <p class="group_name">엔터프라이즈</p>
                        </div>

                        <div class="department_element" onclick="selectGroup(3);">
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