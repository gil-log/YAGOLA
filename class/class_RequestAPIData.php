<?

class RequestAPIData
{
    public $arrCurlOptions;


    function __construct($arrCurlOptions)
    {
        $this->arrCurlOptions = $arrCurlOptions;
    }

    function requestAPI($url)
    {
        $cURL = curl_init();
        $header = '';
        $htmlSource = 'error';

        $this->arrCurlOptions[CURLOPT_URL] = $url;

        if( curl_setopt_array( $cURL, $this->arrCurlOptions ) ) {
            if (($htmlSource = curl_exec($cURL)) === false) {
                $htmlSource = curl_exec($cURL);
            }

        }

        if(preg_match_all('!(HTTP/.*?(?:\s){4,})!s', $htmlSource, $arrSet)){
            if(isset($arrSet[1])){
                if(count($arrSet[1]) > 0){
                    foreach($arrSet[1] as $idx => $http_header){
                        $header .= chr(10).$http_header;
                        if($htmlSource == null){
                            $htmlSource = str_replace($http_header, '', $htmlSource);
                        }else{
                            $htmlSource = str_replace($http_header, '', $htmlSource);
                        }
                    }
                }
            }
        }

        curl_close($cURL);

        return $htmlSource;
    }

    function requestNaverPlaceAPI($url)
    {
        $cURL = curl_init();
        $header = '';
        $htmlSource = 'error';

        #echo $url;

        $naverArrCurlOptions =array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
            ),
            CURLOPT_POST => 0,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLINFO_HEADER_OUT => false,
            CURLOPT_HEADER => false,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36',
            CURLOPT_ENCODING => 'gzip,deflate',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_MAXREDIRS => 10

        );
        if( curl_setopt_array( $cURL, $naverArrCurlOptions ) ) {
            if (($htmlSource = curl_exec($cURL)) === false) {

                echo "###########error###########".chr(10);

                echo curl_error($cURL);

                echo chr(10) . "curl_exec fail!! " . chr(10);

                $htmlSource = curl_exec($cURL);
            }

            #echo 'url : ' . $url . ' curl_exec : ' . $htmlSource;

        }

        if(preg_match_all('!(HTTP/.*?(?:\s){4,})!s', $htmlSource, $arrSet)){
            if(isset($arrSet[1])){
                if(count($arrSet[1]) > 0){
                    foreach($arrSet[1] as $idx => $http_header){
                        $header .= chr(10).$http_header;
                        if($htmlSource == null){
                            $htmlSource = str_replace($http_header, '', $htmlSource);
                        }else{
                            $htmlSource = str_replace($http_header, '', $htmlSource);
                        }
                    }
                }
            }
        }

        curl_close($cURL);

        return $htmlSource;
    }
}

?>