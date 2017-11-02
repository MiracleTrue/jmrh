@extends('layouts.master')
@section('MyCss')
   <link rel="stylesheet" href="{{asset('webStatic/css/quote.css')}}">
   	<style type="text/css">
   			.error{
    			color: red;
    		}
    		#qte-submit{
				background: #fe8d01;
			    color: #FFFFFF;
			    margin-left: 45px;
			    width: 200px;
			    height: 64px;
			    font-size: 19px;
			    font-weight: bolder;
			    line-height: 64px;
			    text-align: center;
			    display: inline-block;
			}
			#qte-reset{
				background: #EEEEEE;
			    color: #000000;
			    margin-left: 45px;
			    width: 200px;
			    height: 64px;
			    font-size: 19px;
			    font-weight: bolder;
			    line-height: 64px;
			    text-align: center;
			    display: inline-block;
			}
			
   	</style>
@endsection
@section('content')
<div class="qte-box">
	<form  id="platformbaojiatijiao" method="post">		
			<header>客户报价(剩余时间<span>23小时</span><span>52分</span><span>05秒</span>)</header>
			<div class="offer_div">
				@foreach($order_info['offer_info'] as $item)
				<p>
					<span>{{$item['user_info']['nick_name']}}</span>
					 
				 	<input class="price_color" data-price="{{$item['total_price']}}" disabled="disabled class="blueWord" type="" name="" id="" value=@if($item['status']=="0") "待报价" @elseif($item['status']=="1")"单价{{$item['price']}}元/{{$order_info['product_unit']}}  总价{{$item['total_price']}}元" @endif  />
				 	
				</p>
				@endforeach
			</div>

			<div >
				<p style="text-indent: 49px;">
					<span>品名</span>
				 	<input type="" name="" id="" value="{{$order_info['product_name']}}" disabled="disabled"/>
				</p>
				<p style="margin-left: 38px;">
					<span>最终选择</span>
						
				 	<select name="offer_id">
				 		@foreach($order_info['offer_info'] as $item)
				 		
				 		<option class="op_price" data-price="{{$item['total_price']}}"  value="{{$item['offer_id']}}">{{$item['user_info']['nick_name']}}</option>
				 		
				 		@endforeach
				 	</select>
				</p>
			</div>
			
			<div class="qte-ope">
				<input type="hidden" name="order_id" value="{{$order_info['order_id']}}" />
				
				<a class="qte-submit" type="submit" name="" id="qte-submit" value="提交" >提交</a>
				<input class="qte-reset" type="reset" name="" id="qte-reset" value="重置" />
				
				
			</div>
			</form>
		</div>
		
		
@endsection
@section('MyJs')
<script type="text/javascript" src="{{asset('/webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
   <script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>
<script>
	$(function(){
		
	
	
		var arr =new Array(3);
		for(var i=0;i<$(".price_color").length;i++ ){
			arr[i]=$(".price_color").eq(i).attr("data-price");
		}
		function sortNumber(a, b)
		{
		return a - b
		}
		console.log(arr.sort(sortNumber));
		
			var small=arr.sort(sortNumber)[0];
			var midd=arr.sort(sortNumber)[1];
			var mast=arr.sort(sortNumber)[2];
			console.log(small);
			for(var i=0;i<$(".price_color").length;i++ ){
				if($(".price_color").eq(i).attr("data-price")==0){
						$(".price_color").eq(i).css("color","#dddddd");
				}
				else if($(".price_color").eq(i).attr("data-price")==small && $(".price_color").eq(i).attr("data-price")!=0){
					
				$(".price_color").eq(i).css("color","green");
				
				}
				else if($(".price_color").eq(i).attr("data-price")==midd){
					$(".price_color").eq(i).css("color","blue")
				}else{
					$(".price_color").eq(i).css("color","red")
				}
			}
			
//			console.log($(".price_color").eq(i).attr("data-price") !=0)
//			console.log($(".price_color").eq(i).attr("data-price") !=0)

			var c = small == 0 ? midd == 0 ? mast == 0 ? 0 : mast : midd : small;		
			for(var i=0;i<$(".op_price").length;i++ ){
				if($(".op_price").eq(i).attr("data-price")==c && $(".price_color").eq(i).attr("data-price") !=0) {
					$(".op_price").eq(i).attr("selected","selected");
				
				}

				
			}
			$('.qte-submit').on("click",function(){
				
				   $("#platformbaojiatijiao").ajaxSubmit({
		            url: '{{url("platform/selected/offer")}}',
		            type: 'POST',
		            dataType: 'JSON',
		            data:{
		            	_token:'{{csrf_token()}}'
		            },
		            success: function (res) {
		            	console.log(res)
		            if(res.code==0){
		             	   layer.msg(res.messages, {icon: 1, time: 1000},function(){
		             	   	  parent.location.reload();
		             	   });
		             	
			        var index=parent.layer.getFrameIndex(window.name);
					setTimeout(function(){
						parent.layer.close(index);
		             	layer.closeAll('')
					},1200)
						
		             }else{
		             	   layer.msg(res.messages, {icon: 2, time: 1000});
		             }
		            }
		          });
	     
				
			})
		  
		
	})
	
</script>
@endsection