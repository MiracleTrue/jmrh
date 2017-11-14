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
</style>
@endsection
@section('content')
<div class="error"></div>
<div class="clt-box">
	<form id="platallpost" action="" method="post">
		
		
	
			<header>客户分配</header>
			<div>
				<p>
					<span>选择供应商1</span>
				 	<select name="supplier_A">
				 		<option value="0">不选择供应商</option>
				 		 @foreach($supplier_list as $item)
				 		<option value="{{$item['user_id']}}">{{$item['nick_name']}}</option>
				 		@endforeach
				 	</select>
				</p>
				<p>
					<span>选择供应商2</span>
				 	<select name="supplier_B">
				 		<option value="0">不选择供应商</option>
				 		 @foreach($supplier_list as $item)
				 		<option value="{{$item['user_id']}}">{{$item['nick_name']}}</option>
				 		@endforeach
				 	</select>
				</p>
			</div>
			
			<div>
				<p>
					<span>选择供应商3</span>
				 	<select name="supplier_C">
				 		<option value="0">不选择供应商</option>
				 		 @foreach($supplier_list as $item)
				 		<option value="{{$item['user_id']}}">{{$item['nick_name']}}</option>
				 		@endforeach
				 	</select>
				</p>
				
				<p>
					<span>确认时间</span>
				 	<input onClick="laydate({elem: '#confirm_time',istime: true, format: 'YYYY-MM-DD hh:mm:ss',min: laydate.now()})" placeholder="请选择时间" type="" name="confirm_time" id="confirm_time" value="" />
				</p>
			</div>
			
			<div >
				<p style="text-indent: 52px;">
					<span>品名</span>
				 	<input type="" name="" id="" value="{{$order_info['product_name']}}" disabled="" placeholder=""/>
				</p>
				<p>
					<span>需求量</span>
				 	<input type="" name="" id="" value="{{$order_info['product_number']}}{{$order_info['product_unit']}}" disabled="" placeholder=""/>
				</p>
			</div>
			<div>
				<p style="text-indent: 20px;">
					<span>到货时间</span>
				 	<input onClick="laydate({elem: '#platform_receive_time',istime: true, format: 'YYYY-MM-DD hh:mm:ss',max:'{{\Carbon\Carbon::createFromFormat("Y-m-d H:i:s",$order_info['army_receive_time'])->subSecond()->toDateTimeString()}}' })" type="" name="platform_receive_time" id="platform_receive_time" value="" placeholder="请选择时间"/>
				</p>
				<p style="position: relative;">
					<span>到货预警时间</span>
				 	<select name="warning_time">
				 		<option value="0">无预警</option>
				 		<option value="14400">4小时</option>
				 		<option value="28800">8小时</option>
				 		<option value="43200">12小时</option>
				 		<option value="86400">24小时</option>
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
           confirm_time: {
          	required:true,
          },
          order_id:{
           required: true,
           isIntGtZero:true
          },
         
        },
         messages: {
	      warning_time: "请选择预警时间",
	      platform_receive_time:{
	      	required:"请选择到货时间"
	      	
	      },
	       confirm_time:{
	      	required:"请选择确认时间"
	      	
	      },
	      order_id: {
	        required: "请输入数量",
	        isIntGtZero:"请输入大于0的整数"
	      }
	   
	    },
	    errorLabelContainer:$("div.error"),
	     wrapper:"li",
	        submitHandler: function (form) {
		          $(form).ajaxSubmit({
		            url: '{{url("platform/allocation/offer")}}',
		            type: 'POST',
		            dataType: 'JSON',
		            data:{
		            	_token:'{{csrf_token()}}'
		            },
		             beforeSend:function(res){
		            	if(!networkState){
		            		return false;
		            	}
		            	networkState=false;
		            },
		            success: function (res) {
		            if(res.code==0){
		             	   layer.msg(res.messages, {icon: 1, time: 1000},function(){
		             	   		networkState=true;
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
	        }
	
	     

      });
      
      $(".clt-reset").on("click",function(){
     	   validatorAdd.resetForm();
     	
        });

  </script>
@endsection
