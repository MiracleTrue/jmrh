<aside class="home-aside">
    <ul class="sidebar-menu">
    	
    	

		@if($manage_user['identity'] == '1')<!--超级管理员-->
		
		<li class="treeview">
            <a href="{{url('welcome')}}">
                <i class="fa fa-home"></i> <span>首页</span>
            </a>

        </li>
        <li class="treeview">
            <a href="{{url('army/need/list')}}">
                <i class="fa fa-edit" style="width: 19px;"></i>
                <span>军方发布需求</span>
                <!--<span class="label label-primary pull-right">4</span>-->
            </a>

        </li>

        <li class="treeview">
            <a href="{{url('supplier/need/list')}}">
                <i class="fa fa-cart-arrow-down"></i>
                <span>供应商</span>

            </a>

        </li>
        <li class="treeview">
            <a href="{{url('platform/need/list')}}">
                <i class="fa fa-laptop"></i>
                <span>平台</span>

            </a>

        </li>
        <li class="treeview">
            <a href="{{url('user/list')}}">
                <i class="fa fa-user"></i> <span>用户管理</span>
            </a>

        </li>
        <li class="treeview">
            <a href="{{url('product/list')}}">
                <i class="fa fa-bars"></i> <span>商品管理</span>

            </a>

        </li>
         <li class="treeview">
            <a href="{{url('category/list')}}">
                <i class="fa  fa-ellipsis-v"></i> <span>分类管理</span>

            </a>

        </li>
        <li class="password">
            <a href="javascript:void(0)">
                <i class="fa fa-lock"></i> <span>密码修改</span>
                <!--<small class="label pull-right label-danger">3</small>-->
            </a>
        </li>
        <li class="munu-caozuorizhi">
            <a href="{{url('log/list')}}">
                <i class="fa fa-envelope-square"></i> <span>操作日志</span>
                <!--<small class="label pull-right label-warning">12</small>-->
            </a>
        </li>
         <li class="munu-woderizhi">
            <a href="{{url('log/manage')}}">
                <i class="fa fa-user-o fa-lg"></i> <span>我的日志</span>
                <!--<small class="label pull-right label-warning">12</small>-->
            </a>
        </li>
		@elseif($manage_user['identity'] == '2')<!--平台运营员-->
							
							
							
							
							
		<li class="treeview">
            <a href="{{url('welcome')}}">
                <i class="fa fa-home"></i> <span>首页</span>
            </a>

        </li>
      
        <li class="treeview">
            <a href="{{url('platform/need/list')}}">
                <i class="fa fa-laptop"></i>
                <span>平台</span>

            </a>

        </li>
       
        <li class="treeview">
            <a href="{{url('product/list')}}">
                <i class="fa fa-bars"></i> <span>商品管理</span>

            </a>

        </li>
       
        <li class="password">
            <a href="javascript:void(0)">
                <i class="fa fa-lock"></i> <span>密码修改</span>
                <!--<small class="label pull-right label-danger">3</small>-->
            </a>
        </li>
        <li class="munu-caozuorizhi">
            <a href="{{url('log/list')}}">
                <i class="fa fa-envelope-square"></i> <span>操作日志</span>
                <!--<small class="label pull-right label-warning">12</small>-->
            </a>
        </li>
      
		@elseif($manage_user['identity'] == '3')<!--供货商-->
				
				
				
		

        <li class="treeview">
            <a href="{{url('supplier/need/list')}}">
                <i class="fa fa-cart-arrow-down"></i>
                <span>供应商</span>

            </a>

        </li>
      
        <li class="password">
            <a href="javascript:void(0)">
                <i class="fa fa-lock"></i> <span>密码修改</span>
                <!--<small class="label pull-right label-danger">3</small>-->
            </a>
        </li>
        <li class="munu-caozuorizhi">
            <a href="{{url('log/list')}}">
                <i class="fa fa-envelope-square"></i> <span>操作日志</span>
                <!--<small class="label pull-right label-warning">12</small>-->
            </a>
        </li>
        
		@elseif($manage_user['identity'] == '4')<!--军方-->
			
			
		<li class="treeview">
            <a href="{{url('welcome')}}">
                <i class="fa fa-home"></i> <span>首页</span>
            </a>

        </li>
        <li class="treeview">
            <a href="{{url('army/need/list')}}">
                <i class="glyphicon glyphicon-edit" style="width: 19px;"></i>
                <span>军方发布需求</span>
                <!--<span class="label label-primary pull-right">4</span>-->
            </a>

        </li>

      
        <li class="password">
            <a href="javascript:void(0)">
                <i class="fa fa-lock"></i> <span>密码修改</span>
                <!--<small class="label pull-right label-danger">3</small>-->
            </a>
        </li>
        <li class="munu-caozuorizhi">
            <a href="{{url('log/list')}}">
                <i class="fa fa-envelope-square"></i> <span>操作日志</span>
                <!--<small class="label pull-right label-warning">12</small>-->
            </a>
        </li>
       
			
		@endif



    </ul>

</aside>