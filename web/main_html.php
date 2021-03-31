

<span class="page_location" onmouseover="changeCursor(this, 'default')"> <img class="page_location_img"src="images/logo_background.png"> 야골라 / 메인 </span>

<div class="recommend_area">

    <div class="menu_area">


        <div class="recommend_menu" onmouseover="changeCursor(this, 'default')" title="주문 수가 높은 추천 메뉴에오 :)">
            <h2><i class="fas fa-utensils"></i>
                추천 메뉴
                <i class="fas fa-utensils"></i>
            </h2>
        </div>


        <div class="slide_wrapper">
            <div class="slides" id="slides">

            </div>
        </div>

    </div>


    <div class="restaurant_area">

        <div class="recommend_restaurant" onmouseover="changeCursor(this, 'default')" title="주문 수가 높은 추천 식당이에오 :)">
            <h2><i class="fas fa-store"></i>
                추천 식당
                <i class="fas fa-store"></i>
            </h2>
        </div>


        <div class="slide_wrapper">
            <div class="slides" id="slides_restaurant">

            </div>
        </div>

    </div>


</div>

<div id="dailyChart" class="chart"></div>

<div id="monthChart" class="chart"></div>


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

