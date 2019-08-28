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
        <input type="hidden" id="_token" value="{{csrf_token()}}">
        <input type="hidden" id="EditStatus" value="{{route('admin.system.account_status')}}">
        <input type="hidden" id="AdminInfo" value="{{route('admin.system.account_info')}}">
        <div class="buttons-preview">
            <a class="btn btn-success" href="javascript:void(0);" onclick="GetModal('add',0)"><i class="fa fa-plus"></i>添加管理人员</a>
        </div>
        {{--系统管理人员列表--}}
        <div class="row">
            <div class="col-lg-12">
                <div class="well with-header with-footer">
                    <div class="header bordered-sky">
                        系统管理员列表
                    </div>
                    <div class="table-scrollable">
                        <table class="table table-hover">
                            <thead class="bordered-darkorange">
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>
                                    头像
                                </th>
                                <th>
                                    账号
                                </th>
                                <th>
                                    角色
                                </th>
                                <th>
                                    状态（点击即可切换状态）
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
                            @foreach($account_list as $key=> $val)
                                <tr>
                                    <td>
                                        {{$val['id']}}
                                    </td>
                                    <td>
                                        <img src="{{$val['avatar']}}" alt="用户头像" class="img-thumbnail"
                                             style="width:50px;height:50px;">
                                    </td>
                                    <td>
                                        {{$val['account']}}
                                    </td>
                                    <td>
                                        {{$val['role_name']}}
                                    </td>
                                    <td>
                                        @if($val['status'] == 1)
                                            <button class="btn btn-success" onclick="EditStatus({{$val['id']}})">正常
                                            </button>
                                        @elseif($val['status'] == -1)
                                            <button class="btn btn-danger" onclick="EditStatus({{$val['id']}})">冻结
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        {{$val['created_at']}}
                                    </td>
                                    <td>
                                        <button class="btn btn-primary" onclick="GetModal('edit',{{$val['id']}})">编辑
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="footer">
                        <div class="pull-right">
                            {{ $account_list->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    {{--添加管理人员--}}
    <div id="AddModal" class="modal fade" aria-hidden="true" data-width="35%">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="title">添加管理员</h4>
                </div>
                <div class="modal-body">
                    <form id="AddFrom">
                        {{csrf_field()}}
                        <input type="hidden" id="AddAccount" value="{{route('admin.system.account_add')}}">
                        <div class="form-group">
                            <label>系统角色</label>
                            <select class="form-control" name="role_id">
                                <option value="0">请选择管理人员的角色</option>
                                @foreach($role_list as $key=>$val)
                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>设置账号</label>
                            <input type="text" name="account" class="form-control" placeholder="设置账号"/>
                        </div>
                        <div class="form-group">
                            <label>设置密码</label>
                            <input type="text" name="password" class="form-control" placeholder="设置密码"/>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
                    <button type="button" class="btn btn-blue" onclick="addData()">确认添加</button>
                </div>
            </div>
        </div>
    </div>
    <!--添加管理人员-->


    {{--编辑管理人员--}}
    <div id="EditModal" class="modal fade" aria-hidden="true" data-width="35%">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="title">编辑管理员</h4>
                </div>
                <div class="modal-body">
                    <form id="AddFrom">
                        {{csrf_field()}}
                        <input type="hidden" id="EditAccount" value="{{route('admin.system.account_edit')}}">
                        <input type="hidden" id="admin_id" name="admin_id">
                        <div class="form-group">
                            <label>系统角色</label>
                            <select class="form-control" name="role_id" id="role_id">
                                <option value="0">请选择管理人员的角色</option>
                                @foreach($role_list as $key=>$val)
                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>设置账号</label>
                            <input type="text" name="account" id="account" class="form-control" placeholder="设置账号"/>
                        </div>
                        <div class="form-group">
                            <label>设置密码</label>
                            <input type="text" name="password" class="form-control" placeholder="设置密码"/>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
                    <button type="button" class="btn btn-blue" onclick="editData()">确认编辑</button>
                </div>
            </div>
        </div>
    </div>
    <!--编辑管理人员-->


@endsection


{{--引入其他js脚本--}}
@section('script')
    <!--Page Related Scripts-->
    <script src="{{asset('admin/assets/js/bootbox/bootbox.js')}}"></script>
    <script>
        // 展示添加路由模态框
        function GetModal(name, id) {
            if (name === 'add') {
                $("#AddModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
            } else if (name === 'edit') {
                let url = $("#AdminInfo").val();
                let _token = $("#_token").val();
                let data = {id: id, '_token': _token};
                $.post(url, data, function (res) {
                    if (res.code === 200) {
                        $("#admin_id").val(res.data.id);
                        $("#role_id").find("option[value=" + res.data.role_id + "]").attr('selected', 'selected');
                        $("#role_id").find("option[value=" + res.data.role_id + "]").siblings().attr('selected', false);
                        $("#account").val(res.data.account);
                        // 禁止点击外部，模态框消失
                        $("#EditModal").modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                    } else {
                        Notify(res.message, 'top-right', '5000', 'danger', 'fa-check', true);
                    }
                });
            }
        }

        // 修改账号状态
        function EditStatus(id) {
            let url = $("#EditStatus").val();
            let _token = $("#_token").val();
            let data = {id: id, _token: _token};
            $.post(url, data, function (res) {
                if (res.code === 200) {
                    Notify(res.message, 'top-right', '5000', 'success', 'fa-check', true);
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                } else {
                    Notify(res.message, 'top-right', '5000', 'danger', 'fa-check', true);
                }
            })
        }

        // 创建系统管理员
        function addData() {
            let url = $("#AddAccount").val();
            let data = $("#AddFrom").serialize();
            $.post(url, data, function (res) {
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

        // 编辑管理员信息
        function editData() {
            let url = $("#RouteEdit").val();
            let data = $("#RouteEditFrom").serialize();
            $.post(url, data, function (res) {
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