<?
    $API_INFO_RESTAURANT['urlParameter'] = array(
        'items' => 100,
        'lat' => 37.6657483324654,
        'lng' => 126.750200196769,
        'order' => 'rank'
    );
    $API_INFO_RESTAURANT['arrCurlOptions'] = array(
        CURLOPT_HTTPHEADER => array(
            'x-apisecret: fe5183cc3dea12bd0ce299cf110a75a2',
            'x-apikey: iphoneap',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
        ),
        CURLOPT_POST => 0,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLINFO_HEADER_OUT => false,
        CURLOPT_HEADER => false,
        CURLOPT_CONNECTTIMEOUT => 60,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => 'gzip,deflate'
    );

    $DB_INFO = array(
        'host' => '112.172.163.139:3306',
        'id' => 'yagola',
        'pass' => 'engine!@#',
        'db' => 'yagola');

?>


