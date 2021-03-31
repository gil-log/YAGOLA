<?php header('Content-Type: text/html; charset=UTF-8'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1.0, minimum-scale=1" />
    <title>야골라</title>

    <style type="text/css">
        #wrapper{
            width: 95%;
            margin: 0px auto;
            padding: 2%;
            border: 1px solid #828282;
            border-style: solid;
            overflow: auto;
        }

        #header{
            height: 10%;
            min-height: 40px;
            margin-bottom: 2%;
            padding: 2%;
            border: 1px solid #46b2fb;
            background-color: #46b2fb;
            overflow: auto;
        }

        #section{
            margin-bottom: 2%;
            padding: 2%;
            min-height: 60vh;
            width: 95%;
            height: 100%;
            border: 1px solid #828282;
            border-radius: 2%;
        }

        #footer{
            clear: both;
            border-style: groove;
            margin: 0px 5px;
            background-color: #fff;
            color: black;
            text-align: center;
            border: 1px solid #828282;
        }

        #header_left{
            float: left;
            box-sizing: border-box;
            min-height: 20vh;
            min-width: 20vh;
            width: auto;
            height: auto;
            background-image: url("images/logo_main.png");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }

        #header_center{
            float: left;
            margin-left: 3%;
            margin-right: 3%;
            width:60%;
            box-sizing: border-box;
            min-height: 20vh;
            min-width: 10vh;
            height: auto;
        }

        #header_right{
            float: right;
            box-sizing: border-box;
            min-height: 20vh;
            min-width: 20vh;
            width: auto;
            height: auto;
            background-image: url("images/dept_logo.png");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }

        #search_area{
            height:60%;
        }

        .search{
            height:100%;
            width:100%;
            background-color: #FFFF99;
            border: 1px solid #FFFF99;
            border-radius: 20px;
        }

        #category_area{
            hegiht:30%;
        }

        .category{
            float: left;
            width: 15%;
            padding: 5px;
        }

        .recommend_area{

        }

        .menu{

        }
    </style>

    <script type="text/javascript" src="lib/axisj/jquery/jquery.min.js"></script>

    <script>

        function directPage(pageName){
            var page = pageName + ".php";
            document.location.href = page;
        };

        function findInfo(){
            alert('find');
        }

        $(document).ready(function(){

        });

    </script>
</head>

<body>

​
<div id="wrapper">

    <div id="header">
        <div align="left" id="header_left" onclick="directPage('main')"></div>

        <div align="center" id="header_center">
            <div id="search_area">
                <div class="search">
                    <h6>검색</h6>
                    <button type="button" onclick="findInfo()"height="10px" width="10px"><img src="images/find_icon.png" ></button>
                </div>
            </div>
            <div id="category_area">
                <div class="category">
                    <img src="images/category_kor.png" style="width: 100%;">
                </div>
                <div class="category">
                    <img src="images/category_wes.png" style="width: 100%;">
                </div>
                <div class="category">
                    <img src="images/category_jap.png" style="width: 100%;">
                </div>
                <div class="category">
                    <img src="images/category_chi.png" style="width: 100%; ">
                </div>
            </div>
        </div>

        <div align="right" id="header_right" onclick="directPage('department')"></div>
    </div>

    <div id="section">

        <div class="recommend_area">

            <div class="category">
                <img src="images/category_chi.png" style="width: 100%; ">
            </div>

        </div>

        <div class="restaurant_area">


        </div>

    </div>

    <div id="footer">
        <h5> <strong>swgil@realsn.com / R&D  </strong></h5>
    </div>

</body>
</html>