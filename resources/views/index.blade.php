<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>首页</title>
    <link rel="stylesheet" href="{{asset('webStatic/css/reset.css')}}"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{asset('webStatic/css/sidebar-menu.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/css/home.css')}}"/>
    <script type="text/javascript" src="{{asset('webStatic/library/jquery-1.11.0/jquery-1.11.0.js')}}"></script>
    <script type="text/javascript" src="{{asset('webStatic/library/layer-v3.1.0/layer/layer.js')}}"></script>
    
</head>
<body>
    @include('include.inc_menus')
    @include('include.inc_header')
    <section class="home-section">
        <iframe name="Info1" id="Info1" frameborder="0" src="{{url('welcome')}}" width="100%" scrolling="yes" frameborder="0">
        </iframe>
    </section>
</body>
<script type="text/javascript">
	
    $(document).ready(function() {
        console.log($(document).height() - 42);
        $(".home-aside").height($(document).height());
        var iheight = $(document).height() - 42;
        $("#Info1").height(iheight)


	$(".sidebar-menu li").not($(".password")).on("click",function(){
		console.log($(this).find('a').attr('href'));
		$("#Info1").attr("src",$(this).find('a').attr('href'));
		return false;
	})
	$(".sidebar-menu li").on("click",function(){
		$(this).css("background-color","#fe8d01").siblings().css("background-color","#EFEFEF");
		$(this).find("a").css("color","#fff");
		$(this).siblings().find("a").css("color","#000000");
	})

    });
    ! function() {

			//页面一打开就执行，放入ready是为了layer所需配件（css、扩展模块）加载完毕
			layer.ready(function() {
				$('.password').on('click', function() {
					
					layer.open({
						type: 2,
						title: false,
						maxmin: false,
						shadeClose: true, //点击遮罩关闭层
						area: ['620px', '514px'],
						content: '{{url('password/original/view')}}'
					});
				});
			});

		}();
</script>
</html>