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
				<h5>商品名称：<span class="product_name">{{$product_info['product_name']}}</span></h5>
				<p class="productshow_p1">所属分类：{{$product_info['category_info']['category_name']}}</p>
				<p class="productshow_price">价格：<span style="font-weight: bolder;" class="product_price">{{$product_info['product_price']}}</span><span>元</span></p>
				<p class="productnumber" style="font-size: 16px;margin-top: 5px;">数量：<input class="product_number" style="height: 24px;display: inline-block;" type="text" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}"  
    onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'0')}else{this.value=this.value.replace(/\D/g,'')}" value=""placeholder="请填入商品数量"/></p>
				<p class="productunit" style="font-size: 16px;margin-top: 8px;">单位：<span class="product_unit" style="font-weight: bolder;">{{$product_info['product_unit']}}</span></p>
				
				<p class="productshow_p2"><span class="productshow_pspan1">详情描述</span><span class="productshow_pspan2">下单</span></p>
				<p  class="productshow_p3">{!!$product_info['product_content']!!}</p>
			</div>
		</section>
@section('content')
@endsection
@section('MyJs')
<script type="text/javascript">
	$(function(){
		$(".goodsimg").height($(".goodsimg").width());
		
	})
	var identity={{$manage_user['identity']}};

	$(".backgo").click(function(){
		/*var url="{{url('welcome')}}"
		location.replace(url);*/
		
		
		parent.layer.closeAll();
		
	})
	if(identity == '2' || identity == '4'){
		$(".productshow_pspan2").show();
	}else{
			$(".productshow_pspan2").hide();
	}
	$(".productshow_pspan2").on("click",function(){
		product_name=$(".product_name").text();
		product_unit=$(".product_unit").text();
		product_number=$(".product_number").val();
	/*	console.log(product_unit)*/
		if(identity == '2'){
			/*平台*/
			  layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		       fixed :false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['965px' , '600px'],
		      content: '{{url('platform/need/view')}}'+'?product_name='+product_name+'&product_unit='+product_unit+'&product_number='+product_number
		    });
		}else if(identity == '4'){
			layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		      fixed :false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['965px' , '550px'],
		      content: '{{url('army/need/view')}}'+'?product_name='+product_name+'&product_unit='+product_unit+'&product_number='+product_number
		    });
		}
	});
	
</script>
@endsection