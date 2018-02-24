@extends('layouts.master')
@section('MyCss')
  <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/css/goods-management.css')}}">
    	<style>
    		.jia,.jian{
    			display: block;
    			width: 30px;
    			height: 30px;
    			background: #C3C3C3;
    			border-bottom: 1px solid #F6F6F6;
    			cursor: pointer;
    		}
    		.xiadan{
    			width: 240px;
    			height: 85px;
    			background: #FE8D01;
    			color: #FFFFFF;
    			font-weight: bolder;
    			line-height: 85px;
    			text-align: center;
    			border-radius: 40px;
    			font-size: 40px;
    			float: right;
    			margin-top: 37px;
    			margin-right: 20px;
    		}
    		.gouwuchediv{
    			height: 36px;
    			width: 92px;
    			background: #0e99dc;
    			color: #FFFFFF;
    			line-height: 36px;
    			text-align: center;
    			font-size: 15px;
    			margin-top: 60px;
    			margin-left: 40px;
    		}
    		.product_num{
    			width: 50%;
    		}
    	</style>
@endsection

@section('content')
<div style="height: 75px;line-height: 150px;">
	<div class="gouwuchediv">购物车</div>
</div>
		<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 14%;text-align: left;">
							<input class="allcheck" type="checkbox" name="" id="" value="" style="margin-left: 20%;" />
							<label style="margin-left: 15%;">全选</label>
						</th>
						<th style="width: 14%;"><span>品名</span></th>
						<th style="width: 12%;"><span>联系人</span></th>
						<th style="width: 8%;"><span>规格</span></th>
						<th style="width: 12%;"><span>到货时间</span></th>
						<th style="width: 13%;"><span>数量</span></th>
						<th><span style="">电话</span></th>
						<th style="width: "><span>操作</span></th>

						
					</tr>
					
					 @foreach($cart_list as $item)
					<tr>
					<td style="text-align: left;">
						<input class="onecheck" cart_id="{{$item['cart_id']}}" type="checkbox" name="" id="" value="" style="margin-left: 20%;"/>
						<img src="{{$item['product_thumb']}}" style="margin-left: 10%;"/>
					</td>
					<td>{{$item['product_name']}}</td>
					<td>{{$item['contact_person']}}</td>
					<td>{{$item['spec_name']}}</td>
					<td>{{$item['army_receive_date']}}</td>
					<td><input cart_id="{{$item['cart_id']}}"  class="product_num" type="text" name="" id="" value="{{$item['product_number']}}" readonly="readonly" />{{$item['spec_unit']}}</td>
					<td>{{$item['contact_tel']}}</td>
				<td><a onclick="cartdelete(this,'{{$item['cart_id']}}')">删除</a></td>
					</tr>
					 @endforeach
				</tbody>
			</table>
			<div style="height: 174px;background: #fbfbfb;width: 85%;">
					<div style="font-size: 16px;width: 200px;line-height: 70px;float: left;">
						<input style="margin-left: 20%;" class="allcheck" type="checkbox" name="" id="" value="" /><label style="margin-left: 30px;">全选</label><a class="more_delete" style="margin-left: 30px;">删除</a>
					</div>
					<div class="xiadan">下单</div>
					
				</div>
			
