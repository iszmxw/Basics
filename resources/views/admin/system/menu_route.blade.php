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

            <div class="col-lg-6">
                <div class="well with-header">
                    <div class="header bordered-darkorange">左侧菜单列表</div>
                    <div class="dd dd-draghandle bordered">
                        <ol class="dd-list">
                            @foreach($menu_data as $key=>$val)
                                <li class="dd-item dd2-item" data-id="{{$val['id']}}">
                                    <div class="dd-handle dd2-handle">
                                        <i class="{{$val['icon']}}"></i>

                                        <i class="drag-icon fa fa-arrows-alt "></i>
                                    </div>
                                    <div class="dd2-content">
                                        {{$val['name']}}
                                        <span class="pull-right">
                                            <span class="pull-left">
                                                <input type="number" style="width:50px"
                                                       onblur="route_order(this,{{$val['id']}})"
                                                       value="{{$val['order']}}">
                                            </span>
                                            <span class="pull-left">&nbsp;&nbsp;</span>
                                            <span class="pull-left">
                                                <a href="javascript:void(0);"
                                                   class="btn btn-danger btn-xs"
                                                   onclick="deleted('{{$val['id']}}')">删除</a>
                                            </span>
                                        </span>
                                    </div>
                                    <ol class="dd-list" style="">
                                        @foreach($val['menu_sub'] as $k=>$v)
                                            @if(isset($v['name']))
                                                <li class="dd-item dd2-item" data-id="{{$v['id']}}">
                                                    <div class="dd2-content">
                                                        {{$v['name']}}
                                                        <span class="pull-right">
                                                            <span class="pull-left">
                                                                <input type="number" style="width:50px"
                                                                       onblur="route_order(this,{{$v['id']}})"
                                                                       value="{{$v['order']}}">
                                                            </span>
                                                            <span class="pull-left">&nbsp;&nbsp;</span>
                                                            <span class="pull-left">
                                                                <a href="javascript:void(0);"
                                                                   class="btn btn-danger btn-xs"
                                                                   onclick="deleted('{{$v['id']}}')">删除</a>
                                                            </span>
                                                        </span>
                                                    </div>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ol>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>


            <div class="col-lg-6">
                <div class="well with-header">
                    <a class="btn btn-info" href="javascript:void(0);" onclick="showAddRouteModal()">
                        <i class="fa fa-plus"></i>添加路由</a>
                    <div class="header bordered-darkorange">
                        系统权限路由
                        <span style="color: #F00;">（路由包含左侧所有菜单，修改菜单路由，会印影响到左侧菜单哦）</span>
                    </div>
                    <div class="dd dd-draghandle bordered">
                        <ol class="dd-list">
                            @foreach($route_data as $key=>$val)
                                <li class="dd-item dd2-item" data-id="{{$val['id']}}">
                                    <div class="dd-handle dd2-handle">
                                        <i class="{{$val['icon']}}"></i>

                                        <i class="drag-icon fa fa-arrows-alt "></i>
                                    </div>
                                    <div class="dd2-content">
                                        <span class="pull-left">
                                            {{$val['name']}}
                                            @if($val['is_menu'] == 1)
                                                <i class="glyphicon glyphicon-align-left"></i>
                                            @else
                                                <i class="glyphicon glyphicon-link"></i>
                                            @endif
                                        </span>

                                        <span class="pull-right">
                                            <span class="pull-left">
                                                <input type="number" style="width:50px"
                                                       onblur="route_order(this,{{$val['id']}})"
                                                       value="{{$val['order']}}">
                                            </span>
                                            <span class="pull-left">&nbsp;&nbsp;</span>
                                            <span class="pull-left">
                                                <a href="javascript:void(0);"
                                                   class="btn btn-palegreen btn-xs"
                                                   onclick="showEditRouteModal('{{$val['id']}}')">编辑</a>
                                            </span>
                                            <span class="pull-left">&nbsp;&nbsp;</span>
                                            <span class="pull-left">
                                                <a href="javascript:void(0);"
                                                   class="btn btn-danger btn-xs"
                                                   onclick="deleted('{{$val['id']}}')">删除</a>
                                            </span>
                                        </span>
                                    </div>
                                    <ol class="dd-list" style="">
                                        @foreach($val['menu_sub'] as $kk=>$vv)
                                            @if(isset($vv['name']))
                                                <li class="dd-item dd2-item" data-id="{{$vv['id']}}">
                                                    <div class="dd2-content">
                                                        <span class="pull-left">
                                                            {{$vv['name']}}
                                                            @if($vv['is_menu'] == 1)
                                                                <i class="glyphicon glyphicon-align-left"></i>
                                                            @else
                                                                <i class="glyphicon glyphicon-link"></i>
                                                            @endif
                                                        </span>
                                                        <span class="pull-right">
                                                            <span class="pull-left">
                                                                <input type="number" style="width:50px"
                                                                       onblur="route_order(this,{{$vv['id']}})"
                                                                       value="{{$vv['order']}}">
                                                            </span>
                                                            <span class="pull-left">&nbsp;&nbsp;</span>
                                                            <span class="pull-left">
                                                                <a href="javascript:void(0);"
                                                                   class="btn btn-palegreen btn-xs"
                                                                   onclick="showEditRouteModal('{{$vv['id']}}')">编辑</a>
                                                            </span>
                                                            <span class="pull-left">&nbsp;&nbsp;</span>
                                                            <span class="pull-left">
                                                                <a href="javascript:void(0);"
                                                                   class="btn btn-danger btn-xs"
                                                                   onclick="deleted('{{$vv['id']}}')">删除</a>
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <ol class="dd-list" style="">


                                                        @foreach($vv['menu_sub'] as $k=>$v)
                                                            @if(isset($v['name']))
                                                                <li class="dd-item dd2-item" data-id="{{$v['id']}}">
                                                                    <div class="dd2-content">
                                                        <span class="pull-left">
                                                            {{$v['name']}}
                                                            @if($v['is_menu'] == 1)
                                                                <i class="glyphicon glyphicon-align-left"></i>
                                                            @else
                                                                <i class="glyphicon glyphicon-link"></i>
                                                            @endif
                                                        </span>
                                                                        <span class="pull-right">
                                                            <span class="pull-left">
                                                                <input type="number" style="width:50px"
                                                                       onblur="route_order(this,{{$v['id']}})"
                                                                       value="{{$v['order']}}">
                                                            </span>
                                                            <span class="pull-left">&nbsp;&nbsp;</span>
                                                            <span class="pull-left">
                                                                <a href="javascript:void(0);"
                                                                   class="btn btn-palegreen btn-xs"
                                                                   onclick="showEditRouteModal('{{$v['id']}}')">编辑</a>
                                                            </span>
                                                            <span class="pull-left">&nbsp;&nbsp;</span>
                                                            <span class="pull-left">
                                                                <a href="javascript:void(0);"
                                                                   class="btn btn-danger btn-xs"
                                                                   onclick="deleted('{{$v['id']}}')">删除</a>
                                                            </span>
                                                        </span>
                                                                    </div>
                                                                </li>
                                                            @endif
                                                        @endforeach


                                                    </ol>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ol>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>




    {{--添加路由--}}
    <div id="AddRouteModal" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="title">添加权限路由</h4>
                </div>
                <div class="modal-body">
                    <form id="RouteAddFrom">
                        {{csrf_field()}}
                        <input type="hidden" id="RouteAdd" value="{{route('admin.system.route_add')}}">
                        <div class="form-group">
                            <label>类型</label>
                            <div class="control-group">
                                <div class="radio">
                                    <label>
                                        <input name="is_menu" value="0" type="radio" checked="checked" class="colored-success">
                                        <span class="text">系统路由</span>
                                    </label>
                                    <label>
                                        <input name="is_menu" value="1" type="radio" class="colored-success">
                                        <span class="text">左侧菜单</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>上级路由菜单</label>
                            <select class="form-control" name="parent_id">
                                <option value="0">选择上级路由菜单</option>
                                @foreach($parent_data as $key=>$val)
                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>路由名称</label>
                            <input type="text" name="name" class="form-control" placeholder="路由名称"/>
                        </div>
                        <div class="form-group">
                            <label>路由地址</label>
                            <input type="text" name="route" class="form-control" placeholder="路由地址"/>
                        </div>
                        <div class="form-group">
                            <label>ICON</label>
                            <input type="text" name="icon" class="form-control" placeholder="ICON"/>
                        </div>
                        <div class="form-group">
                            <label>排序</label>
                            <input type="text" name="order" value="0" class="form-control" placeholder="排序"/>
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
    <!--添加路由-->

    {{--编辑路由--}}
    <div id="EditRouteModal" class="modal fade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="title">编辑路由信息</h4>
                </div>
                <div class="modal-body">
                    <form id="RouteEditFrom">
                        <input type="hidden" id="_token" name="_token" value="{{csrf_token()}}">
                        <input type="hidden" id="RouteInfo" value="{{route('admin.system.route_info')}}">
                        <input type="hidden" id="RouteEdit" value="{{route('admin.system.route_edit')}}">
                        <input type="hidden" id="RouteDelete" value="{{route('admin.system.route_delete')}}">
                        <input type="hidden" id="RouteOrder" value="{{route('admin.system.route_order')}}">
                        <input type="hidden" id="route_id" name="route_id" value="">
                        <div class="form-group">
                            <label>上级路由菜单</label>
                            <select class="form-control" id="parent_id" name="parent_id">
                                <option value="0">选择上级路由菜单</option>
                                @foreach($parent_data as $key=>$val)
                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>路由名称</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="路由名称"/>
                        </div>
                        <div class="form-group">
                            <label>路由地址</label>
                            <input type="text" id="route" name="route" class="form-control" placeholder="路由地址"/>
                        </div>
                        <div class="form-group">
                            <label>ICON</label>
                            <input type="text" id="icon" name="icon" class="form-control" placeholder="ICON"/>
                        </div>
                        <div class="form-group">
                            <label>排序</label>
                            <input type="text" id="order" name="order" value="0" class="form-control" placeholder="排序"/>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
                    <button type="button" class="btn btn-blue" onclick="editData()">编辑保存</button>
                </div>
            </div>
        </div>
    </div>
    <!--编辑路由-->

