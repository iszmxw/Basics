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
        <input type="hidden" id="EditStatus" value="{{route('admin.advert.advert_status')}}">
        <div class="buttons-preview">
            <a class="btn btn-success" href="{{route('admin.advert.advert_add')}}"><i class="fa fa-plus"></i>添加广告</a>
        </div>
        {{--系统广告列表--}}
        <div class="row">
            <div class="col-lg-12">
                <div class="well with-header with-footer">
                    <div class="header bordered-sky">
                        系统广告列表
                    </div>
                    <div class="table-scrollable">
                        <table class="table table-hover">
                            <thead class="bordered-darkorange">
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>
                                    投放用户
                                </th>
                                <th>
                                    广告名称
                                </th>
                                <th>
                                    描述
                                </th>
                                <th>
                                    广告类型
                                </th>
                                <th>
                                    宽*高
                                </th>
                                <th>
                                    查看广告资源
                                </th>
                                <th>
                                    广告单价
                                </th>
                                <th>
                                    展示时间
                                </th>
                                {{--                                <th>--}}
                                {{--                                    投放城市--}}
                                {{--                                </th>--}}
                                <th>
                                    创建时间
                                </th>
                                <th>
                                    状态
                                </th>
                                <th>
                                    操作
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($advert_list as $key=> $val)
                                <tr>
                                    <td>
                                        {{$val['id']}}
                                    </td>
                                    <td>
                                        {{$val['user']}}
                                    </td>
                                    <td>
                                        {{$val['name']}}
                                    </td>
                                    <td>
                                        {{$val['desc']}}
                                    </td>
                                    <td>
                                        @if($val['type'] === 'IMAGE')
                                            <label class="label label-success">图片</label>
                                        @elseif($val['type'] === 'VIDEO')
                                            <label class="label label-primary">视频</label>
                                        @else
                                            <label class="label label-active">未知</label>
                                        @endif

                                    </td>
                                    <td>
                                        {{$val['width']}}*{{$val['height']}}
                                    </td>
                                    <td>
                                        <button class="btn btn-success"
                                                onclick="LookDetail('{{$val['type']}}','{{$val['complete_url']}}')">查看
                                        </button>
                                    </td>
                                    <td>
                                        {{$val['price'] / 100}} 元
                                    </td>
                                    <td>
                                        {{$val['show_time']}} 秒
                                    </td>
                                    {{--                                    <td>--}}
                                    {{--                                        {{$val['city']}}--}}
                                    {{--                                    </td>--}}
                                    <td>
                                        {{$val['created_at']}}
                                    </td>
                                    <td>
                                        @if($val['status'] == 0)
                                            <label class="label label-warning">待审核</label>
                                        @elseif($val['status'] == 1)
                                            <label class="label label-success">已上架</label>
                                        @elseif($val['status'] == 2)
                                            <label class="label label-danger">已下架</label>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-primary"
                                           href="{{route('admin.advert.advert_edit').'?advert_id='.$val['id']}}">编辑
                                        </a>
                                        <select name="advert_status" id="advert_status"
                                                onchange="EditStatus(this,{{$val['id']}})">
                                            <option value="0">修改状态</option>
                                            <option value="0">待审核</option>
                                            <option value="1">已上架</option>
                                            <option value="2">已下架</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="footer">
                        <div class="pull-right">
                            {{ $advert_list->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--展示广告详情--}}
    <div id="DetailModal" class="modal fade" aria-hidden="true" data-width="35%">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="title">广告详情</h4>
                </div>
                <div class="modal-body">
                    <div class="row" id="modal-body">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
                </div>
            </div>
        </div>
    </div>
    {{--展示广告详情--}}
@endsection


{{--引入其他js脚本--}}
@section('script')
    <script>
        // 查看广告详情
        function LookDetail(type, url) {
            let html;
            if (type === 'IMAGE') {
                html = '<img src="' + url + '" alt="图片详情" class="img-responsive">';
            } else if (type === 'VIDEO') {
                html = '<video src="' + url + '" controls="controls" width="100%" height="100%"></video>';
            }
            // 禁止点击外部，模态框消失
            $("#DetailModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#modal-body").html(html);
        }

        // 修改广告状态
        function EditStatus(obj, id) {
            let url = $("#EditStatus").val();
            let _token = $("#_token").val();
            let status = $(obj).val();
            let data = {
                id: id,
                status: status,
                _token: _token
            };
            $.post(url, data, function (res) {
                if (res.code === 200) {
                    Notify(res.message, 'top-right', '5000', 'success', 'fa-check', true);
                    window.location.reload();
                } else {
                    Notify(res.message, 'top-right', '5000', 'danger', 'fa-check', true);
                }
            })
        }

    </script>
@endsection