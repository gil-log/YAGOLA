<?
require_once('../class/config.php');
require_once('../class/class_RequestAPIData.php');
require_once('../class/class_MySqlConnection.php');

class API
{
    public $apiInfo;
    public $url;
    public $dbconn;

    function __construct($API_INFO, $DB_INFO)
    {
        $this ->apiInfo = $API_INFO;
        $this -> dbconn = new MySQLConnection($DB_INFO);
    }

    function setURL($url){
        $parameters = http_build_query($this->apiInfo['urlParameter'], '');
        $tempURL = $url . $parameters;

        $this->url = $tempURL;
    }

    function main(){

    }

    function initData(){

        $RequestAPI = new RequestAPIData($this->apiInfo['arrCurlOptions']);

        $response = $RequestAPI->requestAPI($this->url);

        $arrResponse = json_decode($response, true);

        $totalPage = $arrResponse['pagination']['total_pages'];

        $naverPlaceURLPrefix = "https://map.naver.com/v5/api/search?query=";
        $naverPlaceURLSuffix = "&searchCoord=126.74634933471671;37.69655537319996";

        $categories = array(
            '중식' => 'chi',
            '일식돈까스' => 'jap',
            '피자양식' => 'wes',
            '한식' => 'kor');

        foreach($categories as $keyword => $category){

            for($nextPage = 0; $nextPage < 1; $nextPage++){
                $tempURL = $this->url ."&category=".urlencode($keyword) ."&page=" . $nextPage;

                echo '페이지 : ' .$nextPage;

                $response = $RequestAPI->requestAPI($tempURL);

                $arrResponse = json_decode($response, true);

                $arrRestaurantsInfo = $arrResponse['restaurants'];

                foreach($arrRestaurantsInfo as $index => $restaurantInfo ){
                    $name = $restaurantInfo['name'];
                    $id = $restaurantInfo['id'];
                    $delivery_time = $restaurantInfo['estimated_delivery_time'];
                    $logo_url = $restaurantInfo['logo_url'];

                    if(!$time = $this->getDeliveryTime($delivery_time)){
                        $time = 0;
                    }


                    sleep(rand(2, 6));




                    $placeName = $this->subBranchName($name);

                    $beforeUrlEncodedPlaceName = $placeName;

                    $placeName = urlencode($placeName);

                    $naverPlaceAPIURL = $naverPlaceURLPrefix. $placeName . $naverPlaceURLSuffix;

                    $naverPlaceAPIResponse = $RequestAPI->requestNaverPlaceAPI($naverPlaceAPIURL);

                    $arrNaverResponse = json_decode($naverPlaceAPIResponse, true);

                    echo chr(10). '################tel############'.chr(10);


                    echo 'placeName : ' . $name . 'BranchName : ' . $placeName . 'beforeEnceded Place Name' . $beforeUrlEncodedPlaceName;



                    #print_r($arrNaverResponse);


                    $tel = $arrNaverResponse['result']['place']['list'][0]['tel'];

                    echo 'tel : ' . $tel . chr(10);

                    $telDisplay = $arrNaverResponse['result']['place']['list'][0]['telDisplay'];

                    echo 'telDisplay : ' . $telDisplay . chr(10);

                    $sql = '';

                    if(!($tel=='' && $telDisplay=='')){
                        echo chr(10).'!!!!!!!!!!!!!IS NOT NULL!!!!!!!!!!'.chr(10);

                        if(!$telDisplay=='') $tel = $telDisplay;

                        if($time=='0'){
                            $sql = "INSERT INTO RESTAURANT(r_name, r_api_id, r_img_link, r_tel, r_total_time, r_category, r_total_count)".chr(10);
                            $sql .= "VALUES('" . $name ."', " . $id .", '" . $logo_url ."', '" . $tel . "', " . $time . ", '" . $category ."', ". 0 .")".chr(10);
                        }else{
                            $sql = "INSERT INTO RESTAURANT(r_name, r_api_id, r_img_link, r_tel, r_total_time, r_category)".chr(10);
                            $sql .= "VALUES('" . $name ."', " . $id .", '" . $logo_url ."', '" . $tel . "', " . $time . ", '" . $category ."')".chr(10);
                        }



                        echo chr(10)."sql : " . $sql . chr(10);

                    }else{
                        echo chr(10).'@@@@@!!!!!!!!!!!!!IS NULL!!!!!!!!!!@@@@@'.chr(10);

                        if($time=='0'){
                            $sql = "INSERT INTO RESTAURANT(r_name, r_api_id, r_img_link, r_total_time, r_category, r_total_count)".chr(10);
                            $sql .= "VALUES('" . $name ."', " . $id .", '" . $logo_url ."', " . $time . ", '" . $category ."', ". 0 .")".chr(10);
                        } else {
                            $sql = "INSERT INTO RESTAURANT(r_name, r_api_id, r_img_link, r_total_time, r_category)".chr(10);
                            $sql .= "VALUES('" . $name ."', " . $id .", '" . $logo_url ."', " . $time . ", '" . $category ."')".chr(10);
                        }

                        echo chr(10)."sql : " . $sql . chr(10);
                    }


                    echo chr(10). '###############################' . chr(10);

                    $this->dbconn->excute($sql);


                    #echo "index : " . $index . " 이름 : " . $name . " ID : " . $id . " deli_time : " . $delivery_time . " img : " . $logo_url . " time : " . $time . chr(10);


                }

            }
        }

        # print_r($arrResponse);
    }

