<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1.0, minimum-scale=1"/>
    <title>야 골라</title>

    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon"/>

    <link rel="stylesheet" type="text/css" href="main.css"/>
    <link rel="stylesheet" href="lib/fontawesome/css/all.css">

    <script type="text/javascript" src="fun_common.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Black+Han+Sans&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Do+Hyeon&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jua&display=swap" rel="stylesheet">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="http://code.highcharts.com/highcharts.js"></script>

    <script>
        var playMenuSlide;

        var playRestaurantSlide;

        function search(s_flag, s_keyword) {

        }

        function directPage(pageName) {
            var page = pageName + ".php";
            document.location.href = page;
        }

        function checkDepartmentInfo() {

            if(!isDepartmentInfoSetUp()){

                chooseDepartment();
            }

        }

        function isDepartmentInfoSetUp(){

            var cookieDepartment = getCookie("cookie_department");
            var favorite_dept_flag = document.getElementById('favorite_dept_flag');

            if (cookieDepartment == null || cookieDepartment == 0) {

                favorite_dept_flag.value = 0;

                return false;
            }

            favorite_dept_flag.value = 1;

            return true;
        }

        function chooseDepartment() {
            modal('main_modal');
        }

        function checkAccessInfo() {

            let url = "accessInfo.php";
            let method = "POST";
            let data = null;
            var callback = function (data) {
                alert("Access IP : " + data);
            };

            callAjax(url, method, data, callback);

        }

        function getRecommendFood() {

            let url = "recommend_menu_html.php";
            let method = "POST";
            var callback = setRecommendFood;

            callAjax(url, method, null, callback);
        }

        function setRecommendFood(data) {

            var result = data;

            var slides = document.getElementById("slides");

            slides.innerHTML = result;

        }

        function clickContent(r_seq) {

            if(!isDepartmentInfoSetUp()){
                alert('부서 설정을 해주세요!');

                modal('main_modal');
            } else {

                let url = "order_html.php";
                let method = "POST";
                let sendData = {
                    r_seq: r_seq,
                    page: 0
                };

                callAjax(url, method, sendData, setOrderModal);
            }

        }

        function deleteFavoriteDepartment(d_seq){

            isDepartmentInfoSetUp();
            var favorite_dept_flag = document.getElementById('favorite_dept_flag');

            var favorite_flag = favorite_dept_flag.value;

            if(favorite_flag == 0){
                chooseDepartment();
                return
            }

            favorite_dept_flag = 0;

            var isUserAgreed = confirm("설정된 부서가 삭제되요.. \n부서 삭제 후 재 등록도 되여!");

            if(isUserAgreed){
                let url = "department.php";
                let method = "PATCH";
                let sendData = {
                    d_seq: d_seq
                };

                callAjax(url, method, sendData, changeDepartment);
            }


        }

        function changeDepartment(data){

            deleteCookie('cookie_department');

            chooseDepartment();

            var favoriteDept = document.getElementById('favorite_dept');

            favoriteDept.text = '부서 미지정';

        }

        function applyFavoriteDepartment(){

            var dSeq = getCookie("cookie_department");

            let url = "department_favorite_html.php";
            let method = "POST";
            let sendData = {
                d_seq: dSeq
            };

            callAjax(url, method, sendData, setFavoriteDepartment);

        }

        function setFavoriteDepartment(data){

            var favoriteDept = document.getElementById('favorite_dept');

            favoriteDept.innerHTML = data;

        }

        function selectDepartment(d_seq, d_name) {

            //alert("selected D_seq : " + d_seq + d_name);

            var cookieName = "cookie_department";

            setCookie(cookieName, d_seq);

            var favorite_dept_flag = document.getElementById('favorite_dept_flag');

            favorite_dept_flag = 1;

            /* var modal = document.getElementById("main_modal");
             var modalBG = document.getElementById("modal_bg");
             modalBG.remove();
             modal.style.display = 'none';*/

            var sendData = {d_seq: d_seq, d_name: d_name};

            callAjax("department_done_html.php", "POST", sendData, setDepartmentDone);

        }

        function deleteCookie(cookieName){
            setCookie(cookieName, "", null , null , null, 1);
        }

        function orderNextMenu(r_seq, page){

            let url = "order_page_menu_html.php";
            let method = "POST";
            let sendData = {
                r_seq: r_seq,
                page: page
            };

            callAjax(url, method, sendData, changeMenuContentsInModal);

        }

        function changeMenuContentsInModal(data){

            var orderMenuArea = document.getElementById('order_menu_area');

            while(orderMenuArea.hasChildNodes()){
                orderMenuArea.removeChild(orderMenuArea.firstChild);
            }

            orderMenuArea.innerHTML = data;

        }

        function orderRecruitNextMenu(r_seq, page, o_seq, d_seq, o_ip){

            let url = "order_recruit_page_menu_html.php";
            let method = "POST";
            let sendData = {
                r_seq: r_seq,
                page: page,
                o_seq : o_seq,
                d_seq : d_seq,
                o_ip : o_ip
            };

            callAjax(url, method, sendData, changeMenuContentsInRecruitModal);

        }

        function changeMenuContentsInRecruitModal(data){

            var orderMenuArea = document.getElementById('order_menu_area');

            while(orderMenuArea.hasChildNodes()){
                orderMenuArea.removeChild(orderMenuArea.firstChild);
            }

            orderMenuArea.innerHTML = data;

        }

        function createOrder(r_seq){

            var isUserAgreed = confirm("주문 모집을 생성하시겠어요?");

            if(isUserAgreed){
                let url = "order_create.php";
                let method = "POST";
                let sendData = {
                    r_seq: r_seq
                };

                callAjax(url, method, sendData, afterCreateOrder);

            }


        }

        function afterCreateOrder(data){

            var orderDoneArea = document.getElementById('order_done_area');

            orderDoneArea.innerHTML = data;

            cancelModal('modal_order');


            modal('modal_order_done');

        }

        function setOrderModal(data) {

            var result = data;

            var orderArea = document.getElementById("order_area");

            orderArea.innerHTML = result;

            modal("modal_order");

        }

        function getRecommendRestaurant() {

            let url = "recommend_restaurant_html.php";
            let method = "POST";
            var callback = setRecommendRestaurant;

            callAjax(url, method, null, callback);

        }

        function setRecommendRestaurant(data) {

            var result = data;

            var slideRestaurant = document.getElementById("slides_restaurant");

            slideRestaurant.innerHTML = result;

        }

        function selectGroup(departmentFlag) {

            var sendData = {d_flag: departmentFlag};

            callAjax("department_list_html.php", "POST", sendData, replaceModalContent);

        }

        function replaceModalContent(data) {

            var result = data;

            var departmentArea = document.getElementById("department_area");

            var departmentListArea = document.getElementById("department_list_area");

            departmentArea.setAttribute('style', 'display:none');

            departmentListArea.innerHTML = result;

            departmentListArea.setAttribute('style', 'display:block');

        }

        function setDepartmentDone(data) {
            var result = data;
            //alert(result);
            var departmentListArea = document.getElementById("department_list_area");

            departmentListArea.setAttribute('style', 'display:none');


            var departmentDoneArea = document.getElementById("department_done_area");

            departmentDoneArea.innerHTML = result;
            departmentDoneArea.setAttribute('style', 'display:block');

        }


        function closeModal(id) {
            var modal = document.getElementById(id);
            var modalBG = document.getElementById(id + "_bg");

            modalBG.remove();
            modal.style.display = 'none';

            var favoriteDept = document.getElementById('favorite_dept');

            if(favoriteDept == null){
                directPage('main');
            } else{
                applyFavoriteDepartment();
            }


            var page_flag = document.getElementById('page_flag').value;

            if(page_flag == 'main'){
                getMainHtml();
            } else if (page_flag == 'search'){
                searchKeyword();
            } else if (page_flag == 'department'){
                getDepartmentHtml(0);
            }


        }

        function cancelModal(id){
            var modal = document.getElementById(id);
            var modalBG = document.getElementById(id + "_bg");

            modalBG.remove();
            modal.style.display = 'none';
        }

        function changeOrderMenu(buttonType){
            var orderMenuArea = document.getElementById('order_menu_area');

            var contents = orderMenuArea.querySelectorAll('.order_menu_content');

            var nextContents = orderMenuArea.querySelectorAll('.order_menu_content_next');

            if(buttonType == 'next'){
                for(var i = 0; i < 2; i++){
                    contents[i].setAttribute('class', 'order_menu_content_next');
                    nextContents[i].setAttribute('class', 'order_menu_content');
                }
            }else if(buttonType == 'previous'){
                for(var i = 0; i < 2; i++){
                    nextContents[i].setAttribute('class', 'order_menu_content');
                    contents[i].setAttribute('class', 'order_menu_content_next');
                }
            }
        }

        function modal(id) {
            var zIndex = 9999;
            var modal = document.getElementById(id);

            // 모달 div 뒤에 희끄무레한 레이어
            var bg = document.createElement('div');
            bg.setAttribute("id", id + "_bg");
            bg.setStyle({
                position: 'fixed',
                zIndex: zIndex,
                left: '0px',
                top: '0px',
                width: '100%',
                height: '100%',
                overflow: 'auto',
<<<<<<< HEAD
                backgroundColor: 'rgba(0,0,0,0.0)'
=======
                backgroundColor: 'rgba(0,0,0,0.4)'
>>>>>>> c7f49fc2ad5b8efcdac6d32aa41ba68359edac53
            });
            document.body.append(bg);

            modal.querySelector('.modal_close_btn').addEventListener('click', function () {
                bg.remove();
                modal.style.display = 'none';
            });

            modal.setStyle({
                position: 'fixed',
                display: 'block',
                boxShadow: '0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)',

                // 시꺼먼 레이어 보다 한칸 위에 보이기
                zIndex: zIndex + 1,

                // div center 정렬
                top: '50%',
                left: '50%',
                transform: 'translate(-50%, -50%)',
                msTransform: 'translate(-50%, -50%)',
                webkitTransform: 'translate(-50%, -50%)'
            });
        }

        // Element 에 style 한번에 오브젝트로 설정하는 함수 추가
        Element.prototype.setStyle = function (styles) {
            for (var k in styles) this.style[k] = styles[k];
            return this;
        };

        function slideMenu() {

            var slides = document.getElementById("slides");

            var firstContent = slides.querySelector('.content');

            var firstNextContent = slides.querySelector('.content_next');

            firstNextContent.setAttribute('class', 'content');

            slides.append(firstContent);

            firstContent.setAttribute('class', 'content_next');

<<<<<<< HEAD
=======

>>>>>>> c7f49fc2ad5b8efcdac6d32aa41ba68359edac53
        }

        function slideRestaurant() {

            var slides = document.getElementById("slides_restaurant");

            var firstContent = slides.querySelector('.content');

            var firstNextContent = slides.querySelector('.content_next');

            if (firstNextContent != null) {

                firstNextContent.setAttribute('class', 'content');

                slides.append(firstContent);

                firstContent.setAttribute('class', 'content_next');
            }

        }

        function startSlide(type, time) {

            if (type == 'menu') {
                playMenuSlide = setInterval(slideMenu, time);
            } else {
                playRestaurantSlide = setInterval(slideRestaurant, time);
            }

        }


        function searchKeyword(){

            unsetCategoryBoxColored();

            var checkedBox = document.querySelector("input[type='radio']:checked");

            var s_criteria = checkedBox.value;

            var s_flag = 'keyword';

            var page = 0;

            var s_keyword = document.getElementById("s_keyword").value;

            if(!isValidKeyword(s_keyword)){

                alert("약오르게 막아버리기 ㅋㅋ");

                return false;

            }


            var section = document.getElementById("section");

            while(section.hasChildNodes()){
                section.removeChild(section.firstChild);
            }



            let url = "search_html.php"

            let method = "POST";

            let sendData = {
                s_flag : s_flag,
                s_criteria : s_criteria,
                s_keyword : s_keyword,
                page : page
            };

            callAjax(url, method, sendData, setSearchHtml);





        }

        function nextSearchPage(s_flag, s_criteria, s_keyword, page){

            let url = "search_html.php"

            let method = "POST";

            let sendData = {
                s_flag : s_flag,
                s_criteria : s_criteria,
                s_keyword : s_keyword,
                page : page
            };

            callAjax(url, method, sendData, setSearchHtml);
        }

        function searchCategory(s_keyword, isImgClickedFlag=true){

            if(isImgClickedFlag){
                setCategoryBoxColored(s_keyword);
            }

            var checkedBox = document.querySelector("input[type='radio']:checked");

            var s_criteria = checkedBox.value;

            var s_flag = 'category';

            var page = 0;

            let url = "search_html.php"

            let method = "POST";

            let sendData = {
                s_flag : s_flag,
                s_criteria : s_criteria,
                s_keyword : s_keyword,
                page : page
            };

            callAjax(url, method, sendData, setSearchHtml);

        }

        function setSearchHtml(data){

            var page_flag = document.getElementById('page_flag');

            page_flag.value = 'search';

            resetSlideInterval();

            var section = document.getElementById("section");

            while(section.hasChildNodes()){
                section.removeChild(section.firstChild);
            }

            section.innerHTML = data;

            var criteriaTable = document.getElementsByClassName("criteria_table");

            criteriaTable[0].setAttribute('style', 'display: block;')

        }

        function setCategoryBoxColored(s_keyword){

            var id = '';

            if(s_keyword == 'kor'){
                id = 'category_kor';
            } else if(s_keyword == 'wes'){
                id = 'category_wes';
            } else if(s_keyword == 'jap'){
                id = 'category_jap';
            } else if(s_keyword == 'chi'){
                id = 'category_chi';
            }

            var categoryDiv = document.getElementById(id);

            var categoryDivClass = categoryDiv.getAttribute("class");


            unsetCategoryBoxColored();

            if(categoryDivClass == 'category'){

                categoryDiv.setAttribute('class', 'checked_category');

            } else if (categoryDivClass == 'checked_category'){

                categoryDiv.setAttribute('class', 'category');

            }

        }


        function unsetCategoryBoxColored(){

            var anotherCheckedDiv = document.getElementsByClassName("checked_category");

            if(anotherCheckedDiv.length != 0) {

                anotherCheckedDiv[0].setAttribute('class', 'category');

            }
        }

        function isValidKeyword(keyword){

            var injectionFlag = false;
            var specialSymbolFlag = false;

            var arrInjectionRegExp = Array(
                "select", "where", "or", "and"
            );

            for(var i = 0 ; i < arrInjectionRegExp.length; i++){
                var pattern = arrInjectionRegExp[i];
                var injectionRegExp = new RegExp(pattern, 'i');

                if(injectionRegExp.test(keyword)){
                    injectionFlag = true;
                }

            }

            var arrSpecialSymbolRegExp = Array(
                "[~!@#$%^&*()_+|<>?:{}]"
                //"%", "=", "`", "|", "@", "#", "$", "^", "&", "(", ")"
            )

            for(var i = 0 ; i < arrSpecialSymbolRegExp.length; i++){
                var pattern = arrSpecialSymbolRegExp[i];
                var specialSymbolRegExp = new RegExp(pattern, 'i');

                if(specialSymbolRegExp.test(keyword)){
                    specialSymbolFlag = true;
                }
            }

            if( injectionFlag || specialSymbolFlag ){

                return false;
            }

            return true;
        }


        function checkCriteria(id){

            var s_criteria = document.getElementById(id);

            s_criteria.checked = true;

            if(id=='s_criteria_time'){
                var s_criteria_count = document.getElementById('s_criteria_count');
                s_criteria_count.checked = false;

            }else{
                var s_criteria_time = document.getElementById('s_criteria_time');
                s_criteria_time.checked = false;
            }

            var checkedCategoryDiv = document.getElementsByClassName("checked_category");

            if(checkedCategoryDiv[0] != null){
                var category_id = checkedCategoryDiv[0].getAttribute("id");

                if(category_id == 'category_kor'){
                    searchCategory('kor', false);
                } else if(category_id == 'category_wes'){
                    searchCategory('wes', false);
                } else if(category_id == 'category_jap'){
                    searchCategory('jap', false);
                } else if(category_id == 'category_chi'){
                    searchCategory('chi', false);
                }
            } else{
                searchKeyword();
            }

            /*            var search_paging_previous = document.getElementById("search_paging_previous");
             if(search_paging_previous != null){
             var search_paging_previous_onclick = search_paging_previous.getAttribute("onclick");
             var onclickArgumentsRegExp = new RegExp("\'.*?\'", "g");
             var regExpResult = onclickArgumentsRegExp.exec(search_paging_previous_onclick);
             alert(regExpResult);
             }*/

        }

        function getMainHtml(){

            var page_flag = document.getElementById('page_flag');

            page_flag = 'main';

            //deleteModal('main_modal');

            let url = "main_html.php";
            let method = "POST";
            let sendData = null;
            let callback = setMainHtml;

            callAjax(url, method, sendData, callback);

        }

        function deleteModal(id){
            var modal = document.getElementById(id);

            if(modal != null){
                modal.parentNode.removeChild(modal);
            }
        }

        function clickDeptImg(dept_name){

            var d_flag = '';


        }

        function clickOrder(o_seq, r_seq, page){

            var favorite_dept_flag = document.getElementById('favorite_dept_flag');

            var favorite_flag = favorite_dept_flag.value;

            if(favorite_flag == 0){

                alert('부서 설정을 해주세요!');

                chooseDepartment();

                return
            }

            let url = "order_recruit_is_accessible.php";
            let method = "POST";
            let sendData = {
                o_seq : o_seq,
                r_seq : r_seq,
                page : page
            };
            let callback = isAccessibleOrder;

            callAjax(url, method, sendData, callback);

        }

        function isAccessibleOrder(data){

            var result = JSON.parse(data);

            let isAccessible = result['result'];
            if(isAccessible == 'ok'){
                let o_seq = result['o_seq'];
                let r_seq = result['r_seq'];
                let page = result['page'];

                let url = "order_recruit_html.php";
                let method = "POST";
                let sendData = {
                    o_seq : o_seq,
                    r_seq : r_seq,
                    page : page
                };
                let callback = setRecruitModal;

                callAjax(url, method, sendData, callback);
            } else {
                let comment = result['comment'];
                alert(comment);
            }

        }

        function setRecruitModal(data){

            var recruitArea = document.getElementById('recruit_area');

            recruitArea.innerHTML = data;

            modal('modal_recruit');

        }

        function clickRecruitMenu(o_seq, r_seq, f_seq, d_seq, o_ip){

            var isUserAgreed = confirm("해당 메뉴를 선택하시겠어요?");

            if(isUserAgreed){

                let url = "order_recruit_select_menu_html.php";
                let method = "POST";
                let sendData = {
                    o_seq : o_seq,
                    r_seq : r_seq,
                    f_seq : f_seq,
                    d_seq : d_seq,
                    o_ip : o_ip
                };
                let callback = selectMenuInRecruit;

                callAjax(url, method, sendData, callback);
            }

        }

        function selectMenuInRecruit(data){
            var result = JSON.parse(data);
            alert(result['result']);

            let o_seq = result['o_seq'];
            let r_seq = result['r_seq'];
            let page = 0;

            //closeModal('modal_recruit');

            clickOrder(o_seq, r_seq, page);

        }

        function enterQuestion(id, o_seq, v_ip, r_seq){

            if(window.event.keyCode == 13){

                var input = document.getElementById(id);

                var text = input.value;

                let url = "order_recruit_register_question.php";
                let method = "POST";
                let sendData = {
                    text : text,
                    o_seq : o_seq,
                    v_ip : v_ip,
                    r_seq : r_seq
                };

                let callback = registerQuestion;

                callAjax(url, method, sendData, callback);

            }
        }

        function registerQuestion(data){

            var result = JSON.parse(data);

            //alert(result['result']);

            let o_seq = result['o_seq'];
            let r_seq = result['r_seq'];
            let page = 0;

            //closeModal('modal_recruit');

            clickOrder(o_seq, r_seq, page);

        }

        function changeRecruitStatus(o_seq, r_seq, o_status){

            var isUserAgreed = confirm("모집 상태를 변경하시겠어요?");

            if(isUserAgreed){

                var url = 'order_recruit_change_status.php';
                var method = 'POST';
                var sendData = {
                    o_seq : o_seq,
                    o_status : o_status,
                    r_seq : r_seq
                };
                var callback = afterRecruitStatusChanged;

                callAjax(url, method, sendData, callback);
            }


        }

        function afterRecruitStatusChanged(data){

            var result = JSON.parse(data);

            if(result['result'] == 'done'){

                closeModal('modal_recruit');

                let count = result['count'];
                let time = result['time'];

                alert("대표로 골라줘서 고마워요잉 :)\n" + count +"명이 "+time+" 분 걸렸네요!");

                return
            }

            let o_seq = result['o_seq'];
            let r_seq = result['r_seq'];
            let page = 0;

            closeModal('modal_recruit');

            clickOrder(o_seq, r_seq, page);
        }



        function getDepartmentHtml(d_flag){

            var page_flag = document.getElementById('page_flag');

            page_flag.value = 'department';

            resetSlideInterval();

            deleteModal('main_modal');

            let url = "department_page_html.php";
            let method = "POST";
            let sendData = {d_flag : d_flag};
            let callback = setDepartmentHtml;

            callAjax(url, method, sendData, callback);

        }

        function setDepartmentHtml(data){

            unsetCategoryBoxColored();

            var section = document.getElementById("section");

            while(section.hasChildNodes()){
                section.removeChild(section.firstChild);
            }

            section.innerHTML = data;

            checkDepartmentInfo();

            var d_flag = document.getElementById('d_flag');

            var d_flag_value = d_flag.value;

            unsetDepartmentCategoryBoxColored();

            setDepartmentCategoryBoxColored(d_flag_value);

            var criteriaTable = document.getElementsByClassName("criteria_table");

            criteriaTable[0].setAttribute('style', 'display: none;')

        }

        function unsetDepartmentCategoryBoxColored(){

            var department_category_detail_area_picked = document.getElementsByClassName('department_category_detail_area');

            if(department_category_detail_area_picked[0] != null){
                department_category_detail_area_picked[0].setAttribute('class', 'department_category_detail_area');
            }

        }

        function setDepartmentCategoryBoxColored(d_flag){

            var department_category_detail_area = document.getElementsByClassName('department_category_detail_area');

            department_category_detail_area[d_flag].setAttribute('class', 'department_category_detail_area_picked');

        }

        function setMainHtml(data){

            unsetCategoryBoxColored();

            var section = document.getElementById("section");

            while(section.hasChildNodes()){
                section.removeChild(section.firstChild);
            }

            section.innerHTML = data;

            startMainSlide();

            var criteriaTable = document.getElementsByClassName("criteria_table");

            criteriaTable[0].setAttribute('style', 'display: none;')

            getChartData();

        }

        function resetSlideInterval(){

            clearInterval(playMenuSlide);
            clearInterval(playRestaurantSlide);

        }

        function startMainSlide(){
            getRecommendFood();

            getRecommendRestaurant();

            resetSlideInterval();

            playMenuSlide = setInterval(slideMenu, 3000);
            playRestaurantSlide = setInterval(slideRestaurant, 3000);
        }

        function initCookie(){

            var cookieName = "cookie_department";

            var cookie = getCookie(cookieName);

            if(cookie == null){

                setCookie(cookieName, 0);

            }

        }

        function changeCursor(id, type){
            id.style.cursor = type;
        }

        function deleteContent(type, data){

            var isUserAgreed = confirm('해당 모집 글이 삭제 됩니다. \n 정말 삭제 하시겠어요?');

            if(isUserAgreed){

                let url = "delete_content.php";
                let method = "POST";
                let sendData = {
                    type : type,
                    target : data};
                let callback = afterDeleteContent;

                callAjax(url, method, sendData, callback);

            }
        }

        function afterDeleteContent(data){
            var jsonResult = JSON.parse(data);

            let result = jsonResult['result'];
            let message = jsonResult['message'];
            let modal = jsonResult['modal'];
            let o_seq = jsonResult['o_seq'];
            let r_seq = jsonResult['r_seq'];

            alert(message);

            if(modal != ''){
                closeModal(modal);

                getDepartmentHtml(0);

            } else {

                clickOrder(o_seq, r_seq, 0);

            }
        }

        function getChartData(){
            let url = "chart_data.php";
            let method = "GET";
            let sendData = null;
            let callback = initChart;

            callAjax(url, method, sendData, callback);
        }

        function initChart(data){

            var jsonResult = JSON.parse(data);

            var dailyData = jsonResult['daily'];

            var monthlyData = jsonResult['monthly'];

            var dailyCategories = [];
            var dailyRTime = [];
            var dailyRCount = [];

            var monthlyCategories = [];
            var monthlyRTime = [];
            var monthlyRCount = [];

            if(dailyData == null){
                dailyCategories.push('없어용');
                dailyRTime.push(0);
                dailyRCount.push(0);
            } else {
                for(var i = 0 ; i < dailyData.length; i++){

                    var r_name = dailyData[i]['r_name'];
                    var r_time = dailyData[i]['r_time'] * 1;
                    var count = dailyData[i]['count'] * 1;

                    dailyCategories.push(r_name);
                    dailyRTime.push(r_time);
                    dailyRCount.push(count);
                }
            }

            if(monthlyData == null){
                monthlyCategories.push('없어용');
                monthlyRTime.push(0);
                monthlyRCount.push(0);
            } else {
                for(var i = 0 ; i < monthlyData.length; i++){

                    var r_name = monthlyData[i]['r_name'];
                    var r_time = monthlyData[i]['r_time'] * 1;
                    var count = monthlyData[i]['count'] * 1;

                    monthlyCategories.push(r_name);
                    monthlyRTime.push(r_time);
                    monthlyRCount.push(count);
                }
            }

            $('#dailyChart').highcharts({
                chart: {
                    type: 'bar'
                },
                title: {
                    text: '일별 주문 현황'
                },
                xAxis: {
                    categories: dailyCategories
                },
                yAxis: {
                    title: {
                        text: '주문 수'
                    }
                },
                series: [{
                    name: '배달 시간',
                    data: dailyRTime
                }, {
                    name: '주문 수',
                    data: dailyRCount
                }]
            });

            $('#monthChart').highcharts({
                chart: {
                    type: 'bar'
                },
                title: {
                    text: '월별 주문 현황'
                },
                xAxis: {
                    categories: monthlyCategories
                },
                yAxis: {
                    title: {
                        text: '주문 수'
                    }
                },
                series: [{
                    name: '배달 시간',
                    data: monthlyRTime
                }, {
                    name: '주문 수',
                    data: monthlyRCount
                }]
            });
        }

        document.addEventListener("DOMContentLoaded", function () {

            initCookie();

            getMainHtml();

            //setDepartmentCookie(123);
            //checkAccessInfo();
            //deleteCookie("cookie_department");

            // 마지막 창
            checkDepartmentInfo();

            startMainSlide();


            getChartData();

        });


    </script>
