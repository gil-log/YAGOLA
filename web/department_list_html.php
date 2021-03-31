<?
require_once("../class/config.php");
require_once("../class/class_MySqlConnection.php");

$dbconn = new MySQLConnection($DB_INFO);

$request = file_get_contents("php://input");
$decodedRequest = json_decode($request);


$d_flag = $decodedRequest->d_flag;

$groupName = '';
$groupImgSrc = '';

if($d_flag == 1){
    $groupName = '연구 & 개발';
    $groupImgSrc = 'images/dept_rnd.png';
} else if ($d_flag == 2){
    $groupName = '엔터프라이즈';
    $groupImgSrc = 'images/dept_enter.png';
} else if ($d_flag == 3){
    $groupName = '서비스 컨설팅';
    $groupImgSrc = 'images/dept_consult.png';
}



$sql = "SELECT d_seq, d_name FROM department".chr(10);
$sql .= "WHERE d_flag = " . $d_flag;

$result = $dbconn->excuteArray($sql);


?>

<div class="department_element" onmouseover="changeCursor(this, 'default')" title="선택 그룹 명 이에오 :)">
    <p class="group_name_selected" id="selected_group_name"><? echo $groupName; ?></p>
    <img class="department_img" id="selected_group_img" src="<? echo $groupImgSrc; ?>">
</div>
<p class="department_system" onmouseover="changeCursor(this, 'default')">부서를 선택해주세요.</p>

<?
foreach($result as $index => $departmentInfo){
    $d_seq = $departmentInfo['d_seq'];
    $d_name = $departmentInfo['d_name'];
    ?>

    <div class="department_list" onmouseover="changeCursor(this, 'pointer')" title="부서를 선택해 주세오 :)" onclick="selectDepartment('<? echo $d_seq; ?>', '<? echo $d_name; ?>')">

        <p class="department_name"><i class="fas fa-building"></i>&nbsp;<? echo $d_name; ?>&nbsp;<i class="fas fa-building"></i></p>

    </div>

<?


}

//echo (json_encode(array("d_flag" => $d_flag, "result" => $result)));

?>