@endsection


{{--引入其他js脚本--}}
@section('script')
    <!--Page Related Scripts-->
    <script src="{{asset('admin/assets/js/bootbox/bootbox.js')}}"></script>
    <script>
        // 展示添加路由模态框
        function showAddRouteModal() {
            $("#AddRouteModal").modal({
                backdrop: 'static',
                keyboard: false
            });
        }

        // 修改路由排序
        function route_order(e, id) {
            let order = $(e).val();
            let url = $("#RouteOrder").val();
            let _token = $("#_token").val();
            let data = {id: id, order: order, _token: _token};
            $.post(url, data, function (res) {
                if (res.code === 200) {
                    Notify(res.message, 'top-right', '5000', 'success', 'fa-check', true);
                } else {
                    Notify(res.message, 'top-right', '5000', 'danger', 'fa-check', true);
                }
            })
        }

        // 展示编辑路由模态框
        function showEditRouteModal(id) {
            let url = $("#RouteInfo").val();
            let _token = $("#_token").val();
            let data = {id: id, '_token': _token};
            $.post(url, data, function (res) {
                if (res.code === 200) {
                    $("#route_id").val(res.data.id);
                    $("#parent_id").find("option[value=" + res.data.parent_id + "]").attr('selected', 'selected');
                    $("#parent_id").find("option[value=" + res.data.parent_id + "]").siblings().attr('selected', false);
                    $("#name").val(res.data.name);
                    $("#route").val(res.data.route);
                    $("#icon").val(res.data.icon);
                    $("#order").val(res.data.order);
                    // 禁止点击外部，模态框消失
                    $("#EditRouteModal").modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                } else {
                    Notify(res.message, 'top-right', '5000', 'danger', 'fa-check', true);
                }
            });
        }

        // 添加新的路由信息
        function addData() {
            let url = $("#RouteAdd").val();
            let data = $("#RouteAddFrom").serialize();
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

        // 删除路由
        function deleted(id) {
            let url = $("#RouteDelete").val();
            let _token = $("#_token").val();
            let data = {id: id, '_token': _token};
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
                                }, 500);
                            } else {
                                Notify(res.message, 'top-right', '5000', 'danger', 'fa-check', true);
                            }
                        });
                    }
                }
            });
        }

        // 添加新的路由信息
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