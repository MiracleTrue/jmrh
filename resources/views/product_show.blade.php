@extends('layouts.master')
    <link rel="stylesheet" href="{{asset('webStatic/css/details.css')}}">

@section('MyCss')
@endsection
	<section style="padding-left: 2%;">
			<div class="head">
				
					<div class="point">

					</div>
					<span>
						您当前的位置：
					</span>
					<i>首页</i>-
					<i>{{$product_info['category_info']['category_name']}}</i>-
					<i>{{$product_info['product_name']}}</i>
					<a class="backgo" href="#">
						返回上级
					</a>
			

			</div>
			<div class="goodsimg">
				<img style="width: 100%;height: 100%;" src="{{$product_info['product_original']}}"/>
			</div>
			<div class="detalsdata">
				<h5>商品名称：{{$product_info['product_name']}}</h5>
				<p>所属分类：{{$product_info['category_info']['category_name']}}</p>
				<p>详情描述</p>
				<p>{!!$product_info['product_content']!!}</p>
			</div>
		</section>
@section('content')
@endsection
@section('MyJs')
<script type="text/javascript">
	$(".goodsimg").height($(".goodsimg").width());
	$(".backgo").click(function(){
		var url="{{url('welcome')}}"
		location.replace(url);
		
	})
</script>
@endsection