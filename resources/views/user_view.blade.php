@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/adduser.css')}}">

@endsection
@section('content')
    <div class="adr-box">
			<header>添加账户</header>
			<p><span>用户名</span><input type="" name="" id="" value="" /></p>

			<p><span>手机</span><input type="" name="" id="" value="" /></p>

			<p><span>姓名</span><input type="" name="" id="" value="" /></p>

			<p><span>账户分类</span>
				<select name="">
					<option value="">请选择</option>
				</select>
			</p>

			<p><span>密码</span><input type="" name="" id="" value="" /></p>

			<p><span>确认密码</span><input type="" name="" id="" value="" /></p>

		
		<div style="margin: 0 auto;">
			<a href="#" class="adr-submit">
				提交
			</a>
			<a href="" class="adr-reset">重置</a>
		</div>
		</div>
@endsection
