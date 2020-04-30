@extends('admin.app')

@section('title', $title)
{{--引入其他css样式--}}
@section('style')
    <link rel="stylesheet" href="{{asset('admin_style/assets/library/webuploader/webuploader.css')}}">
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
        {{--编辑广告表单--}}
        <div class="row">
            <div class="col-lg-12">
                <div class="widget">
                    <div class="widget-header bordered-bottom bordered-blue">
                        <span class="widget-caption">编辑广告信息</span>
                    </div>
                    <div class="widget-body">

                        <form role="form" id="EditData" action="{{route('admin.advert.advert_edit_data')}}">
                            <input type="hidden" id="_token" name="_token" value="{{csrf_token()}}">
                            <input type="hidden" name="advert_id" value="{{ $advert['id'] }}">
                            <div class="form-title">
                                <a href="{{route('admin.advert.advert_list')}}" class="btn btn-info">
                                    <i class="fa fa-mail-reply"></i>返回广告列表
                                </a>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>所属用户</label>
                                    <select name="user_id" class="form-control">
                                        <option value="0">请选择广告归属用户</option>
                                        <option value="1" @if($advert['user_id'] === 1) selected @endif>十万粉后台</option>
                                        <option value="2" @if($advert['user_id'] === 2) selected @endif>追梦小窝测试</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>广告名称</label>
                                    <input type="text" name="name" value="{{$advert['name']}}" class="form-control"
                                           placeholder="广告名称">
                                </div>
                                <div class="form-group">
                                    <label>广告描述</label>
                                    <textarea name="desc" placeholder="广告描述" class="form-control" cols="30"
                                              rows="10">{{$advert['desc']}}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>广告类型</label>
                                    <select name="type" class="form-control">
                                        <option value="0">请选择广告类型</option>
                                        <option value="IMAGE" @if($advert['type'] === 'IMAGE') selected @endif>图片
                                        </option>
                                        <option value="VIDEO" @if($advert['type'] === 'VIDEO') selected @endif>视频
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>选择上传</label>
                                    <input type="hidden" name="url" value="{{$advert['url']}}" id="ad_file">
                                    <div id="uploader" class="wu-example">
                                        <!--用来存放文件信息-->
                                        <div id="thelist" class="uploader-list">

                                            {{--编辑图片--}}
                                            <div id="WU_FILE_0" data-id="" class="item" style="padding: 10px 5px;">
                                                <span class="info">{{$advert['complete_url']}}</span>&nbsp;
                                                <span class="state">已上传</span>&nbsp;
                                                <span class="remove-this">删除</span>
                                                <div class="progress progress-striped active">
                                                    <div class="progress-bar" role="progressbar"
                                                         style="width: 100%;"></div>
                                                </div>
                                            </div>
                                            {{--编辑图片--}}
                                        </div>
                                        <div class="btns">
                                            <button type="button" id="picker" class="btn btn-info">
                                                <i class="fa fa-cloud-upload"></i>选择文件
                                            </button>
                                            <button type="button" id="ctlBtn" class="btn btn-default">开始上传</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>宽</label>
                                    <input type="number" name="width" value="{{ $advert['width'] }}" id="width"
                                           placeholder="宽" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>高</label>
                                    <input type="number" name="height" value="{{ $advert['height'] }}" id="height"
                                           placeholder="高" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>播放场景（不选择的话，默认为所有场景下可以播放）</label>
                                    <input type="hidden" id="scene" value="{{$advert['scene']}}">
                                    <select class="input-group input-group-sm" id="e2" name="scene[]"
                                            multiple="multiple"
                                            style="width:100%;">
                                        @foreach ($scene as $key=>$val)
                                            <option value="{{$val['id']}}">{{$val['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>广告单价</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="price" value="{{ $advert['price']/100 }}"
                                               placeholder="广告单价" class="form-control">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button">元</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="status" value="1"
                                                   @if($advert['status'] === 1) checked @endif>
                                            <span class="text">直接上架</span>
                                        </label>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-blue" onclick="postForm()">保存数据</button>
                            </div>
                            <div style="clear:both"></div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>


    {{--广告素材库--}}
    <div id="ResourcesModal" class="modal fade" aria-hidden="true" data-width="35%">
    </div>
    {{--广告素材库--}}

@endsection


{{--引入其他js脚本--}}
@section('script')
    <!--Jquery Select2-->
    <script src="{{asset("admin_style/assets/js/select2/select2.js")}}"></script>
    <script src="{{asset("admin_style/assets/library/webuploader/webuploader.js")}}"></script>
    <script>

        $("#e2").select2({
            placeholder: "请选择场景",
            allowClear: true
        });
        $(function () {
            let scene = $("#scene").val();
            if (scene !== 0) {
                scene = scene.split(',');
                $("#e2").val(scene).trigger('change');
            }
            let deletd_url = "{{route('admin.advert.delete_file')}}";
            let $list = $("#thelist"); //这几个初始化全局的
            let $btn = $("#ctlBtn"); //开始上传。
            let uploader = WebUploader.create({
                // 文件接收服务端。
                server: '{{route('admin.advert.uploads')}}',
                // 选择文件的按钮。可选。
                // 内部根据当前运行是创建，可能是input元素，也可能是flash.
                pick: '#picker',
                // 一次只能上传一个文件
                fileNumLimit: 1,
                // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
                resize: false,
                accept: {
                    title: '目前支持以上格式jpg，jpeg，png，mp4',
                    extensions: 'jpg,jpeg,png,mp4',
                    mimeTypes: 'image/*,video/*'
                }
            });


            // 上传
            $btn.on('click', function () {
                uploader.upload();
            });


            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function (file, percentage) {
                let $li = $('#' + file.id),
                    $percent = $li.find('.progress .progress-bar');
                // 避免重复创建
                if (!$percent.length) {
                    $percent = $('<div class="progress progress-striped active">' +
                        '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                        '</div>' +
                        '</div>').appendTo($li).find('.progress-bar');
                }
                $li.find('span.state').text('上传中');
                $percent.css('width', percentage * 100 + '%');
            });

            // 当有文件被添加进队列的时候
            uploader.on('fileQueued', function (file) {
                if (file.id === "WU_FILE_0") {
                    $("#WU_FILE_0").remove();
                    $list.append('<div id="' + file.id + '" data-id="" class="item" style="padding: 10px 5px;">' +
                        '<span class="info">' + file.name + '</span>&nbsp;' +
                        '<span class="state">等待上传...</span>&nbsp;' +
                        '<span class="remove-this">删除</span>' +
                        '</div>');
                }
            });


            // 移除文件操作
            $list.on('click', '.remove-this', function () {
                let $ele = $(this);
                let id = $ele.parent().attr("id");
                let file = uploader.getFile(id);
                if (file === undefined) {
                    $("#" + id).remove();
                } else {
                    uploader.removeFile(file, true);
                }
            });

            //有文件从队列删除时执行的方法
            uploader.on('fileDequeued', function (file) {
                $(file.id).remove();
                $('#' + file.id).hide();
                let ad_file = $("#ad_file").val();
                if (ad_file) {
                    $.post(deletd_url, {ad_file: ad_file}, function (res) {
                        if (res === 'success') {
                            // 删除成功清除页面显示的数据
                            $("#ad_file").val('');
                            $("#width").val('');
                            $("#height").val('');
                        }
                    })
                }
            });

            // 队列文件上传成功时候
            uploader.on('uploadSuccess', function (file, response) {
                if (response.code === 200) {
                    // 上传成功，设置页面表单显示的数据
                    $("#ad_file").val(response.data.file_path);
                    $("#width").val(response.data.width);
                    $("#height").val(response.data.height);
                    // 通知成功
                    $('#' + file.id).find('span.state').text('已上传');
                    Notify(response.message, 'top-right', '5000', 'success', 'fa-check', true);
                } else {
                    Notify(response.message, 'top-right', '5000', 'danger', 'fa-check', true);
                }

            });

            // 队列文件上传出错时
            uploader.on('uploadError', function (file) {
                $('#' + file.id).find('span.state').text('上传出错');
            });

            // 当有错误的时候报错处理
            uploader.on('error', function (error) {
                console.log(error);
                if (error === 'Q_EXCEED_NUM_LIMIT') {
                    Notify('您已经选择了文件，如果想更换，请先删除之前的', 'top-right', '5000', 'danger', 'fa-check', true);
                }
                if (error === "Q_TYPE_DENIED") {
                    Notify('您上传的文件格式不正确，请用专业软件处理后再上传', 'top-right', '5000', 'danger', 'fa-check', true);
                }
            });
        });

        // 提交表单数据，创建广告
        function postForm() {
            let url = $("#EditData").attr('action');
            let data = $("#EditData").serialize();
            $.post(url, data, function (res) {
                if (res.code === 200) {
                    Notify(res.message, 'top-right', '5000', 'success', 'fa-check', true);
                    setTimeout(function () {
                        window.location.href = "{{route('admin.advert.advert_list')}}"
                    })
                } else {
                    Notify(res.message, 'top-right', '5000', 'danger', 'fa-check', true);
                }
            })
        }
    </script>
@endsection