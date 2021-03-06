<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name=renderer content=webkit>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
    <title>首页</title>
    <link rel="stylesheet" href="{{asset('webStatic/css/reset.css')}}"/>
     <link rel="stylesheet" href="{{asset('webStatic/library/font-awesome-4.7.0/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/css/sidebar-menu.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/css/home.css')}}"/>
    <script type="text/javascript" src="{{asset('webStatic/library/jquery-1.11.0/jquery-1.11.0.js')}}"></script>
    <script type="text/javascript" src="{{asset('webStatic/library/layer-v3.1.0/layer/layer.js')}}"></script>
    
</head>
<body>
    @include('include.inc_menus')
    <div id="index_right">
    @include('include.inc_header')
	    <div class="home-section">
	        <iframe name="Info1" id="Info1" frameborder="0" src="{{$iframe_url}}" width="100%" scrolling="yes" frameborder="0">
	        </iframe>
	    </div>
    </div>
</body>
<script type="text/javascript">
	var that;
		var myDate = new Date();
	
		$(".lu_year").text(myDate.getFullYear());
		$(".lu_month").text(myDate.getMonth()+1)
		$(".lu_day").text(myDate.getDate())

	
	
    $(document).ready(function() {
      
        $(".home-aside").height($(document).height());
        var iheight = $(document).height() - 42;
        $("#Info1").height(iheight)


/*判断是否为ie*/
if (window.ActiveXObject || "ActiveXObject" in window){
	  $(".home-aside").height($(document).height()-5);
        var iheight = $(document).height() - 42;
        $("#Info1").height(iheight-10)
}



	$(".sidebar-menu li").not($(".password")).on("click",function(){
		
		$("#Info1").attr("src",$(this).find('a').attr('href'));
		return false;
	})
	
	
	
	$(".headerdiv1 a").on("click",function(){
		
		$("#Info1").attr("src",$(this).attr("href"));
		return false;
	})
	$(".cartbutton").click(function(){
		$("#Info1").attr("src",$(this).attr("href"));
		return false;
	})
	
	
	$(".sidebar-menu li").eq(0).css("background-color","#fe8d01");
	$(".sidebar-menu li").eq(0).find("a").css("color","#fff");
	
	$(".sidebar-menu li").on("click",function(){
		$(this).css("background-color","#fe8d01").siblings().css("background-color","#EFEFEF");
		$(this).find("a").css("color","#fff");
		$(this).siblings().find("a").css("color","#000000");
		 that= $(this);
		
	})
	$(".sidebar-menu li").hover(function(){
		
		$(this).not(that).addClass("hoverli");
		$(this).not(that).find("a").addClass("hovera");
	
	
		
	},function(){
		  $(this).removeClass("hoverli");
		  	$(this).find("a").removeClass("hovera");
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
						fixed :false,
						shadeClose: true, //点击遮罩关闭层
						area: ['620px', '514px'],
						content: '{{url('password/original/view')}}'
					});
				});
			});

		}();
</script>
</html>