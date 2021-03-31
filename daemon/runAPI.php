<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<?

    require_once('../api/API.php');
    require_once('../class/config.php');

    $apiRestaurant = new API($API_INFO_RESTAURANT, $DB_INFO);
    $apiRestaurant->setURL("https://www.yogiyo.co.kr/api/v1/restaurants-geo/?");
    #$apiRestaurant->initData();
    #$apiRestaurant->getDeliveryTime("60~70분");
    #$apiRestaurant->initMenuData();
    //$apiRestaurant->updateRestaurantTel();
    #$apiRestaurant->replaceAmpersandUnicodeToNormal("%EC%9D%B4%EC%82%AD%EC%86%8C%EB%B0%94%26%EB%8F%88%EA%B9%8C%EC%8A%A4");
    //$apiRestaurant->endAPI();
    //$apiRestaurant->upgradeMenuData(1);
    $apiRestaurant->updateFoodImg();
?>
