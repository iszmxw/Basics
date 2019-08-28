{{--引入菜单样式渲染工具--}}
@inject('Tools', 'App\Library\Tools')
<ul class="nav sidebar-menu">
    @foreach($system_menu as $key=>$val)
        <li {!! $Tools->li_class($val, request()->path()) !!}>
            <a href="{{empty($val['route']) ? 'javascript:;' : url($val['route']) }}" {{count($val['menu_sub']) <= 0 ? : 'class=menu-dropdown'}}>
                <i class="{{$val['icon']}}"></i>
                <span class="menu-text">{{$val['name']}}</span>
                @if(count($val['menu_sub']) > 0)
                    <i class="menu-expand"></i>
                @endif
            </a>
            @if(count($val['menu_sub']) > 0)
                <ul class="submenu">
                    @foreach($val['menu_sub'] as $k=>$v)
                        <li {!! $Tools->li_class($v, request()->path(), true) !!}>
                            <a href="{{empty($v['route']) ? 'javascript:;' : url($v['route']) }}">
                                <span class="menu-text">{{$v['name']}}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
    {{--退出登录--}}
    <li>
        <a href="{{route('admin.quit')}}">
            <i class="menu-icon glyphicon glyphicon-off"></i>
            <span class="menu-text"> 退出系统 </span>
        </a>
    </li>
</ul>