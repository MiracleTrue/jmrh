@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/passwd.css')}}">

@endsection
@section('content')
  <div class="pass-box">
			<div>修改密码</div>
			<p><span>用户名</span><input type="" name="" id="" value="" /></p>

			<p><span>手机</span><input type="" name="" id="" value="" /></p>

			<p><span>姓名</span><input type="" name="" id="" value="" /></p>
			<div style="margin: 0 auto;">
			<a href="#" class="pass-submit">
				提交
			</a>
			<a href="" class="pass-reset">重置</a>
		</div>
		</div>
	
@endsection