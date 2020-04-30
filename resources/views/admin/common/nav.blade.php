<div class="navbar">
    <div class="navbar-inner">
        <div class="navbar-container">
            <!-- Navbar Barnd -->
            <div class="navbar-header pull-left">
                <a href="{{route('admin.dashboard')}}" class="navbar-brand">
                    <small>
                        <img src="{{asset('admin_style/assets/img/logo.png')}}" alt=""/>
                    </small>
                </a>
            </div>
            <!-- /Navbar Barnd -->

            <!-- Sidebar Collapse -->
            <div class="sidebar-collapse" id="sidebar-collapse">
                <i class="collapse-icon fa fa-bars"></i>
            </div>
            <!-- /Sidebar Collapse -->
            <!-- Account Area and Settings --->
            <div class="navbar-header pull-right">
                <div class="navbar-account">
                    <ul class="account-area">
                        <li>
                            <a class="wave in dropdown-toggle" data-toggle="dropdown" title="Help" href="#">
                                <i class="icon fa fa-envelope"></i>
                                <span class="badge">3</span>
                            </a>
                            <!--最新反馈消息-->
                            <ul class="pull-right dropdown-menu dropdown-arrow dropdown-messages">
                                <li>
                                    <a href="#">
                                        <img src="{{asset('admin_style/assets/img/avatars/divyia.jpg')}}"
                                             class="message-avatar"
                                             alt="合作商名称">
                                        <div class="message">
                                                <span class="message-sender">
                                                    合作商名称
                                                </span>
                                            <span class="message-time">
                                                    2 分钟前
                                                </span>
                                            <span class="message-subject">
                                                    合作商反馈标题
                                                </span>
                                            <span class="message-body">
                                                    合作商反馈内容
                                                </span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <img src="{{asset('admin_style/assets/img/avatars/divyia.jpg')}}"
                                             class="message-avatar"
                                             alt="合作商名称">
                                        <div class="message">
                                                <span class="message-sender">
                                                    合作商名称
                                                </span>
                                            <span class="message-time">
                                                    2 分钟前
                                                </span>
                                            <span class="message-subject">
                                                    合作商反馈标题
                                                </span>
                                            <span class="message-body">
                                                    合作商反馈内容
                                                </span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <img src="{{asset('admin_style/assets/img/avatars/divyia.jpg')}}"
                                             class="message-avatar"
                                             alt="合作商名称">
                                        <div class="message">
                                                <span class="message-sender">
                                                    合作商名称
                                                </span>
                                            <span class="message-time">
                                                    2 分钟前
                                                </span>
                                            <span class="message-subject">
                                                    合作商反馈标题
                                                </span>
                                            <span class="message-body">
                                                    合作商反馈内容
                                                </span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                            <!--/最新反馈消息-->
                        </li>
                        <li>
                            <a class="login-area dropdown-toggle" data-toggle="dropdown">
                                <div class="avatar" title="个人中心">
                                    <img src="{{$admin_data['avatar']}}">
                                </div>
                                <section>
                                    <h2><span class="profile"><span>{{$admin_data['account']}}</span></span></h2>
                                </section>
                            </a>
                        </li>
                        <li>
                            <a class="login-area dropdown-toggle" data-toggle="dropdown">
                                <section>
                                    <h2><span class="text">登录IP：{{$admin_data['ip']}}</span></h2>
                                </section>
                            </a>
                        </li>
                        <li>
                            <a class="login-area dropdown-toggle" data-toggle="dropdown">
                                <section>
                                    <h2><span class="text">登录地址：{{$admin_data['address']}}</span></h2>
                                </section>
                            </a>
                        </li>
                    </ul>
                    <div class="setting">
                        <a title="退出系统" href="{{route('admin.quit')}}">
                            <i class="icon glyphicon glyphicon-off"></i>
                        </a>
                    </div>
                    <!-- Settings -->
                </div>
            </div>
            <!-- /Account Area and Settings -->
        </div>
    </div>
</div>