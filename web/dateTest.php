<?

$timestamp = time();

$todayStartTimeStamp = strtotime(date("Y-m-d 22:40:38", $timestamp));
$todayEndTimeStamp = strtotime(date("Y-m-d 23:59:59", $timestamp));
#echo "timestamp : " . $timestamp . " start : " . $todayStartTimeStamp . " end : " . $todayEndTimeStamp;

#echo chr(10) . "startTimeStampDate : " . date("Y-m-d h:i:s A", $todayStartTimeStamp) . "endTimeStampDate : " . date("m-d h:i", $todayEndTimeStamp);


$testSubTimestamp = $todayEndTimeStamp - $todayStartTimeStamp;

#echo $testSubTimestamp ." " .date("h", $testSubTimestamp) . " " .date("i", $testSubTimestamp);

#echo chr(10) . "sub test : " . $testSubTimestamp . " sub date : ". date("h", $testSubTimestamp) . "  " . date("i", $testSubTimestamp);



$dateStart = date("h:i:s", $todayStartTimeStamp);

$dateEnd = date("h:i:s", $todayEndTimeStamp);


$dateSub = $dateEnd - $dateStart;

#echo $dateEnd . " " . $dateStart;


$start_date_time = new DateTime(date("Y-m-d 22:40:38",$todayStartTimeStamp));
$since_start_date_time = $start_date_time->diff(new DateTime(date("Y-m-d 23:59:59",$todayEndTimeStamp)));

$minutes = $since_start_date_time->h * 60;
$minutes += $since_start_date_time->i;

echo $minutes;


?>