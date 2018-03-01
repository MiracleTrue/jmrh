@extends('layouts.master')
@section('MyCss')
    <link rel="stylesheet" href="{{asset('webStatic/css/client.css')}}">
<style>
	.error{
    			color: red;
    			padding-left: 20px;
    		}
    		#clt-submit{
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
			#clt-reset{
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
			.clt-box .input_value  input{
				width: 114px;
			    height: 30px;
			    outline: none;
			    margin-left: 18px;
			    line-height: 40px;
			}
</style> 
@endsection
@section('content')
<div class="error"></div>
<div class="clt-box">
	<form id="platallpost" action="" method="post">
			<header>客户二次分配</header>
			<div>
				<span>库存剩余</span>
				<span>{{$repertory_info['number']}}{{$order_info['spec_unit']}}</span>
					
				
				<span class="shijian" style="margin-left: 20px;">分配数量</span>
				<input type="number" class="fenpeinumber shijian" name="platform_allocation_number" id="" value="{{$order_info['platform_allocation_number']}}" style="width: 110px;height: 30px;" min="0"/>
			</div>
			
			<div class="input_value">
				<p>
					<span>选择供应商1</span>
				 	<select name="supplier_A_id">
				 		<option value="0" priceData="0">不选择供应商</option>
				 		 @foreach($supplier_list as $item)
				 		<option value="{{$item['user_id']}}" priceData="{{$item['price_info']['price']}}">{{$item['nick_name']}}</option>
				 		@endforeach
				 	</select>
				 	<span style="margin-left: 20px;">协议价</span>
				 	<input class="xieyijia"  type="text" name="" id="" value="" readonly="readonly"/>
					<span style="margin-left: 20px;">分配数量</span><input name="supplier_A_number" class="supplier_A_number" type="number" value="" />
				</p>
				
					
				
			</div>
			<div class="input_value">
				<p>
					<span>选择供应商2</span>
				 	<select name="supplier_B_id">
				 		<option value="0" priceData="0">不选择供应商</option>
				 		 @foreach($supplier_list as $item)
				 		<option value="{{$item['user_id']}}" priceData="{{$item['price_info']['price']}}">{{$item['nick_name']}}</option>
				 		@endforeach
				 	</select>
				 	<span style="margin-left: 20px;">协议价</span><input class="xieyijia" type="text" name="" id="" value="" readonly="readonly" />
					<span style="margin-left: 20px;">分配数量</span><input name="supplier_B_number" class="supplier_B_number" type="number" value="" />
				</p>
			</div>
			<div class="input_value">
				<p>
					<span>选择供应商3</span>
				 	<select name="supplier_C_id">
				 		<option value="0" priceData="0">不选择供应商</option>
				 		 @foreach($supplier_list as $item)
				 		<option value="{{$item['user_id']}}" priceData="{{$item['price_info']['price']}}">{{$item['nick_name']}}</option>
				 		@endforeach
				 	</select>
				 	<span style="margin-left: 20px;">协议价</span><input class="xieyijia" type="text" name="" id="" value="" readonly="readonly"/>
					<span style="margin-left: 20px;">分配数量</span><input name="supplier_C_number" class="supplier_C_number" type="number" value="" />
				</p>
				
					
				
			</div>
			
			
			
			<div >
				<p style="text-indent: 52px;">
					<span>品名</span>
				 	<input type="" name="" id="" value="{{$order_info['product_name']}}" disabled="" placeholder=""/>
				</p>
					<p>
					<span>需求量</span>
				 	<input type=""class="xuqiuliang" name="" id="" value="{{$need_number}}{{$order_info['spec_unit']}}" disabled="" placeholder=""/>
				</p>
				
					
					
				
			</div>
			<div class="shijian">
				<p style="text-indent: 20px;">
					<span>到货时间</span>
					@if($order_info['type'] =='2')
				 	<input  style="width: 267px;" autocomplete="off" type="" name="platform_receive_time" id="platform_receive_time1" value="" class="laydate-icon" placeholder="请选择时间"/>
					@else
					<input  style="width: 267px;" autocomplete="off" type="" name="platform_receive_time" id="platform_receive_time2" value="" class="laydate-icon" placeholder="请选择时间"/>
					@endif				</p>
			<p>
					<span>接单时间</span>
				 	<select name="" class="confirm_time" >
				 		<option value="0">请选择时间 </option>
				 		<option value="1800">0.5小时</option>
				 		<option value="3600">1小时</option>
				 		<option value="5400">1.5小时</option>
				 		<option value="7200">2小时</option>
				 		<option value="9000">2.5小时</option>
				 		<option value="10800">3小时</option>

				 	</select>
				</p>
				
			</div>
			<div class="shijian">
				<p style="">
					<span>到货预警时间</span>
					<select style="margin-left: 10px;width: 289px;" name="warning_time">
				 		<option value="0">请选择时间 </option>
				 		<option value="3600">1小时</option>
				 		<option value="7200">2小时</option>
				 		<option value="10800">3小时</option>
				 		<option value="14400">4小时</option>
				 		<option value="18000">5小时</option>
				 		<option value="21600">6小时</option>
				 		<option value="25200">7小时</option>
				 		<option value="28800">8小时</option>
				 		<option value="32400">9小时</option>
				 		<option value="36000">10小时</option>
				 		<option value="39600">11小时</option>
				 		<option value="43200">12小时</option>

				 	</select>
				</p>
			</div>
			<input type="hidden" name="order_id" id="order_id" value="{{$order_info['order_id']}}" />
			<div class="clt-ope">
				
				<input class="clt-submit" type="submit" name="" id="clt-submit" value="提交" />
				<input class="clt-reset" type="reset" name="" id="clt-reset" value="重置" />

			</div>
			</form>
		</div>
