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
    <script type="text/javascript" src="{{asset('webStatic/library/jquery-1.11.0/jquery-1.11.0.js')}}"></script>
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
$(".password").on("click",function(){
	
})

    });
</script>
</html>