<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);


$d_seq = $decodedRequest->d_seq;
$d_name = $decodedRequest->d_name;

$groupImgSrc = '';

if($d_seq == 1){
    $groupImgSrc = 'images/dept_rnd.png';
}else if($d_seq == 2){
    $groupImgSrc = 'images/dept_consult.png';
}else{
    $groupImgSrc = 'images/dept_enter.png';
}


?>
    <div class="department_element" onmouseover="changeCursor(this, 'default')">
        <img class="department_img" id="selected_group_img" src="<? echo $groupImgSrc ?>">
        <p class="department_name_selected" id="selected_group_name"><? echo $d_name; ?></p>
        <p class="department_system">최종 선택!</p>
        <p class="department_system">이제 골라 보세요!</p>
        <span style="color:#CD2D2D; font-size: 1vh;" onmouseover="changeCursor(this, 'pointer')" title="닫기!"><i class="fas fa-utensils fa-5x" onclick="closeModal('main_modal');"> 고르러 가기!</i></span>

    </div>


<?
?>