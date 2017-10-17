<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>首页</title>
    {{--<link rel="stylesheet" href="css/reset.css"/>--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/reset.css')}}"/>
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">--}}
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">--}}
    <script type="text/javascript" src="{{asset('webStatic/library/jquery-1.11.0/jquery-1.11.0.js')}}"></script>
    <script type="text/javascript" src="{{asset('webStatic/library/layer-v3.1.0/layer/layer.js')}}"></script>
@section('MyCss')
    @show
</head>
<body>
@yield('content')
</body>
@section('MyJs')
@show
</html>
