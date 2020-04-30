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
        <input type="hidden" id="EditStatus" value="{{route('admin.merchant.merchant_status')}}">
        <input type="hidden" id="MerchantInfo" value="{{route('admin.merchant.merchant_info')}}">
        <div class="buttons-preview">
            <a class="btn btn-success" href="javascript:void(0);" onclick="GetModal('add',0)"><i class="fa fa-plus"></i>添加合作商户</a>
        </div>
        {{--合作商户列表--}}
        <div class="row">
            <div class="col-lg-12">
                <div class="well with-header with-footer">
                    <div class="header bordered-sky">
                        合作商户列表
                    </div>
                    <div class="table-scrollable">
                        <table class="table table-hover">
                            <thead class="bordered-darkorange">
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>
                                    名称
                                </th>
                                <th>
                                    账号
                                </th>
                                <th>
                                    Appid
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
                            @foreach($merchant_list as $key=> $val)
                                <tr>
                                    <td>
                                        {{$val['id']}}
                                    </td>
                                    <td>
                                        {{$val['company']}}
                                    </td>
                                    <td>
                                        {{$val['account']}}
                                    </td>
                                    <td>
                                        {{$val['appid']}}
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
                            {{ $merchant_list->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    {{--添加合作商户--}}
    <div id="AddModal" class="modal fade" aria-hidden="true" data-width="35%">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="title">添加合作商户</h4>
                </div>
                <div class="modal-body">
                    <form id="AddFrom">
                        {{csrf_field()}}
                        <input type="hidden" id="AddMerchant" value="{{route('admin.merchant.merchant_add')}}">
                        <div class="form-group">
                            <label>设置商户名称</label>
                            <input type="text" name="company" class="form-control" placeholder="设置商户名称"/>
                        </div>
                        <div class="form-group">
                            <label>设置商户账号</label>
                            <input type="text" name="account" class="form-control" placeholder="设置商户账号"/>
                        </div>
                        <div class="form-group">
                            <label>设置商户密码</label>
                            <input type="password" name="password" class="form-control" placeholder="设置商户密码"/>
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
    <!--添加合作商户-->


    {{--编辑合作商户--}}
    <div id="EditModal" class="modal fade" aria-hidden="true" data-width="35%">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="title">编辑合作商户</h4>
                </div>
                <div class="modal-body">
                    <form id="EditFrom">
                        {{csrf_field()}}
                        <input type="hidden" id="EditMerchant" value="{{route('admin.merchant.merchant_edit')}}">
                        <input type="hidden" id="merchant_id" name="merchant_id">
                        <div class="form-group">
                            <label>合作商户账号</label>
                            <input type="text" id="account" class="form-control" placeholder="合作商户账号"
                                   disabled="disabled"/>
                        </div>
                        <div class="form-group">
                            <label>重新设置商户密码</label>
                            <input type="text" name="password" class="form-control" placeholder="重新设置商户密码"/>
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
    <!--编辑合作商户-->


@endsection


{{--引入其他js脚本--}}
@section('script')
    <!--Page Related Scripts-->
    <script src="{{asset('admin_style/assets/js/bootbox/bootbox.js')}}"></script>
    <script>
        // 展示添加路由模态框
        function GetModal(name, id) {
            if (name === 'add') {
                $("#AddModal").modal({
                    backdrop: 'static',
                    keyboard: false
                });
            } else if (name === 'edit') {
                let url = $("#MerchantInfo").val();
                let _token = $("#_token").val();
                let data = {id: id, '_token': _token};
                $.post(url, data, function (res) {
                    if (res.code === 200) {
                        $("#merchant_id").val(res.data.id);
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
            let url = $("#AddMerchant").val();
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
            let url = $("#EditMerchant").val();
            let data = $("#EditFrom").serialize();
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