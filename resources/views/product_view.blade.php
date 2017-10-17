@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/addclassify.css')}}">

@endsection
@section('content')
  <div class="csy-box">
			<header>添加分类</header>
			<div>
				<p>
					<span>所属分类</span>
					<select name="">
						<option value="">肉类</option>
					</select>
				</p>
				<p>
					<span>商品图片</span>
					<input type="" name="" id="" value="" />
					<a><img src="img/shizi.png" alt="" /></a>
				</p>
			</div>

			<div>
				<p>
					<span>商品名称</span>
					<input type="" name="" id="" value="" />
				</p>
			</div>
			<div class="csy-ope">
				<a href="#" class="csy-submit">
					提交
				</a>
				<a href="" class="csy-reset">重置</a>
			</div>
		</div>
	
@endsection
