@extends('layouts.master')
@section('MyCss')
    <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/library/editable-select/jquery.editable-select.min.css')}}">

<style type="text/css">

#ary-submit{
	background: #fe8d01;
    color: #FFFFFF;
    margin-left: 45px;
    width: 210px;
    height: 64px;
    font-size: 19px;
    font-weight: bolder;
    line-height: 64px;
    text-align: center;
    display: inline-block;
}
#ary-reset{
	background: #EEEEEE;
    color: #000000;
    margin-left: 45px;
    width: 210px;
    height: 64px;
    font-size: 19px;
    font-weight: bolder;
    line-height: 64px;
    text-align: center;
    display: inline-block;
}
.ary-ope{
	width: 514px;
	margin:57px auto 0 auto;
	
}
.error{
	color: red;
}
#product_number{
	position: relative;
	    height: 43px;
    width: 305px;
    outline: 0;
    margin-left: 18px;
}
#product_unit{
	height: 30px;
    width: 60px;
    outline: 0;
    position: absolute;
    top: 9px;
    right: 6px;
}
input{
border:1px solid #ccc ;	
}
</style>
@endsection
@section('content')
   <!--添加需求-->
   @if(!empty($order_info))
    	<form id="form_armyEdit" action="" method="post">
    		@else
    	<form id="form_army" action="" method="post">
    		@endif
<form id="form_army" action="" method="post">
	
	<div class="error">
		
	</div>

			<div class="mly-more">
				@if(!empty($order_info))
    			<div class="arymy_div1">修改需求</div>
			@else
    		<div class="arymy_div1">添加需求</div>
    		@endif
				
		
				<div class="">

					<p><span>品名</span><input type="text" name="product_name" id="product_name" value="{{$order_info['product_name'] or ''}}" /></p>
					<p><span>到货时间</span><input  onClick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss',min: laydate.now()})" class="laydate-icon"  name="army_receive_time" id="army_receive_time" value="{{$order_info['army_receive_time'] or ''}}" placeholder="请选择日期"/></p>
				</div>
				<div class="">
					<p><span>数量</span><input type="text" name="product_number" id="product_number" value="{{$order_info['product_number'] or ''}}" />
						<select id="product_unit" name="product_unit">
							<!--@foreach($unit_list as $item)
								 <option value="{{$item}}" >{{$item}}</option>
								@endforeach-->
								@if(!empty($order_info))
								@foreach($unit_list as $item)
								 <option value="{{$item}}" @if($item == $order_info['product_unit']) selected="selected" @endif >{{$item}}</option>
								@endforeach
							@else
								@foreach($unit_list as $item)
								 <option value="{{$item}}" >{{$item}}</option>
								@endforeach
							@endif
						</select>
					
					</p>
				</div>
				<input type="hidden" name="order_id" id="order_id" value="{{$order_info['order_id'] or ''}}" />
			<div class="ary-ope" style="">
				<input type="submit" class="ary-submit" name="ary-submit" id="ary-submit" value="提交" />
				<input type="reset" class="ary-reset" name="ary-reset" id="ary-reset" value="重置" />
			</div>	
			</div>
</form>
@endsection

@section('MyJs')
 
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery.validation/1.14.0/validate-methods.js')}}"></script>
   <script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery-calendar/js/laydate.js')}}"></script>
    <script type="text/javascript" src="{{asset('/webStatic/library/editable-select/jquery.editable-select.min.js')}}"></script>


  <script type="text/javascript">
  	
  	!function(){

	laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库

	laydate({elem: '#army_receive_time'});//绑定元素

}();
$('#product_unit').editableSelect({
	effects: 'slide'
});
$(".es-input").attr("placeholder","请选择单位");
$(".es-input").val("{{$order_info['product_unit'] or ''}}");

  $().ready(function() 
    {	
      /**
       * 军方添加需求
       */     
     var validatorAdd = $("#form_army").validate({
        rules: {
          product_name: {
            required: true
          },
          army_receive_time: {
          	required:true,
          },
          product_number:{
           required: true,
           isIntGtZero:true
          },
          product_unit:{
          	required: true
          }
        },
         messages: {
	      product_name: "请输入品名",
	      army_receive_time:{
	      	required:"请选择到货时间"
	      	
	      },
	      product_number: {
	        required: "请输入数量",
	        isIntGtZero:"请输入大于0的整数"
	      },
	      product_unit:{
	      	 required: "请输入计量单位"
	      }
	    
	    },
	    errorLabelContainer:$("div.error"),
	     wrapper:"li",
	        submitHandler: function (form) {
		          $(form).ajaxSubmit({
		            url: '{{url("army/need/release")}}',
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
      
        /**
       * 军方修改需求
       */     
     var validatorEd = $("#form_armyEdit").validate({
        rules: {
          product_name: {
            required: true
          },
          army_receive_time: {
          	required:true,
          },
          product_number:{
           required: true,
           isIntGtZero:true
          },
          product_unit:{
          	required: true
          }
        },
         messages: {
	      product_name: "请输入品名",
	      army_receive_time:{
	      	required:"请选择到货时间"
	      	
	      },
	      product_number: {
	        required: "请输入数量",
	        isIntGtZero:"请输入大于0的整数"
	      },
	      product_unit:{
	      	 required: "请输入计量单位"
	      }
	    
	    },
	    errorLabelContainer:$("div.error"),
	     wrapper:"li",
	        submitHandler: function (form) {
		          $(form).ajaxSubmit({
		            url: '{{url("army/need/edit")}}',
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
      
      
      
   
      
      
      
   if(validatorAdd){
      	$(".ary-reset").on("click",function(){
     	   validatorAdd.resetForm();
     	
        });
      }else{
      	$(".ary-reset").on("click",function(){
     	   validatorEd.resetForm();
     	
        });
      }
      
    });
  </script>
@endsection