    function getDeliveryTime($delivery_time){
        #$exRegTime = "!(^[0-9]*$).*?(^[0-9]*$)!is";
        #$exRegTime = "!([0-9]*$)~([0-9]*$)!is";
        #$exRegTime = "!^([0-9]*$)~([0-9]*$)!is";
        #$exRegTime = "!(.*?)!is";
        #$exRegTime = "!(^[0-9]+)[가-힣]*$!is";
        $exRegTime = "!^.*?([0-9]+)[가-힣]*$!is";

        if(preg_match_all($exRegTime, $delivery_time, $arrResult)){
            return $arrResult[1][0];
        }

        return false;
    }

    function subBranchName($name){
        $exRegName = "!(.*?)-!is";

        if(preg_match($exRegName, $name, $matches)){
            return $matches[1];
        }

        return $name;
    }

    function initMenuData()
    {
        $sql = "SELECT r_seq, r_api_id FROM RESTAURANT WHERE r_seq >= 194";
        $arrRestaurants = $this->dbconn->excuteArray($sql);

        foreach($arrRestaurants as $key => $restaurantInfo){

            $r_seq = $restaurantInfo['r_seq'];

            $r_api_id = $restaurantInfo['r_api_id'];

            #echo "r_seq : " . $r_seq . "r_api_id : " . $r_api_id.chr(10);

            $URLPrefix = "https://www.yogiyo.co.kr/api/v1/restaurants/";

            $URLSuffix = "/menu/?add_photo_menu=android&add_one_dish_menu=true&order_serving_type=delivery";

            $url = $URLPrefix  . $r_api_id. $URLSuffix;

            #echo "url : " . $url . chr(10);

            $RequestAPI = new RequestAPIData($this->apiInfo['arrCurlOptions']);

            $menuResponse = $RequestAPI->requestAPI($url);

            $arrResponse = json_decode($menuResponse, true);

            #print_r($arrResponse);

            $totalMenu = $arrResponse['0']['items'];

            foreach($totalMenu as $index => $menuInfo){


                #echo "index : " . $index . "value : " . $value;

                $name = $menuInfo['name'];
                $price = $menuInfo['price'];
                $img_link = $menuInfo['image'];


/*                echo "******************Menu INFO*****************".chr(10);
                echo "name :: " . $name . "price ::: " . $price . "img : " . $img_link.chr(10);
                echo "******************Menu INFO*****************".chr(10);*/


                $sql = "INSERT INTO FOOD(f_name, f_price, f_img_link, r_seq)".chr(10);
                $sql .= "VALUES('" . $name . "', '" .$price ."', '" . $img_link . "', " . $r_seq . ")";


/*                echo "****************SQL*****************";
                echo chr(10). $sql . chr(10);
                echo "****************SQL*****************";*/

                $this->dbconn->excute($sql);

            }

        }



    }

    function upgradeMenuData($startPage)
    {
        $sql = "SELECT r_seq, r_api_id FROM RESTAURANT";
        $arrRestaurants = $this->dbconn->excuteArray($sql);

        foreach($arrRestaurants as $key => $restaurantInfo){

            $r_seq = $restaurantInfo['r_seq'];

            $r_api_id = $restaurantInfo['r_api_id'];

            #echo "r_seq : " . $r_seq . "r_api_id : " . $r_api_id.chr(10);

            $URLPrefix = "https://www.yogiyo.co.kr/api/v1/restaurants/";

            $URLSuffix = "/menu/?add_photo_menu=android&add_one_dish_menu=true&order_serving_type=delivery";

            $url = $URLPrefix  . $r_api_id. $URLSuffix;

            #echo "url : " . $url . chr(10);

            $RequestAPI = new RequestAPIData($this->apiInfo['arrCurlOptions']);

            $menuResponse = $RequestAPI->requestAPI($url);

            $arrResponse = json_decode($menuResponse, true);

            #print_r($arrResponse);


            for($i = $startPage; $i < count($arrResponse); $i++){
                $totalMenu = $arrResponse[$i]['items'];

                foreach($totalMenu as $index => $menuInfo){


                    #echo "index : " . $index . "value : " . $value;

                    $name = $menuInfo['name'];
                    $price = $menuInfo['price'];
                    $img_link = $menuInfo['image'];
                    if($img_link == false){
                        $img_link = $menuInfo['original_image'];
                        if($img_link == false){
                            $img_link = '';
                        }
                    }


                                    echo "******************Menu INFO*****************".chr(10);
                                    echo "name :: " . $name . "price ::: " . $price . "img : " . $img_link.chr(10);
                                    echo "******************Menu INFO*****************".chr(10);


                    $sql = "INSERT INTO FOOD(f_name, f_price, f_img_link, r_seq)".chr(10);
                    $sql .= "VALUES('" . $name . "', '" .$price ."', '" . $img_link . "', " . $r_seq . ")";


                                    echo "****************SQL*****************";
                                    echo chr(10). $sql . chr(10);
                                    echo "****************SQL*****************";

                    $this->dbconn->excute($sql);

                }
            }

        }
    }

