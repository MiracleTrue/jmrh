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
				<img style="width: 100%;height: 100%;" src="{{$product_info['product_original']}}" onerror="this.src='{{asset('webStatic/images/noimg.png')}}'"/>
			</div>
			<div class="detalsdata">
				<h5>商品名称：{{$product_info['product_name']}}</h5>
				<p class="productshow_p1">所属分类：{{$product_info['category_info']['category_name']}}</p>
				<p class="productshow_price">价格：<span>20.00</span><span>元</span></p>
				<p class="productshow_p2"><span class="productshow_pspan1">详情描述</span><span class="productshow_pspan2">下单</span></p>
				<p  class="productshow_p3">{!!$product_info['product_content']!!}</p>
			</div>
		</section>
@section('content')
@endsection
@section('MyJs')
<script type="text/javascript">
	var identity={{$manage_user['identity']}};
	$(".goodsimg").height($(".goodsimg").width());
	$(".backgo").click(function(){
		var url="{{url('welcome')}}"
		location.replace(url);
		
	})
	if(identity == '2' || identity == '4'){
		$(".productshow_pspan2").show();
	}else{
			$(".productshow_pspan2").hide();
	}
	$(".productshow_pspan2").on("click",function(){
	
		if(identity == '2'){
			/*平台*/
			  layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		       fixed :false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['925px' , '600px'],
		      content: '{{url('platform/need/view')}}'
		    });
		}else if(identity == '4'){
			layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		      fixed :false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['919px' , '500px'],
		      content: '{{url('army/need/view')}}'
		    });
		}
	});
	
</script>
@endsection