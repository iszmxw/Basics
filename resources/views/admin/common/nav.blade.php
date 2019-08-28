<div class="navbar">
    <div class="navbar-inner">
        <div class="navbar-container">
            <!-- Navbar Barnd -->
            <div class="navbar-header pull-left">
                <a href="{{route('admin.dashboard')}}" class="navbar-brand">
                    <small>
                        <img src="{{asset('admin/assets/img/logo.png')}}" alt=""/>
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
                            <!--Messages Dropdown-->
                            <ul class="pull-right dropdown-menu dropdown-arrow dropdown-messages">
                                <li>
                                    <a href="#">
                                        <img src="{{asset('admin/assets/img/avatars/divyia.jpg')}}"
                                             class="message-avatar"
                                             alt="Divyia Austin">
                                        <div class="message">
                                                <span class="message-sender">
                                                    Divyia Austin
                                                </span>
                                            <span class="message-time">
                                                    2 minutes ago
                                                </span>
                                            <span class="message-subject">
                                                    Here's the recipe for apple pie
                                                </span>
                                            <span class="message-body">
                                                    to identify the sending application when the senders image is shown for the main icon
                                                </span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <img src="{{asset('admin/assets/img/avatars/bing.png')}}" class="message-avatar"
                                             alt="Microsoft Bing">
                                        <div class="message">
                                                <span class="message-sender">
                                                    Bing.com
                                                </span>
                                            <span class="message-time">
                                                    Yesterday
                                                </span>
                                            <span class="message-subject">
                                                    Bing Newsletter: The January Issue‏
                                                </span>
                                            <span class="message-body">
                                                    Discover new music just in time for the Grammy® Awards.
                                                </span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <img src="{{asset('admin/assets/img/avatars/adam-jansen.jpg')}}"
                                             class="message-avatar"
                                             alt="Divyia Austin">
                                        <div class="message">
                                                <span class="message-sender">
                                                    Nicolas
                                                </span>
                                            <span class="message-time">
                                                    Friday, September 22
                                                </span>
                                            <span class="message-subject">
                                                    New 4K Cameras
                                                </span>
                                            <span class="message-body">
                                                    The 4K revolution has come over the horizon and is reaching the general populous
                                                </span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                            <!--/Messages Dropdown-->
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