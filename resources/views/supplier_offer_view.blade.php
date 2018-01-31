@extends('layouts.master')
@section('MyCss')
    <link rel="stylesheet" href="{{asset('webStatic/css/adduser.css')}}">
    	<style type="text/css">
    		.adr-submit{
				background: #fe8d01;
			    color: #FFFFFF;
			    margin-left: 55px;
			    width: 200px;
			    height: 64px;
			    font-size: 19px;
			    font-weight: bolder;
			    line-height: 64px;
			    text-align: center;
			    display: inline-block;
			}
			.adr-reset{
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
			.error{
				color: red;
			}
    	</style>
@endsection
@section('content')
<div class="adr-box">
	<form id="supp_form" action="" method="post">
		
		<div class="error"></div>

			<header>编辑报价</header>
			<p>
				<span>品名</span>
				<input type="text" name="" id="" value="{{$offer_info['order_info']['product_name']}}" disabled="disabled"/>
			</p>

			<p>
				<span>到货时间</span>
				<input type="text" name="" id="" value="{{$offer_info['platform_receive_date']}}" disabled="disabled"/>
			</p>

			<p style="position: relative;">
				<span>单价</span>
				<!--<input class="price" type="number" name="price" id="price" value="" onkeyup="test(this.value)"  />-->
				<input class="price" type="number" name="price" id="price" value="{{$offer_info['price']}}" disabled="disabled" />
				<span class="adr-money">元/{{$offer_info['order_info']['spec_unit']}}</span>
			</p>

			<p style="position: relative;">
				<span>数量</span>
				<input class="product_number" type="text" name="" id="text" value="{{$offer_info['order_info']['product_number']}}" disabled="disabled" />
				<span class="adr-money">{{$offer_info['order_info']['spec_unit']}}</span>
			</p>

			<p style="position: relative;">
				<span>总价</span>
				<input class="total_pride" type="number" name="total_price" id="total_price" value="" disabled="disabled" />
				<span class="adr-money">元</span>
			</p>

			<p>
				<span>供应商</span>
				<input disabled="disabled" type="text" name="" id="" value="{{$offer_info['user_info']['nick_name']}}" />
			</p>
			<p>
				<span>拒绝理由</span>
				<input  class="deny_reason" type="text" name="" id="" value="" placeholder="若需拒绝请填写拒绝理由，不拒绝直接点击同意即可" />
			</p>

		<input type="hidden" name="offer_id" id="offer_id" value="{{$offer_info['offer_id']}}" />
		<div style="margin: 0 auto;">
			
			<input type="submit" class="adr-submit" name="" id="" value="同意" />
			<input offer_id="{{$offer_info['offer_id']}}" onclick="OfferDeny(this,{{$offer_info['offer_id']}})"  type="text" class="adr-reset" name="" id="" value="拒绝" readonly="readonly" style="border: none;"/>

		</div>
		
		
	</form>
</div>
@endsection
@section('MyJs')
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery.validation/1.14.0/validate-methods.js')}}"></script>
  <script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>
<script>
	
	function OfferDeny(elm,offer_id){
		var deny_reason=$(".deny_reason").val();
		      $.ajax({
				            url: '{{url("supplier/offer/deny")}}',
				            type: 'POST',
				            dataType: 'JSON',
				            data:{
				            	offer_id:offer_id,
				            	deny_reason:deny_reason,
				            	_token:'{{csrf_token()}}'
				            },
				            beforeSend:function(res){
				            	$("input[type='submit']").attr("disabled","true");
				            	
				            },
				            success: function (res) {
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
	}
	
	/*  $.fn.watch = function (callback) {
                return this.each(function () {
                    //缓存以前的值  
                    $.data(this, 'originVal', $(this).val());

                    //event  
                    $(this).on('keyup paste click', function () {
                        var originVal = $.data(this, 'originVal');
                        var currentVal = $(this).val();

                        if (originVal !== currentVal) {
                            $.data(this, 'originVal', $(this).val());
                            callback(currentVal);
                        }
                    });
                    
                });
            }
         
            	 $(".price").watch(function(value) { 
     
	            	var val=Number(value*$(".product_number").val());
				$(".total_pride").val(Math.floor(val * 100) / 100)
			});*/
				var value=$(".price").val();
        		var val=Number(value*$(".product_number").val());
				$(".total_pride").val(Math.floor(val * 100) / 100)
            
		
		
		//单价输入保留4位小数
		/*function test(str){
		    var pos;
		    var fst
		    var lst;
		    if (str == "") return;
		    pos = str.indexOf(".");
		    if (pos != -1){
		        fst = str.substring(0,pos);
		        lst = str.substring(pos+1,pos.length);
		        if (lst.length > 4){             
		             var sub = lst.substring(0,4);
		          document.getElementById("price").value=fst+"."+sub;
		        }
		    }    
		}*/
			
		
		
		
		
		
		  var validatorEd = $("#supp_form").validate({
			        submitHandler: function (form) {
				          $(form).ajaxSubmit({
				            url: '{{url("supplier/offer/submit")}}',
				            type: 'POST',
				            dataType: 'JSON',
				            data:{
				            	_token:'{{csrf_token()}}'
				            },
				            beforeSend:function(res){
				            	$("input[type='submit']").attr("disabled","true");
				            	
				            },
				            success: function (res) {
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
			        }
			
	     

      });
      
    	$(".adr-reset").on("click",function(){
     		
     	
        });  
		
		
		
</script>
@endsection