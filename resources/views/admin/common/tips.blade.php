<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <title>广告平台-{{$msg}}</title>
    <meta name="description" content="@yield('title')"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="shortcut icon" href="{{asset("admin/assets/img/favicon.png")}}" type="image/x-icon">
    @include('admin.common.style')
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
<div class="main-container container-fluid">
    <div class="page-container">
        <div class="page-content">
            {{--提示框--}}
            <div class="modal fade in" aria-hidden="false" style="display: block;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="title">{{$msg}}</h4>
                        </div>
                        <div class="modal-body">
                            <div class="title">
                                {{$msg}}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-blue"
                                    onclick="goBack()">确认返回
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{--提示框--}}
        </div>
    </div>
</div>
@include('admin.common.script')
<script>
    // 返回上一级页面
    function goBack() {
        window.location.href = '{{ url()->previous() }}';
    }
</script>
</body>
</html>