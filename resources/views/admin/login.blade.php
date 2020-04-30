<!DOCTYPE html>
<!--
Beyond Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3
Version: 1.0.0
Purchase: http://wrapbootstrap.com
-->

<html xmlns="http://www.w3.org/1999/xhtml">
<!--Head-->
<head>
    <meta charset="utf-8"/>
    <title>广告平台后台管理系统-系统登录</title>

    <meta name="description" content="登录后台"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="shortcut icon" href="{{asset("admin_style/assets/img/favicon.png")}}" type="image/x-icon">

    <!--Basic Styles-->
    <link href="{{asset("admin_style/assets/css/bootstrap.min.css")}}" rel="stylesheet"/>
    <link href="{{asset("admin_style/assets/css/font-awesome.min.css")}}" rel="stylesheet"/>

    <!--Beyond styles-->
    <link href="{{asset("admin_style/assets/css/beyond.min.css")}}" rel="stylesheet"/>
    <link href="{{asset("admin_style/assets/css/animate.min.css")}}" rel="stylesheet"/>

    <!--Skin Script: Place this script in head to load scripts for skins and rtl support-->
    <script src="{{asset("admin_style/assets/js/skins.min.js")}}"></script>
    <style type="text/css">
        body {
            background-image: url({{asset("admin_style/images/login_background")."/".rand(1,13).".jpg"}});
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        body::before {
            display: none;
        }

        .login-container .loginbox {
            height: 335px !important;
        }

        .login-container .loginbox .loginbox-title {
            height: auto;
            line-height: 3;
            margin-bottom: 20px;
        }


        .common_footer {
            color: #eee;
            position: absolute;
            bottom: 0px;
            text-align: center;
            width: 100%;
            background: #444;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px;
        }

        .toast-error {
            background-color: #da4f49;
        }
    </style>
</head>
<!--Head Ends-->
<!--Body-->
<body>
<div class="login-container animated fadeInDown">
    <form id="loginForm" method="post" action="{{route('admin.login_check')}}">
        {{csrf_field()}}
        <div class="loginbox bg-white">
            <div class="loginbox-title">登录后台</div>
            <div class="loginbox-textbox">
                <input type="text" name="account" class="form-control" placeholder="账号"/>
            </div>
            <div class="loginbox-textbox">
                <input type="password" name="password" class="form-control" placeholder="密码"/>
            </div>
            <div class="loginbox-submit">
                <input type="button" onclick="login()" class="btn btn-primary btn-block" value="登录">
            </div>
        </div>
    </form>
</div>

{{--版权部分--}}
<div class="common_footer">Copyright ©深圳海粉传媒广告有限公司 | <a href="http://www.beian.miit.gov.cn" target="_blank">
        粤ICP备18025010号</a>
</div>

<!--Basic Scripts-->
<script src="{{asset("admin_style/assets/js/jquery-2.0.3.min.js")}}"></script>
<script src="{{asset("admin_style/assets/js/bootstrap.min.js")}}"></script>

<!--Beyond Scripts-->
<script src="{{asset("admin_style/assets/js/beyond.js")}}"></script>
<script src="{{asset("admin_style/assets/js/toastr/toastr.js")}}"></script>

<!--Google Analytics::Demo Only-->
<script>
    // 登录函数
    function login() {
        var url = $("#loginForm").attr('action');
        var data = $("#loginForm").serialize();
        $.post(url, data, function (res) {
            if (res.code === 200) {
                Notify(res.message, 'top-right', '5000', 'blue', 'fa-check', true);
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            } else {
                Notify(res.message, 'top-right', '5000', 'danger', 'fa-check', true);
            }
        })
    }
</script>
</body>
<!--Body Ends-->
</html>
