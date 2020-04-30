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
        <div class="buttons-preview">
            <a class="btn btn-success" href="javascript:void(0);" onclick="GetModal('add',0)"><i class="fa fa-plus"></i>创建角色</a>
        </div>
        {{--角色管理--}}
        <div class="row">
            <input type="hidden" id="_token" value="{{csrf_token()}}">
            <input type="hidden" id="RoleModalAdd" value="{{route('admin.system.role_modal_add')}}">
            <input type="hidden" id="RoleDelete" value="{{route('admin.system.role_delete')}}">
            <input type="hidden" id="RoleModalEdit" value="{{route('admin.system.role_modal_edit')}}">
            <div class="col-lg-12">
                <div class="well with-header with-footer">
                    <div class="header bordered-sky">
                        角色列表
                    </div>
                    <table class="table table-hover">
                        <thead class="bordered-darkorange">
                        <tr>
                            <th>
                                #
                            </th>
                            <th>
                                角色名称
                            </th>
                            <th>
                                描述
                            </th>
                            <th>
                                创建时间
                            </th>
                            <th>
                                操作
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($role_list as $key=> $val)
                            <tr>
                                <td>
                                    {{$val['id']}}
                                </td>
                                <td>
                                    {{$val['name']}}
                                </td>
                                <td>
                                    {{$val['desc']}}
                                </td>
                                <td>
                                    {{$val['created_at']}}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary"
                                            onclick="GetModal('edit',{{$val['id']}})">编辑
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="deleteRole({{$val['id']}})">
                                        删除
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="footer">
                        <div class="pull-right">
                            {{ $role_list->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--创建角色/编辑角色模态框--}}
    <div id="Modal" class="modal fade" aria-hidden="true"></div>
    <!--添加路由-->

@endsection


{{--引入其他js脚本--}}
@section('script')
    <!--Page Related Scripts-->
    <script src="{{asset('admin_style/assets/js/bootbox/bootbox.js')}}"></script>
    <script>
        // 获取模态框
        function GetModal(name, id) {
            let url;
            let _token = $("#_token").val();
            if (name === 'add') {
                url = $("#RoleModalAdd").val();
            }
            if (name === 'edit') {
                url = $("#RoleModalEdit").val();
            }
            let data = {_token: _token, id: id};
            $.post(url, data, function (data) {
                if (data.code === 500) {
                    Notify(data.message, 'top-right', '5000', 'danger', 'fa-check', true);
                } else {
                    $("#Modal").html(data);
                    $("#Modal").modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                }
            });
        }

        // 删除角色
        function deleteRole(id) {
            let url = $("#RoleDelete").val();
            let _token = $("#_token").val();
            let data = {_token: _token, id: id};
            bootbox.confirm({
                message: "确认要删除了吗?",
                buttons: {
                    confirm: {
                        label: '确定',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: '取消',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if (result) {
                        $.post(url, data, function (res) {
                            if (res.code === 200) {
                                Notify(res.message, 'top-right', '5000', 'success', 'fa-check', true);
                                setTimeout(function () {
                                    window.location.reload();
                                }, 1000)
                            } else {
                                Notify(res.message, 'top-right', '5000', 'danger', 'fa-check', true);
                            }
                        });
                    }
                }
            });
        }
    </script>
@endsection