<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <title>广告平台-{{$msg}}</title>
    <meta name="description" content="@yield('title')"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="shortcut icon" href="{{asset("admin_style/assets/img/favicon.png")}}" type="image/x-icon">
    <style>
        .swal-footer {
            text-align: center;
        }
    </style>
</head>
<!-- /头部 -->
<!-- Body部分 -->
<body>
<div class="loading-container">
    <div class="loading-progress">
        <div class="rotator">
            <div class="rotator">
                <div class="rotator colored">
                    <div class="rotator">
                        <div class="rotator colored">
                            <div class="rotator colored"></div>
                            <div class="rotator"></div>
                        </div>
                        <div class="rotator colored"></div>
                    </div>
                    <div class="rotator"></div>
                </div>
                <div class="rotator"></div>
            </div>
            <div class="rotator"></div>
        </div>
        <div class="rotator"></div>
    </div>
</div>
<script src="{{asset("admin_style/assets/library/sweetalert/sweetalert.min.js")}}"></script>
{{--error--}}
<script>
    swal({
        title: "提示信息",
        text: "{{$msg}}",
        icon: "warning",
        buttons: [false, "确认返回"],
        closeOnClickOutside: false,
    }).then((res) => {
        if (res) {
            window.location.href = "{{ url()->previous() }}"
        }
    });
</script>
</body>
</html>