    function updateFoodImg(){
        $sql = "SELECT f_seq FROM FOOD" .chr(10);
        $sql .= "WHERE f_img_link IS NULL OR f_img_link = ''";

        $arrFSeqsImgNull = $this->dbconn->excuteArray($sql);

        foreach($arrFSeqsImgNull as $index => $arrFSeq){
            $fSeq = $arrFSeq['f_seq'];

            $sql = "UPDATE FOOD SET f_img_link = 'images/logo_main.png'" . chr(10);
            $sql .= "WHERE f_img_link IS NULL OR f_img_link = ''";

            $result = $this->dbconn->excute($sql);

            if($result == false){
                echo "ERROR!!! , f_seq : " . $fSeq .chr(10);
            } else {
                echo "SUCCES!, f_seq : " . $fSeq . chr(10);
            }

        }


    }

    function updateRestaurantTel(){

        $sql = "SELECT r_seq, r_name FROM RESTAURANT WHERE r_tel IS NULL";
        $restaurantsWithNoTelInfo = $this->dbconn->excuteArray($sql);

        #print_r($restaurantsWithNoTelInfo);

        $RequestAPI = new RequestAPIData($this->apiInfo['arrCurlOptions']);

        $naverPlaceURLPrefix = "https://map.naver.com/v5/api/search?query=";
        $naverPlaceURLSuffix = "&searchCoord=126.74634933471671;37.69655537319996";

        foreach($restaurantsWithNoTelInfo as $index => $restaurant){
            $r_seq = $restaurant['r_seq'];
            $r_name = $restaurant['r_name'];
            $subBranchName = $this->subBranchName($r_name);
            $urlEncodeName = urlencode($subBranchName);
            $ampersandReplacedName = $this->replaceAmpersandUnicodeToNormal($urlEncodeName);

            #echo "r_seq : " . $r_seq . " r_name : " . $r_name ." encodedName : " . $urlEncodeName. "subBranchName : " . $subBranchName . chr(10);

            $naverPlaceAPIURL = $naverPlaceURLPrefix. $ampersandReplacedName . $naverPlaceURLSuffix;

            #echo "URL : " . $naverPlaceAPIURL.chr(10);


            sleep(rand(2, 6));

            $naverPlaceAPIResponse = $RequestAPI->requestNaverPlaceAPI($naverPlaceAPIURL);

            $arrNaverResponse = json_decode($naverPlaceAPIResponse, true);

/*            echo chr(10). '################tel############'.chr(10);*/
            if(isset($arrNaverResponse['result']['place']['list'][0]['tel'])){
                $tel = $arrNaverResponse['result']['place']['list'][0]['tel'];
            }


            $telDisplay = $arrNaverResponse['result']['place']['list'][0]['telDisplay'];

/*            echo 'telDisplay : ' . $telDisplay . chr(10);
            echo "######################" . chr(10);*/

            if(!($tel=='' && $telDisplay=='')){
                echo 'placeName : ' . $r_name .' tel : ' . $tel . chr(10);

                if(!$telDisplay=='') $tel = $telDisplay;

                $sql = "UPDATE RESTAURANT SET r_tel = '".$tel."'".chr(10);
                $sql .= "WHERE r_seq = ".$r_seq.chr(10);

                echo chr(10)."sql : " . $sql . chr(10);

                $this->dbconn->excute($sql);

            } else {
                echo "############FAIL#############" . chr(10);
                echo 'placeName : ' . $r_name .' url : ' . $naverPlaceAPIURL . chr(10);
            }

        }
    }

    function replaceAmpersandUnicodeToNormal($text){
        $replaceText = str_replace("%26", "&", $text);
        return $replaceText;
    }

    function endAPI(){
        $this->dbconn->quit();
    }

}

?>