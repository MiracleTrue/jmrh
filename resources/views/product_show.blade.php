@extends('layouts.master')
   
@section('MyCss')
 <link rel="stylesheet" href="{{asset('webStatic/css/details.css')}}">
<style>
	
	 .error{
		color: red;
		margin-right: 10px;
		}
	input{
		width: 257px;
		height: 36px;
		line-height: 36px;
	}	
</style>
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
				<img style="width: 100%;height: 100%;" src="{{$product_info['product_thumb']}}" onerror="this.src='{{asset('webStatic/images/noimg.png')}}'"/>
			</div>
			<div class="detalsdata">
				<h5>商品名称：<span class="product_name">{{$product_info['product_name']}}</span></h5>
				<p class="productshow_p1">所属分类：{{$product_info['category_info']['category_name']}}</p>
				
				<div class="productshow_pspan1" style="width: 65px;margin-bottom: 10px;margin-top: 10px;">规格</div>
			<!--规格图片-->
			<div class="guige">
				<ul style="overflow: hidden;">
						@foreach($product_info['spec_info'] as $item)
						<li spec_unit="{{$item['spec_unit']}}" spec_id="{{$item['spec_id']}}" style="float: left;width: 77px;height:100px;margin-right: 12px;cursor: pointer;"><img src="{{$item['image_thumb']}}" onerror="this.src='{{asset('webStatic/images/noimg.png')}}'" style="width: 100%;height: 77px;border: 3px solid #FFFFFF;"/><p style="text-align: center;font-size: 16px;font-weight: bold;line-height: 16px;">{{$item['spec_name']}}</p></li>
					@endforeach
				</ul>
			</div>
			
			
			
				<form id="add_car" class="add_car" action="" method="post">
					
					
				<p class="productnumber" style="font-size: 16px;margin-top: 13px;">数量：
				<input type="number" name="product_number" id="" value="" min="0" placeholder="请输入数量" style="margin-left: 16px;"/>
  <!--  onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'0')}else{this.value=this.value.replace(/\D/g,'')}" value=""placeholder="请填入商品数量"/>--></p>
				<p class="productunit" style="font-size: 16px;margin-top: 10px;margin-bottom: 10px;">单位：<span class="product_unit" style="font-weight: bolder;"></span></p>
				
				@if($manage_user['identity'] == '4')
				<p class="productnumber" style="font-size: 16px;margin-top: 9px;"><span style="width: 70px;display: inline-block;">到货时间   </span><input name="army_receive_time" class="product_number laydate-icon" style="height: 36px;display: inline-block;" onClick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss',min: laydate.now(0, 'YYYY-MM-DD 00:00:00')})" /> </p> 
				<p class="productnumber" style="font-size: 16px;margin-top: 9px;"><span style="width: 70px;display: inline-block;">联系人</span><input name="contact_person" class="product_number" style="height: 36px;display: inline-block;" type="text" /> </p> 
				<p class="productnumber" style="font-size: 16px;margin-top: 9px;"><span style="width: 70px;display: inline-block;">电话 </span><input name="contact_tel" class="product_number" style="height: 36px;display: inline-block;" type="text" /> </p> 
				<p class="productnumber" style="font-size: 16px;margin-top: 9px;"><span style="width: 70px;display: inline-block;">备注 </span><input name="note" class="product_number" type="text" style="height: 36px;display: inline-block;"  value=""/> </p> 
				@endif
				<p><input type="submit" class="productshow_pspan1 addshop" style="width: 164px;height: 63px;line-height: 63px;border-radius: 40px;font-size: 18px;float: left;"  name="" id="" value="加入购物车" />
					<span class="productshow_pspan1 goshop" style="width: 164px;height: 63px;line-height: 63px;border-radius: 40px;font-size: 18px;background: #fe8d01;margin-left: 20px;">去购物车</span>
					</p>
				
				<input type="hidden" name="product_id" id="product_id" value="{{$product_info['product_id']}}" />
				</form>
				
				<p class="productshow_p2"><span class="productshow_pspan1">详情描述</span>
					<!--<span class="productshow_pspan2">下单</span>-->
				</p>
				<p  class="productshow_p3">{!!$product_info['product_content']!!}</p>
			</div>
		</section>
@section('content')
@endsection
@section('MyJs')
<script type="text/javascript" src="{{asset('/webStatic/library/jquery-calendar/js/laydate.js')}}"></script>
<script type="text/javascript" src="{{asset('webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
<script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>


<script type="text/javascript">
	$(function(){
		$(".goshop").click(function(){
			var indexlayer1=layer.open({
	            type: 2,
	            title: false,
	            maxmin: false,
	             fixed :false,
	            shadeClose: true, //点击遮罩关闭层
	            closeBtn: 0,
	             isOutAnim: false,
	             anim: -1,
	             area: ['100%', '100%'],
	            content: "{{url('cart/list')}}",
	          
	           
	        });
	        layer.full(indexlayer1);
			$("#Info1").attr("src","{{url('cart/list')}}")
		})
		
		
		
		
		
		var spec_id;
		laydate.skin('molv');
		$(".goodsimg").height($(".goodsimg").width());
		
		spec_id=$(".guige li").eq(0).attr("spec_id");
		/*商品规格选择*/
		$(".guige li img").eq(0).css("border","3px solid #feb501");
		$(".guige li img").click(function(){
			var that=$(this);
			$(this).css("border","3px solid #feb501").parent().siblings().find("img").css("border","3px solid #ffffff");
		})
		
		$(".guige li").on("click",function(){
			 $(".product_unit").text($(this).attr("spec_unit"));   
			      spec_id=$(this).attr("spec_id");
		})
		
		$(".guige li").eq(0).click()
	
	/*添加购物车*/
		var addspec= $("#add_car").validate({
	        rules: {
	          army_receive_time: {
	            required: true,
	           
	          },
	          product_number:{
	          	required: true,
	          	number:true,
	          	min:0
	          }
	          	

	        },
	         messages: {
		      army_receive_time: {
		      	required:"请选择到货时间",
		      
		      },
		        product_number:{
		          	required: "请输入商品数量",
		          	min:"商品数量不能小于0"
	          }
		    }, 
		    submitHandler: function (form) {
		          $(form).ajaxSubmit({
		            url: '{{url("cart/add")}}',
		            type: 'POST',
		            dataType: 'JSON',
		            data:{
		            	spec_id:spec_id,
		            	_token:'{{csrf_token()}}'
		            },
		            beforeSend:function(res){
		            	
		            	 $(".addshop").attr("disabled","true");
		            
		            	
		            },
		            success: function (res) {
		        	
		        	
			          if(res.code==0){			             	
			             	  layer.msg(res.messages, {icon: 1, time: 1000},function(){  
			             	 $(".addshop").removeAttr("disabled");
			             	
			             	   });
							addspecstate=true;
			             }else{
			             	
			             
			             	   layer.msg(res.messages, {icon: 2, time: 1000},function(){
			             	 			 $(".addshop").removeAttr("disabled");
			             	   });
			             }
		            }
		          });
	        }
	
	      });	
	
		
		
		
		
		
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