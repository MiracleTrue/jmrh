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
			.qte-box p{
				width: 390px;
				text-align: right;
			}
   	</style>
   	<!--[if IE]> <style>
   		.qte-box{
   			width:920px;
   		}
   		.plat_div2{
   			margin-top:25px;
   			overflow:hidden;
   			width:900px;
   		}
   		.offer_div p{
   			margin-top:20px;
   		}
   		.offer_div p:first-child+p{
   			margin-left:20px;
   		}
   		.qte-ope{
   		margin-left:18%;
   			display:block;
   		}
   	</style> <![endif]-->
@endsection
@section('content')
<div class="qte-box">
	<form  id="platformbaojiatijiao" method="post">	
			@if($order_info['status']==100)	
			<header>客户报价<span class="header_span">(剩余时间<span class="pf_day"></span><span class="pf_hour"></span><span class="pl-min"></span><span class="pl_sc"></span>)</span></header>
			@elseif($order_info['status']==110)	
				<header>客户报价</header>
				@endif
			<div class="offer_div">
				@foreach($order_info['offer_info'] as $item)
				<p>
					<span>{{$item['user_info']['nick_name']}}</span>
					 
				 	<input class="price_color" data-price="{{$item['total_price']}}" disabled="disabled class="blueWord" type="" name="" id="" value=@if($item['status']=="0") "待报价" @elseif($item['status']=="1" || $item['status']=="3")"单价{{$item['price']}}元/{{$order_info['product_unit']}}  总价{{$item['total_price']}}元" @elseif($item['status']=="2")"(未通过)单价{{$item['price']}}元/{{$order_info['product_unit']}}  总价{{$item['total_price']}}元"  @elseif($item['status']=="-1")"已过期" @endif  />
				 	
				</p>
				@endforeach
			</div>

			<div class="plat_div2">
				<p>
					<span>品名</span>
				 	<input type="" name="" id="" value="{{$order_info['product_name']}}" disabled="disabled"/>
				</p>
				<p>
					<span>最终选择</span>
						
				 	<select name="offer_id" @if($order_info['status']!=100) disabled="disabled" @endif>
				 		@foreach($order_info['offer_info'] as $item)
				 		
				 		<option class="op_price" data-price="{{$item['total_price']}}"  value="{{$item['offer_id']}}">{{$item['user_info']['nick_name']}}</option>
				 		
				 		@endforeach
				 	</select>
				</p>
			</div>
			@if($order_info['status']==100)
			
				<div class="qte-ope">
					<input type="hidden" name="order_id" value="{{$order_info['order_id']}}" />
					
					<a class="qte-submit" type="submit" name="" id="qte-submit" value="提交" >提交</a>
					<input class="qte-reset" type="reset" name="" id="qte-reset" value="重置" />
					
					
				</div>
				
			@endif
			</form>
		</div>
		
		
@endsection
@section('MyJs')
<script type="text/javascript" src="{{asset('/webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
   <script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>
<script>
	
	<!--倒计时-->
	var qteSub=true;
		var EndTimeMsg = {{$count_down}};
	 	if(EndTimeMsg>0){
			    function show() {
			     EndTimeMsg--;
			    h = Math.floor(EndTimeMsg / 60 / 60);
			    if(h>24){
			    	var day=h%24;
			    	  $(".pf_day").text(day+"天");
			    }
			    m = Math.floor((EndTimeMsg - h * 60 * 60) / 60);
			    s = Math.floor((EndTimeMsg - h * 60 * 60 - m * 60));
			  
			    $(".pf_hour").text(h+"小时");
			    $(".pl-min").text(m+"分钟");
			    $(".pl_sc").text(s+"秒");
			    
			  }
			  setInterval("show()", 1000)	
		}else{
			$(".header_span").css("color","red").text('(确认时间已过)');
		 qteSub=false;
		}
	
	
	
	$(function(){
		{{--$(".offer_div p").eq(1).css("margin-left","20px"); --}}
		$('.qte-ope').hide();
		
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
			
			for(var i=0;i<$(".price_color").length;i++ ){
				if($(".price_color").eq(i).attr("data-price")!="0.00"){
					$('.qte-ope').show();
				}
			
			}
			if(qteSub==false){
					console.log(qteSub)
					$('.qte-ope').hide();
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
		              beforeSend:function(res){
		            	$("input[type='submit']").attr("disabled","true");
		            	
		            },
		            success: function (res) {
		            	console.log(res)
		            if(res.code==0){
		             layer.msg(res.messages, {icon: 1, time: 1000},function(){  
		             	   parent.location.reload();	 
		             	   	  layer.closeAll('');
		             	   });
						
		             }else{
		             	   layer.msg(res.messages, {icon: 2, time: 1000},function(){
		             	   $("input[type='submit']").removeAttr("disabled");
		             	   });
		             }
		            }
		          });
	     
				
			})
	
	
		
	})
		
		
	<!--倒计时-->	
	   {{-- function formatSeconds(value) {
            var theTime = parseInt(value);// 秒
            var theTime1 = 0;// 分
            var theTime2 = 0;// 小时
            // alert(theTime);
            if(theTime > 60) {
                theTime1 = parseInt(theTime/60);
                theTime = parseInt(theTime%60);
                // alert(theTime1+"-"+theTime);
                if(theTime1 > 60) {
                    theTime2 = parseInt(theTime1/60);
                    theTime1 = parseInt(theTime1%60);
                }
            }
            var result = ""+parseInt(theTime)+"秒";
            if(theTime1 > 0) {
                result = ""+parseInt(theTime1)+"分"+result;
            }
            if(theTime2 > 0) {
                result = ""+parseInt(theTime2)+"小时"+result;
            }
         console.log(result)
            return result;
        }
 
    setInterval('formatSeconds({{$count_down}})',1000);
	--}} 
	
	
	
	
	
	
	
	 
</script>
@endsection