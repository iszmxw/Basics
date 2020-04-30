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
        <input type="hidden" id="EditStatus" value="{{route('admin.merchant.device_status')}}">
        <input type="hidden" id="DeviceInfo" value="{{route('admin.merchant.device_info')}}">
        <div class="buttons-preview">
            <a class="btn btn-success" href="javascript:void(0);" onclick="GetModal('add',0)"><i class="fa fa-plus"></i>导入新的设备</a>
        </div>
        {{--合作商户列表--}}
        <div class="row">
            <div class="col-lg-12">
                <div class="well with-header with-footer">
                    <div class="header bordered-sky">
                        合作商户设备列表
                    </div>

                    <form action="">
                        <div class="row">
                            <div class="col-lg-12">
                                <div>
                                    <label style="margin-bottom: 0px;">
                                        <input name="device_uuid" type="text" size="30" class="form-control input-sm"
                                               placeholder="设备UUID" value="{{ $search_data['device_uuid'] }}">
                                    </label>
                                    <button type="submit" class="btn btn-success btn-search">
                                        <i class="fa fa-search"></i>搜索
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <br>
                    <div class="table-scrollable">
                        <table class="table table-hover">
                            <thead class="bordered-darkorange">
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>
                                    所属商户名称
                                </th>
                                <th>
                                    设备UUID
                                </th>
                                <th>
                                    场景
                                </th>
                                <th>
                                    省份
                                </th>
                                <th>
                                    城市
                                </th>
                                <th>
                                    区域
                                </th>
                                <th>
                                    lng
                                </th>
                                <th>
                                    lat
                                </th>
                                <th>
                                    地址
                                </th>
                                <th>
                                    状态
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
                            @foreach($device_list as $key=> $val)
                                <tr>
                                    <td>
                                        {{$val['id']}}
                                    </td>
                                    <td>
                                        {{$val['company']}}
                                    </td>
                                    <td>
                                        {{$val['device_uuid']}}
                                    </td>
                                    <td>
                                        {{$val['scene_name']}}
                                    </td>
                                    <td>
                                        {{$val['province']}}
                                    </td>
                                    <td>
                                        {{$val['city']}}
                                    </td>
                                    <td>
                                        {{$val['area']}}
                                    </td>
                                    <td>
                                        {{$val['lng']}}
                                    </td>
                                    <td>
                                        {{$val['lat']}}
                                    </td>
                                    <td>
                                        {{$val['address']}}
                                    </td>
                                    <td>
                                        @if($val['status'] == 1)
                                            <span class="label label-success">正常</span>
                                        @elseif($val['status'] == -1)
                                            <span class="label label-danger">已冻结</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{$val['created_at']}}
                                    </td>
                                    <td>
                                        <button class="btn btn-primary" onclick="GetModal('edit',{{$val['id']}})">编辑
                                        </button>
                                        @if($val['status'] == 1)
                                            <button class="btn btn-danger" onclick="EditStatus({{$val['id']}})">冻结
                                            </button>
                                        @elseif($val['status'] == -1)
                                            <button class="btn btn-success" onclick="EditStatus({{$val['id']}})">解冻
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="footer">
                        <div class="pull-right">
                            {{ $device_list->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{--编辑合作商户的设备--}}
    <div id="EditModal" class="modal fade" aria-hidden="true" data-width="35%">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="title">编辑设备信息</h4>
                </div>
                <div class="modal-body">
                    <form id="EditFrom">
                        {{csrf_field()}}
                        <input type="hidden" id="EditMerchant" value="{{route('admin.merchant.device_edit')}}">
                        <input type="hidden" id="device_id" name="device_id">
                        <div class="form-group">
                            <label>归属商户</label>
                            <select name="merchant_id" id="merchant_id" class="form-control">
                                <option value="0">请选择归属商户</option>
                                @foreach($merchant_list as $key=>$val)
                                    <option value="{{$val['id']}}">{{$val['company']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>设备UUID</label>
                            <input type="text" id="device_uuid" name="device_uuid" class="form-control"
                                   placeholder="设备UUID"/>
                        </div>
                        <div class="form-group">
                            <label>详细地址</label>
                            <input type="text" id="address" name="address" class="form-control" placeholder="详细地址"/>
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
                let url = $("#DeviceInfo").val();
                let _token = $("#_token").val();
                let data = {id: id, '_token': _token};
                $.post(url, data, function (res) {
                    if (res.code === 200) {
                        $("#device_id").val(res.data.id);
                        $("#device_uuid").val(res.data.device_uuid);
                        $("#merchant_id").find("option[value=" + res.data.merchant_id + "]").attr('selected', 'selected');
                        $("#merchant_id").find("option[value=" + res.data.merchant_id + "]").siblings().attr('selected', false);
                        $("#address").val(res.data.address);
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