@endsection
@section('MyJs')
<script>
	
		/*删除商品*/
		function cartdelete(elm,cartid){
			 	if (confirm("确认删除吗？")){
    		 		$.ajax({
		    			type:"post",
		    			url:"{{url('cart/delete')}}",
		    			async:true,
		    			data:{
		    				cart_id:cartid,
		    				_token:'{{csrf_token()}}'
		    			},
		    			success:function(res){
		    				var resData=JSON.parse(res);
		    				if(resData.code==0){
		    					 layer.msg(resData.messages, {icon: 1, time: 1000},function(){
		    					 });
		    					 
			    				 setTimeout(function(){
			    				 	if(!resData.code){
			    					/*	$(elm).parent().parent().hide();*/
			    				location.reload()
			    					}
			    				 },1200)
		    				}else{
		    					 layer.msg(resData.messages, {icon: 2, time: 1000});
		    				}
		    			}
		    		});
    		 		
    		 	}
			
		}
	
	/*多选删除*/
			
	$(".more_delete").click(function(){
			var mydata="";
				var data="";
		$(".onecheck").each(function(i,index){
			if($(index).is(":checked")){
			 mydata += $(index).attr("cart_id")+",";
			 $(index).attr("delete","ture");
			}
		})
		 if(mydata.length>0){
		 	 data=mydata.substr(0,mydata.length-1);
		 }
	/*	console.log(data)*/
		$.ajax({
		    			type:"post",
		    			url:"{{url('cart/delete')}}",
		    			async:true,
		    			data:{
		    				cart_id:data,
		    				_token:'{{csrf_token()}}'
		    			},
		    			success:function(res){
		    				var resData=JSON.parse(res);
		    			//	console.log(resData);
		    				if(resData.code==0){
		    					 layer.msg(resData.messages, {icon: 1, time: 1000},function(){});
		    					 
			    				 setTimeout(function(){
			    				 	if(!resData.code){
			    						$(".onecheck[delete]").parent().parent().hide();
			    						$(".onecheck[delete]").removeAttr("checked");
			    					}
			    				 },1200)
		    				}else{
		    					 layer.msg(resData.messages, {icon: 2, time: 1000});
		    				}
		    			}
		    		});
	
	
	
	})
	
	$(".xiadan").click(function(){
		var xiadanData="";
		var xiadanData2="";
		$(".onecheck").each(function(i,index){
			if($(index).is(":checked")){
			 xiadanData += $(index).attr("cart_id")+",";
			 $(index).attr("delete","ture");
			}
		})
		 if(xiadanData.length>0){
		 	 xiadanData2=xiadanData.substr(0,xiadanData.length-1);
		 }
		if(xiadanData2==""){
			layer.msg("请选择下单商品", {icon: 2, time: 1000},function(){});
		}else{
			if({{$manage_user['identity']}} == '2'){
				  layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		       fixed :false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['965px' , '600px'],
		      content: '{{url('platform/need/view/release')}}'+"/"+xiadanData2
		    });
			}else{
				var xiadan= layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		      fixed :false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['965px' , '80%'],
		      content: '{{url('army/need/view/release')}}'+"/"+xiadanData2
		    });
			}
		
		}
	
	})
	
	
	
	
	
	
	
	/*改变商品数量*/
	$(".product_num").change(function(){
				$.ajax({
		    			type:"post",
		    			url:"{{url('cart/number')}}",
		    			async:true,
		    			data:{
		    				cart_id:$(this).attr("cart_id"),
		    				product_number:$(this).val(),
		    				_token:'{{csrf_token()}}'
		    			},
		    			success:function(res){
		    				var resData=JSON.parse(res);
		    				//console.log(resData);
		    				if(resData.code==0){
		    					 layer.msg(resData.messages, {icon: 1, time: 1000},function(){
		    					 });
		    					 
			    				 setTimeout(function(){
			    				 	if(!resData.code){
			    						$(".onecheck[delete]").parent().parent().hide();
			    						$(".onecheck[delete]").removeAttr("checked");
			    					}
			    				 },1200)
		    				}else{
		    					 layer.msg(resData.messages, {icon: 2, time: 1000});
		    				}
		    			}
		    		});
		
	})
	
	
	
	$(function(){
		$(".product_num").css("border","none");
		
		$(".product_num").css("background",$(".product_num").parent().css("background"));
		$(".product_num").dblclick(function(){
			$(this).removeAttr("readonly").css("border","1px solid #333333")
		})
		$(".product_num").blur(function(){
			$(this).attr("readonly","true");
			$(this).css("border","none")
		})
		
		$(".onecheck").each(function(){
			$(this).click(function(){
				if($(this).is(":checked")){
					var len=$(".onecheck").length;
					var num=0;
					$(".onecheck").each(function(){
						 if ($(this).is(':checked')) {
		                        num++;
		                  }
					});
					if (num == len) {
	                   $(".allcheck").prop("checked", true);
	                   
	                }
				}else{
					  $(".allcheck").prop("checked", false);
				}
				
			})
		})
	
			  $(".allcheck").click(function(){
				  if($(this).is(":checked")){
				  		$(".onecheck").prop("checked", true)
				  }else{
				  	$(".onecheck").prop("checked", false)
				  }
				 
			  	
			  })
			
		
	
	})
</script>
@endsection