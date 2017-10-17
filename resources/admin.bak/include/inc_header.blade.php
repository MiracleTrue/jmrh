<header class="navbar-wrapper">
    <div class="navbar navbar-fixed-top">
        <div class="container-fluid cl">
            <a class="logo navbar-logo f-l mr-10 hidden-xs" href="{{action('Admin\IndexController@Index')}}">{{\App\Models\CommonModel::languageFormat($shop_config['shop_name'],$shop_config['shop_en_name']).'-'.__('admin.backSystem')}}</a>
            <a class="logo navbar-logo-m f-l mr-10 visible-xs" href="{{action('Admin\IndexController@Index')}}">{{__('admin.backSystem')}}</a>
            <span class="logo navbar-slogan f-l mr-10 hidden-xs">v1.0</span>
            <a aria-hidden="false" class="nav-toggle Hui-iconfont visible-xs" href="javascript:;">&#xe667;</a>
            {{--<nav class="nav navbar-nav">--}}
                {{--<ul class="cl">--}}
                    {{--<li class="dropDown dropDown_hover"><a href="javascript:;" class="dropDown_A"><i class="Hui-iconfont">&#xe600;</i> 新增 <i class="Hui-iconfont">&#xe6d5;</i></a>--}}
                        {{--<ul class="dropDown-menu menu radius box-shadow">--}}
                            {{--<li><a href="javascript:;" onclick="article_add('添加资讯','article-add.html')"><i class="Hui-iconfont">&#xe616;</i> 资讯</a></li>--}}
                            {{--<li><a href="javascript:;" onclick="picture_add('添加资讯','picture-add.html')"><i class="Hui-iconfont">&#xe613;</i> 图片</a></li>--}}
                            {{--<li><a href="javascript:;" onclick="product_add('添加资讯','product-add.html')"><i class="Hui-iconfont">&#xe620;</i> 产品</a></li>--}}
                            {{--<li><a href="javascript:;" onclick="member_add('添加用户','member-add.html','','510')"><i class="Hui-iconfont">&#xe60d;</i> 用户</a></li>--}}
                        {{--</ul>--}}
                    {{--</li>--}}
                {{--</ul>--}}
            {{--</nav>--}}
            <nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
                <ul class="cl">
                    <li>{{session('AdminUser')['admin_role']['role_name']}}</li>
                    <li class="dropDown dropDown_hover">
                        <a href="#" class="dropDown_A">{{session('AdminUser')['admin_name']}} <i class="Hui-iconfont">&#xe6d5;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a href="javascript:;" onClick="myselfinfo()">{{__('common.personalInformation')}}</a></li>
                            <li><a href="{{action('Admin\IndexController@Logout')}}">{{__('common.logout')}}</a></li>
                        </ul>
                    </li>
                    <li id="Hui-msg"> <a href="#" title="{{__('common.information')}}"><span class="badge badge-danger">1</span><i class="Hui-iconfont" style="font-size:18px">&#xe68a;</i></a> </li>
                    <li id="Hui-skin" class="dropDown right dropDown_hover"> <a href="javascript:;" class="dropDown_A" title="{{__('admin.header.skin')}}"><i class="Hui-iconfont" style="font-size:18px">&#xe62a;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a href="javascript:;" data-val="default" title="{{__('admin.header.default')}}">{{__('admin.header.default')}}</a></li>
                            <li><a href="javascript:;" data-val="blue" title="{{__('admin.header.blue')}}">{{__('admin.header.blue')}}</a></li>
                            <li><a href="javascript:;" data-val="green" title="{{__('admin.header.green')}}">{{__('admin.header.green')}}</a></li>
                            <li><a href="javascript:;" data-val="red" title="{{__('admin.header.red')}}">{{__('admin.header.red')}}</a></li>
                            <li><a href="javascript:;" data-val="yellow" title="{{__('admin.header.yellow')}}">{{__('admin.header.yellow')}}</a></li>
                            <li><a href="javascript:;" data-val="orange" title="{{__('admin.header.orange')}}">{{__('admin.header.orange')}}</a></li>
                        </ul>
                    </li>
                    <li class="dropDown dropDown_hover">
                        <a href="#" class="dropDown_A">{{__('common.language')}}({{__('common.lang')}})<i class="Hui-iconfont">&nbsp;&#xe6d5;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a href="{{action('Admin\IndexController@SetLanguage','zh')}}">中文</a></li>
                            <li><a href="{{action('Admin\IndexController@SetLanguage','en')}}">English</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>