</head>

<body>

​
<div id="wrapper">

    <input type="hidden" id="favorite_dept_flag" value="0">
    <input type="hidden" id="page_flag" value="main">
    <div id="header">

        <table class="header_table_left">
            <tr ><td ><img style="width: 100%" src= "images/logo_main.png" onmouseover="changeCursor(this, 'pointer')" title="야골라 메인 이동" onclick="getMainHtml()"></td></tr>
            <tr><td>야 골라</td></tr>
        </table>

        <div align="center" id="header_center">

            <div class="search_area">
                <div class="search_criteria">

                    <div class="criteria_area_time">

                    </div>

                    <table class="search_table">
                        <tr>

                            <td rowspan="4" width="60%"  >
                                <input type="text" class="search_bar" id="s_keyword" placeholder="식당이나 메뉴명을 검색 해보세요! :)">

                            </td>
                            <td rowspan="4" width="10%" onmouseover="changeCursor(this, 'pointer')" title="검색 하시려면 눌러주세요! :)">
                                <img src="images/find_icon.png" style="width: 100%;" onclick="searchKeyword()">

                            </td>
                        </tr>

                        <tr height="40">

                        </tr>

                    </table>

                    <div class="criteria_area_count">

                    </div>

                </div>
            </div>


            <table id="category_area">
                <tr>
                    <td class="category" id="category_kor" onmouseover="changeCursor(this, 'pointer')" title="클릭 시 한식 카테고리 식당들이 나와요 :)">
                        <img src="images/category_kor.png" onclick="searchCategory('kor')" style="width: 100%;">

                    </td>
                    <td class="category" id="category_wes" onmouseover="changeCursor(this, 'pointer')"  title="클릭 시 양식 카테고리 식당들이 나와요 :)">
                        <img src="images/category_wes.png" onclick="searchCategory('wes')" style="width: 100%;">

                    </td>
                    <td class="category" id="category_jap" onmouseover="changeCursor(this, 'pointer')" title="클릭 시 일식 카테고리 식당들이 나와요 :)">
                        <img src="images/category_jap.png" onclick="searchCategory('jap')" style="width: 100%;">

                    </td>
                    <td class="category" id="category_chi" onmouseover="changeCursor(this, 'pointer')" title="클릭 시 중식 카테고리 식당들이 나와요 :)">
                        <img src="images/category_chi.png" onclick="searchCategory('chi')" style="width: 100%;">

                    </td>
                </tr>

                <tr style="font-size: 3vh;">
                    <td class="category" id="category_kor" onmouseover="changeCursor(this, 'pointer')" title="클릭 시 한식 카테고리 식당들이 나와요 :)">

                        <a>한식</a>
                    </td>
                    <td class="category" id="category_wes" onmouseover="changeCursor(this, 'pointer')" title="클릭 시 양식 카테고리 식당들이 나와요 :)">
                        <a>양식</a>

                    </td>
                    <td class="category" id="category_jap" onmouseover="changeCursor(this, 'pointer')" title="클릭 시 일식 카테고리 식당들이 나와요 :)">
                        <a>일식</a>

                    </td>
                    <td class="category" id="category_chi" onmouseover="changeCursor(this, 'pointer')" title="클릭 시 중식 카테고리 식당들이 나와요 :)">
                        <a>중식</a>
                    </td>
                </tr>

            </table>

        </div>



            <table class="header_table_right">
                <tr ><td ><img style="width: 100%" src= "images/dept_logo.png" onmouseover="changeCursor(this, 'pointer')" title="야골라 주문 모집 이동" onclick="getDepartmentHtml(0)"></td></tr>
                <tr><td>모집 글 보기</td></tr>
            </table>



    </div>


    <table class="criteria_table">
        <tr>
    <td  onmouseover="changeCursor(this, 'pointer')" title="배달 빠른 순으로 보여 줄게여 :)">
        <p><span class="criteria" style="color:#3498DB;"><i class="fas fa-stopwatch">&nbsp;</i>시간 순</span></p>

    </td>
    <td  onmouseover="changeCursor(this, 'pointer')" title="배달 빠른 순으로 보여 줄게여 :)">
        <input type="radio" id="s_criteria_time" name="criteria" value="time" checked onclick="checkCriteria('s_criteria_time')">
        <!--<input type="checkbox" id="s_criteria_time" checked="true" value="time" onclick="checkCriteria('s_criteria_time')" >-->
    </td>

            <td  onmouseover="changeCursor(this, 'pointer')" title="주문 수량 많은 순으로 보여 줄게여 :)">
                <p><span class="criteria" style="color:#bf601b;"><i class="fas fa-file-invoice-dollar">&nbsp;</i>인기 순</span></p>
            </td>

            <td onmouseover="changeCursor(this, 'pointer')" title="주문 수량 많은 순으로 보여 줄게여 :)">
                <input type="radio" name="criteria" id="s_criteria_count" value="count" onclick="checkCriteria('s_criteria_count')">
                <!--<input type="checkbox" id="s_criteria_count" value="count" onclick="checkCriteria('s_criteria_count')">-->
            </td>
    </tr>

    </table>


    <div id="section">


    </div>
    <div id="my_modal" class="my_modal">
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

<<<<<<< HEAD
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

=======
>>>>>>> c7f49fc2ad5b8efcdac6d32aa41ba68359edac53

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
<<<<<<< HEAD


=======
>>>>>>> c7f49fc2ad5b8efcdac6d32aa41ba68359edac53
    <div id="footer">
        <h5><strong>swgil@realsn.com / R&D </strong></h5>
    </div>

</body>
</html>
