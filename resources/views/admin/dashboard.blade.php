@extends('admin.app')

@section('title', $title)
{{--引入其他css样式--}}
@section('style')
    <style>
        .tickets-container .tickets-list .ticket-item .ticket-type {
            height: unset !important;
            min-height: 50px !important;
            word-wrap: break-word;
        }

        .tickets-container .tickets-list .ticket-item .ticket-type .type {
            text-transform: unset !important;
        }

        .databox .databox-piechart span {
            display: inline-block;
            width: 45px;
            line-height: 45px;
            font-size: 28px;
        }
    </style>
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
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    {{--账号信息--}}
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="databox radius-bordered databox-shadowed databox-graded">
                            <div class="databox-left no-padding bordered-left-2 bordered-palegreen">
                                <img src="{{$admin_data['avatar']}}" style="width:65px; height:65px;">
                            </div>
                            <div class="databox-right padding-top-20">
                                <div class="databox-stat palegreen">
                                    <i class="stat-icon icon-xlg fa fa-user"></i>
                                </div>
                                <div class="databox-text darkgray">账号：{{$admin_data['account']}}</div>
                                <div class="databox-text darkgray">角色：{{$admin_data['role_name']}}</div>
                            </div>
                        </div>
                    </div>

                    {{--合作商户--}}
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="databox radius-bordered databox-shadowed databox-graded">
                            <div class="databox-left bg-themeprimary">
                                <div class="databox-piechart">
                                    <span class="fa fa-users"></span>
                                </div>
                            </div>
                            <div class="databox-right">
                                <span class="databox-number themeprimary">{{ $merchant_num }}</span>
                                <div class="databox-text darkgray">合作商户</div>
                                <div class="databox-state bg-themeprimary">
                                    <i class="fa fa-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="databox radius-bordered databox-shadowed databox-graded">
                            <div class="databox-left bg-themesecondary">
                                <div class="databox-piechart">
                                    <span class="fa fa-question-circle"></span>
                                </div>
                            </div>
                            <div class="databox-right">
                                <span class="databox-number themesecondary">0</span>
                                <div class="databox-text darkgray">问题反馈</div>
                                <div class="databox-stat themesecondary radius-bordered">
                                    <i class="stat-icon icon-lg fa fa-tasks"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="databox radius-bordered databox-shadowed databox-graded">
                            <div class="databox-left bg-themefourthcolor">
                                <div class="databox-piechart">
                                    <span class="fa fa-road"></span>
                                </div>
                            </div>
                            <div class="databox-right">
                                <span class="databox-number themefourthcolor">{{ $advert_num }}</span>
                                <div class="databox-text darkgray">广告数量</div>
                                <div class="databox-stat themefourthcolor radius-bordered">
                                    <i class="stat-icon  icon-lg fa fa-adn"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--登录日志--}}
        <div class="row">
            <div class="col-lg-12">
                <div class="well with-header with-footer">
                    <div class="header bordered-sky">
                        登录日志
                    </div>
                    <table class="table table-hover">
                        <thead class="bordered-darkorange">
                        <tr>
                            <th>
                                #
                            </th>
                            <th>
                                账号
                            </th>
                            <th>
                                角色
                            </th>
                            <th>
                                ip
                            </th>
                            <th>
                                地址
                            </th>
                            <th>
                                登录时间
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($login_log as $key=> $val)
                            <tr>
                                <td>
                                    {{$val['id']}}
                                </td>
                                <td>
                                    {{$val['account']}}
                                </td>
                                <td>
                                    {{$val['role']}}
                                </td>
                                <td>
                                    {{$val['ip']}}
                                </td>
                                <td>
                                    {{$val['address']}}
                                </td>
                                <td>
                                    {{$val['created_at']}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="footer">
                        <div class="pull-right">
                            {{ $login_log->appends(array_except(Request::query(), 'login_page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--操作日志--}}
        <div class="row">
            <div class="col-lg-12">
                <div class="widget">
                    <div class="widget-header bordered-bottom bordered-themesecondary">
                        <i class="widget-icon fa fa-tags themesecondary"></i>
                        <span class="widget-caption themesecondary">操作日志</span>
                    </div><!--Widget Header-->
                    <div class="widget-body">
                        <div class="widget-main no-padding">
                            <div class="tickets-container">
                                <ul class="tickets-list">
                                    @foreach($operation_log as $key=>$val)
                                        <li class="ticket-item">
                                            <div class="row">
                                                <div class="ticket-user col-lg-4 col-sm-12">
                                                    <img src="{{config('app.url').$val['avatar']}}"
                                                         class="user-avatar">
                                                    <span class="user-name">{{$val['role_name']}}</span>
                                                    <span class="user-at">at</span>
                                                    <span class="user-company">{{$val['account']}}</span>
                                                </div>
                                                <div class="ticket-type  col-lg-6 col-sm-6 col-xs-12">
                                                    <span class="divider hidden-xs"></span>
                                                    <span class="type"><code>{{$val['content']}}</code></span>
                                                </div>
                                                <div class="ticket-time  col-lg-2 col-sm-6 col-xs-12">
                                                    <div class="divider hidden-md hidden-sm hidden-xs"></div>
                                                    <i class="fa fa-clock-o"></i>
                                                    <span class="time">{{$val['created_at']}}</span>
                                                </div>
                                                <div class="ticket-state bg-palegreen">
                                                    <i class="fa fa-info"></i>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <br>
                                <div class="pull-right">
                                    {{$operation_log->appends(array_except(Request::query(), 'operation_page'))->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


{{--引入其他js脚本--}}
@section('script')

@endsection