@endsection
@section('MyJs')
 <script type="text/javascript" src="{{asset('/webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery.validation/1.14.0/validate-methods.js')}}"></script>
   <script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery-calendar/js/laydate.js')}}"></script>
  <script type="text/javascript">
  	!function(){

	laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库

	

}();

/*时间戳互相转换*/
		function datetime_to_unix(datetime){
			    var tmp_datetime = datetime.replace(/:/g,'-');
			    tmp_datetime = tmp_datetime.replace(/ /g,'-');
			    var arr = tmp_datetime.split("-");
			    var now = new Date(Date.UTC(arr[0],arr[1]-1,arr[2],arr[3]-8,arr[4],arr[5]));
			    return parseInt(now.getTime()/1000);
			}
			 
			function unix_to_datetime(unix) {
			    var now = new Date(parseInt(unix) * 1000);
			    return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
			}


	$("#platform_receive_time2").click(function(){
		 var timestamp = Date.parse(new Date())/1000;
					//console.log(timestamp)
					var mydate;
					var that=$(this)
			laydate({
				format: 'YYYY-MM-DD hh:mm:ss',
				istime: true, 
				min: laydate.now(0, 'YYYY-MM-DD 00:00:00'),
				max:'{{\Carbon\Carbon::createFromFormat("Y-m-d H:i:s",$order_info['army_receive_date'])->subSecond()->toDateTimeString()}}',
				choose: function(datas){
					 mydate=datetime_to_unix(datas);
					if(mydate<=timestamp){
						//console.log('false')
						that.val('');
						layer.msg("选择的时间请大于现在时间",{icon: 2,time: 1200})
					}
				    }
			})
	})
$("#platform_receive_time1").click(function(){
		 var timestamp = Date.parse(new Date())/1000;
					//console.log(timestamp)
					var mydate;
					var that=$(this)
			laydate({
				format: 'YYYY-MM-DD hh:mm:ss',
				istime: true, 
				min: laydate.now(0, 'YYYY-MM-DD 00:00:00'),
				choose: function(datas){
					 mydate=datetime_to_unix(datas);
					if(mydate<=timestamp){
						//console.log('false')
						that.val('');
						layer.msg("选择的时间请大于现在时间",{icon: 2,time: 1200})
					}
				    }
			})
	})		
		








$(".input_value select").on("click",function(){
	$(this).siblings(".xieyijia").val($(this).find('option:selected').attr("pricedata")+"/"+"{{$order_info['spec_unit']}}");
		
	/*$(".input_value select option").each(function(i,index){
		if($(index).is(":selected")){
			$(index).attr("datastus","ture").siblings().attr("datastus","false");
		}else{
			
		}
		
	})		*/	

})
	/*var date = new Date(1398250549490);
	Y = date.getFullYear() + '-';
	M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
	D = date.getDate() + ' ';
	h = date.getHours() + ':';
	m = date.getMinutes() + ':';
	s = date.getSeconds(); */
	/*console.log(Y+M+D+h+m+s);*/
	var timestamp;
	var date;
	var confirm_time;
$(".confirm_time").change(function(){
	timestamp = Date.parse(new Date());
	
	 date=new Date(timestamp+($(".confirm_time option:selected").val()*1000));
	
	 Y = date.getFullYear() + '-';
		M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
		D = date.getDate() + ' ';
		h = date.getHours() + ':';
		m = date.getMinutes() + ':';
		s = date.getSeconds(); 
	/*	console.log(Y+M+D+h+m+s); */
		confirm_time=Y+M+D+h+m+s;
	/*	console.log(confirm_time)*/
})

 /**
       * 分配客户
       */     
     var validatorAdd = $("#platallpost").validate({
        rules: {
          warning_time: {
            required: true
          },
          platform_receive_time: {
          	required:true,
          },
          confirm_time2:{
          	  required: true
          }
        },
         messages: {
	      warning_time: "请选择预警时间",
	      platform_receive_time:{
	      	required:"请选择到货时间"
	      	
	      },
	      confirm_time2:{
	      	required:"请选择接单时间"
	      }
	    },
	    errorLabelContainer:$("div.error"),
	     wrapper:"li",
	        submitHandler: function (form) {
	        	supplier_A_number=$(".supplier_A_number").val();
	        	supplier_B_number=$(".supplier_B_number").val();
	        	supplier_C_number=$(".supplier_C_number").val();
	        	fenpeinumber=$(".fenpeinumber").val();
	        	xueqiunumber={{$need_number}};
	        	if(xueqiunumber!=Number(supplier_A_number)+Number(supplier_C_number)+Number(fenpeinumber)+Number(supplier_B_number)){
	        		 layer.msg("分配数量不正确", {icon: 2, time: 1000});
	        	}else{
	        		if($(".confirm_time option:selected").val()!=0){
	        			
	        		
	        			$(form).ajaxSubmit({
				            url: '{{url("platform/re/allocation/offer")}}',
				            type: 'POST',
				            dataType: 'JSON',
				            data:{
				            	confirm_time:confirm_time,
				            	_token:'{{csrf_token()}}'
				            },
				            beforeSend:function(res){
				            	$("input[type='submit']").attr("disabled","true");
				            	
				            },
				            success: function (res) {
				            	/*console.log(res)*/
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
		       	}else{
		       		 layer.msg("请选择接单时间", {icon: 2, time: 1000});
		       	}
	        	}
	        	
		         
		         
	        }
	
	     

      });
   
    
      $(".clt-reset").on("click",function(){
     	   validatorAdd.resetForm();
     	
        });


  </script>
@endsection
