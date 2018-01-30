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
@endsection
@section('content')
<div class="qte-box">
	<form  id="platformbaojiatijiao" method="post">	
		
			<header>订单确认<span class="header_span">(剩余时间<span class="pf_day"></span><span class="pf_hour"></span><span class="pl-min"></span><span class="pl_sc"></span>)</span></header>
			
		 @foreach($offer_list as $val)
		 
		 		 
			
			<div class="offer_div">
			
				<p>
					<span>时间</span>
					<input type="" name="" id="" value="{{$val[0]['create_date']}}" disabled="disabled"/>				 	
				</p>
				
				@foreach($val as $item)
				<p style="position:relative;">
					<span>{{$item['user_info']['nick_name']}}</span>
				 	<input type="" name="" id="" value="单价{{$item['price']}}元/{{$order_info['spec_unit']}} {{$item['product_number']}}{{$order_info['spec_unit']}}" disabled="disabled"/>
					<span style="position: absolute;right: 20px;top: 3px;color: blue;">{{$item['status_text']}}</span>
				</p>
				@endforeach
				
				
			</div>

			<div class="plat_div2">
				<p>
					<span>品名</span>
				 	<input type="" name="" id="" value="{{$order_info['product_name']}}" disabled="disabled"/>
				</p>
				<p>
					<span>总需求量</span>
				 	<input type="" name="" id="" value="{{$order_info['product_number']}}{{$order_info['spec_unit']}}" disabled="disabled"/>
						
				 	
				</p>
			</div>
			<div class="plat_div2">
				<p>
					<span>库存供应</span>
				 	<input type="" name="" id="" value="{{$order_info['platform_allocation_number']}}{{$order_info['spec_unit']}}" disabled="disabled"/>
				</p>
				
			</div>
			   <hr style="margin-top: 20px;margin-bottom: 20px;" />
			   			   @endforeach
		
				<div class="qte-ope">
					<input type="hidden" name="order_id" value="" />
					
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
	
<!--倒计时-->
	var qteSub=true;
var EndTimeMsg = {{$count_down}};
	
	 	if(EndTimeMsg>0){
			    function show() {
			     EndTimeMsg--;
			    h = parseInt(EndTimeMsg%(24*3600)/3600);
			 
			   
			    	day=parseInt((EndTimeMsg)/(24*3600));
			    	  $(".pf_day").text(day+"天");
			      
			    m = parseInt((EndTimeMsg)%3600/60);
			    s = parseInt((EndTimeMsg)%60);
			  
			    $(".pf_hour").text(h+"小时");
			    $(".pl-min").text(m+"分钟");
			    $(".pl_sc").text(s+"秒");
			    	if(s<0){
		
					$(".header_span").css("color","red").text('(确认时间已过)');
				}
			  }
			  setInterval("show()", 1000)	
			  
		}else{
			$(".header_span").css("color","red").text('(确认时间已过)');
		<!-- qteSub=false;-->
		}
		var order_id={{$order_info['order_id']}};
		if("{{$button}}"=="等待"){
			$(".qte-ope").hide();
		}else if("{{$button}}"=="重新分配"){
			 layer.open({
			      type: 2,
			      title: false,
			      maxmin: false,
			       fixed :false,
			      shadeClose: true, //点击遮罩关闭层
			      area : ['900px' , '600px'],
			      content: '{{url('platform/re/allocation/view')}}'+'/'+{{$order_info['order_id']}}
		    });
		}else{
			$('.qte-submit').on("click",function(){
				 $("#platformbaojiatijiao").ajaxSubmit({
			            url: '{{url("platform/order/confirm")}}',
			            type: 'POST',
			            dataType: 'JSON',
			            data:{			            	
			            	_token:'{{csrf_token()}}',
			            	order_id:'{{$order_info['order_id']}}'
			            },
			              beforeSend:function(res){
			            	$("input[type='submit']").attr("disabled","true");
			            	
			            },
			            success: function (res) {
			            	<!--console.log(res)-->
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
			
		}
		
		
		
		
		
				
				  
	     
				
		
		
		
	 
</script>
@endsection