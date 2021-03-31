<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />


    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1.0, minimum-scale=1" />
    <title>modal</title>

    <link rel="stylesheet" type="text/css" href="lib/axisj/ui/arongi/page.css">
    <link rel="stylesheet" type="text/css" href="lib/axisj/ui/arongi/AXJ.min.css">

    <script type="text/javascript" src="lib/axisj/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="lib/axisj/lib/AXJ.js"></script>
    <script type="text/javascript" src="lib/axisj/lib/AXModal.js"></script>
    <script type="text/javascript" src="fun_common.js"></script>


    <script type="text/javascript" src="lib/axisj/lib/AXCore.js"></script>



    <link href="https://fonts.googleapis.com/css2?family=Black+Han+Sans&display=swap" rel="stylesheet">

    <style type="text/css">
        .modalProgramTitle{
            text-align: center;
            height:150px;line-height:49px;
            color:#282828;font-size:14px;font-weight:bold;
            padding-left:15px;
            border-bottom:1px solid #3498DB;

            background-image: url("images/logo_header.png");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;

        }
        .modalButtonBox{
            padding:10px;border-top:1px solid #3498DB;
        }

        .masterModalBody{
            text-align: center;
            font-family: 'Black Han Sans', sans-serif;
        }

        .department_list{
            margin-top: 10%;
        }

        .department_name{
            font-size: 21px;
            color: white;
        }

        .department_system{
            font-size: 36px;
        }

        .group_name{
            font-size: 21px;
            color: white;
        }

        .group_name_selected{
            font-size: 21px;
            color:black;
        }

        .group_img{
            width:22%;
            height:22%;
        }

        .department_img{
            width:14%;
            height:14%;
        }
    </style>

    <script>
        var myModal = new AXModal();
        var modalObj = {
            pageStart: function(){
                myModal.setConfig({
                    windowID:"myModalCT",
                    mediaQuery: {
                        mx:{min:0, max:320}, dx:{min:320}
                    },
                    displayLoading:true,
                    defaultTop: 2
                });
            },
            open: function(){
                var pars = {};
                myModal.open({
                    url:"modal.php",
                    pars:pars,
                    top:10, width:400,
                    closeByEscKey:true,
                    verticalAlign:true,
                    closeButton:true
                });
            },
            close: function(){
                if(opener){
                    window.close();
                }
                else
                if(parent){
                    if(parent.myModal) parent.myModal.close();
                }
                else
                {
                    window.close();
                }
            }
        };



        function selectGroup(departmentFlag){

            var sendData = {d_flag : departmentFlag};

            callAjax("department_info.php", "POST", sendData, replaceModalContent);

        }

        function replaceModalContent(data){
            var parseData = JSON.parse(data);

            let d_flag = parseData['d_flag'];


            var parseResultData = parseData['result'];

            var axGridTarget = document.getElementById("AXGridTarget");

            var departmentArea = document.getElementById("department_area");

            var departmentListArea = document.getElementById("department_list_area");

            departmentArea.setAttribute('style','display:none');

            var groupName = '';
            var groupImgSrc = '';
            if (d_flag == 1){
                groupName = '연구 & 개발';
                groupImgSrc = 'images/dept_rnd.png';
            } else if (d_flag == 2){
                groupName = '엔터프라이즈';
                groupImgSrc = 'images/dept_enter.png';
            } else if (d_flag == 3){
                groupName = '서비스 컨설팅';
                groupImgSrc = 'images/dept_consult.png';
            }


/*
        */

            document.getElementById("selected_group_name").innerText = groupName;

            document.getElementById("selected_group_img").src = groupImgSrc;

            for(var i = 0; i < parseResultData.length; i++){
                var d_seq = parseResultData[i]['d_seq'];
                var d_name = parseResultData[i]['d_name'];

                var newDiv = document.createElement('div');

                newDiv.setAttribute('class', 'department_list');
                newDiv.setAttribute('onclick', 'selectDepartment('+d_seq+')');

                var newP = document.createElement('p');

                newP.setAttribute('class', 'department_name');
                newP.innerText = d_name;

                newDiv.appendChild(newP);

                departmentListArea.appendChild(newDiv);

            }



            departmentListArea.setAttribute('style', 'display:block');

        }

        function selectDepartment(d_seq){


            alert("selected D_seq : " + d_seq);

            var cookieName = "cookie_department";

            setCookie(cookieName, d_seq);


            //parent.myModal.close();

            //window.close();

            dialog.push('<b>최종 선택!</b>\n 이제 골라 보세요!');

            var modal = document.querySelector(".AXModalBox");

            //modal.parentNode.removeChild(modal);

            alert(modal);
            //modal.outerHTML = '';

            quitModal();

            var departmentListArea = document.getElementById("department_list_area");

            departmentListArea.setAttribute('style','display:none');


            var departmentDoneArea = document.getElementById("department_done_area");

            departmentDoneArea.setAttribute('style', 'display:block');

        }

    </script>


</head>

<body>

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

                    </div>

                    <p class="department_system">부서를 선택해주세요.</p>

                </div>

                <div id="department_done_area" style="display: none;">

                    <div class="department_element">

                        <img class="department_img" id="selected_group_img" src="images/dept_rnd.png">
                        <p class="group_name_selected" id="selected_group_name"></p>

                        <p class="department_system">최종 선택!</p>
                        <p class="department_system">이제 골라 보세요!</p>

                        <button type="button" class="AXButtonSmall Blue" onclick="self.close();">고르기</button>
                    </div>


                </div>

            </div>
        </div>
    </div>
</div>

</body>

</html>

</script>
