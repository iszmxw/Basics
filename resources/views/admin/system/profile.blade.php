@extends('admin.app')

@section('title', $title)
{{--引入其他css样式--}}
@section('style')

@endsection

@section('Breadcrumb')
    <div class="page-breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{route('admin.dashboard')}}">首页</a>
            </li>
            <li class="active">{{$title}}</li>
        </ul>
    </div>
@endsection

@section('PageBody')
    <div class="page-body">
        <div class="row">
            {{--个人资料--}}
            <div class="col-lg-8">
                <div class="widget">
                    <div class="widget-header bordered-bottom bordered-palegreen">
                        <span class="widget-caption">个人资料</span>
                    </div>
                    <div class="widget-body">
                        <div>
                            <form class="form-horizontal form-bordered" id="postForm"
                                  action="{{route('admin.system.profile_edit')}}">
                                {{csrf_field()}}
                                <div class="form-group text-center">
                                    <img src="{{$admin_data['avatar']}}" alt="头像">
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right">账号</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{$admin_data['account']}}"
                                               disabled="disabled"
                                               placeholder="账号">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right">角色</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{$admin_data['role_name']}}"
                                               disabled="disabled"
                                               placeholder="账号">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right">原密码</label>
                                    <div class="col-sm-10">
                                        <input type="password" name="old_password" class="form-control"
                                               placeholder="原密码">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right">新密码</label>
                                    <div class="col-sm-10">
                                        <input type="password" name="new_password" class="form-control"
                                               placeholder="新密码">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right">确认密码</label>
                                    <div class="col-sm-10">
                                        <input type="password" name="re_password" class="form-control"
                                               placeholder="确认密码">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="button" class="btn btn-palegreen" onclick="save()">保存</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


{{--引入其他js脚本--}}
@section('script')
    <script>
        function save() {
            let url = $("#postForm").attr('action');
            let data = $("#postForm").serialize();
            $.post(url, data, function (res) {
                console.log(res);
                if (res.code === 200) {
                    Notify(res.message, 'top-right', '5000', 'success', 'fa-check', true);
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                } else {
                    Notify(res.message, 'top-right', '5000', 'danger', 'fa-check', true);
                }
            });
        }
    </script>
@endsection