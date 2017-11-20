@extends('layouts.master')
@section('MyCss')
    <link rel="stylesheet" href="{{asset('webStatic/css/addcompile.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/library/editable-select/jquery.editable-select.min.css')}}">

    	<style type="text/css">
    		.error{
    			color: red;
    			
    		}
    		.error li{
    			margin-left: 20px;
    		}
    		#ade-submit{
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
			#ade-reset{
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
			#product_unit{
				height: 30px;
			    width: 60px;
			    outline: 0;
			    position: absolute;
			    top: 15px;
			    right: 6px;
			}
			.es-input{
				border:1px solid #ddd ;	

			}
    	</style>
@endsection
@section('content')
<div class="error"></div>
<div class="ade-box">
	<form id="platform" action="" method="post" >
		
		
	
			<header>添加采购需求</header>
			<div>
				<p>
					<span>品名</span>
				 	<input type="" name="product_name" id="product_name" value="" />
				</p>
				<p>
					<span>到货时间</span>
				 	<input autocomplete="off" name="platform_receive_time" id="platform_receive_time" class="laydate-icon"   value="{{$order_info['army_receive_time'] or ''}}" placeholder="请选择日期(必须大于现在时间)"/>
				</p>
			</div>
			
			<div>
				<p style="position: relative;">
					<span>数量</span>
				 	<input type="" name="product_number" id="product_number" value="" />
				 	<select id="product_unit" name="product_unit" class="product_unit ade-num">
				 		 @foreach($unit_list as $item)
				 		<option value="{{$item}}">{{$item}}</option>
				 		@endforeach
				 	</select>
				</p>
				<p>
					<span>确认时间</span>
				 	<input autocomplete="off" name="confirm_time" id="confirm_time"  class="laydate-icon"  value="{{$order_info['army_receive_time'] or ''}}" placeholder="请选择日期"/>
				</p>
				
			</div>
			
			<div class="ade-select">
				<p style="position: relative;">
					<span>选择供应商1</span>
				 	<select name="supplier_A">
				 		<option value="0">无</option>
				 		 @foreach($supplier_list as $item)
				 		<option value="{{$item['user_id']}}">{{$item['nick_name']}}</option>
				 		@endforeach
				 	</select>
				</p>
				<p>
					<span>选择供应商2</span>
				 	<select name="supplier_B">
				 		<option value="0">无</option>
				 		 @foreach($supplier_list as $item)
				 		<option value="{{$item['user_id']}}">{{$item['nick_name']}}</option>
				 		@endforeach
				 	</select>
				</p>
			</div>
			<div class="ade-select">
				<p style="position: relative;">
					<span>选择供应商3</span>
				 	<select name="supplier_C">
				 		<option value="0">无</option>
				 		 @foreach($supplier_list as $item)
				 		<option value="{{$item['user_id']}}">{{$item['nick_name']}}</option>
				 		@endforeach
				 	</select>
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
			<div class="ade-ope">
				
				<input class="ade-submit" type="submit" name="" id="ade-submit" value="提交" />
				<input class="ade-reset" type="reset" name="" id="ade-reset" value="重置" />

				
			</div>
			</form>
		</div>
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

	

}();
	
	/*确认时间到货时间限制*/
	var confirmTime={
		elem: '#confirm_time',
		format: 'YYYY-MM-DD hh:mm:ss',
		istime: true,
		min: laydate.now(),
		choose: function(datas){
	        receiveTime.max = datas; //开始日选好后，重置结束日的最小日期
	        receiveTime.start = datas //将结束日的初始值设定为开始日
	    }
};
/*到货时间*/
var receiveTime={
	elem: '#platform_receive_time',
	format: 'YYYY-MM-DD hh:mm:ss',
	istime: true,
	min: laydate.now(),
	choose: function(datas){

        confirmTime.max = datas; //结束日选好后，充值开始日的最大日期
		
		$("#confirm_time").focus(function(){
			laydate(confirmTime);
		})
    }
};
laydate(receiveTime);
$("#confirm_time").click(function(){
	if($("#platform_receive_time").val()==""){
		//alert("请先选择到货时间");
		  layer.msg("请先选择到货时间",{icon: 2, time: 1000})
		
	}
});

$('#product_unit').editableSelect({
	effects: 'slide'
});
$(".es-input").attr("placeholder","请选择单位");

  $().ready(function(){	
      /**
       * 军方添加需求
       */     
     var validatorAdd = $("#platform").validate({
        rules: {
          product_name: {
            required: true
          },
          platform_receive_time: {
          	required:true,
          },
          confirm_time:{
          		required:true
          },
          product_number:{
           required: true,
           isIntGtZero:true
          },
          product_unit:{
          	required: true,
          },
         
        },
         messages: {
	      product_name: "请输入品名",
	      platform_receive_time:{
	      	required:"请选择到货时间"
	      	
	      },
	      confirm_time:{
	      	required:"请选择确认时间"
	      },
	      product_number: {
	        required: "请输入数量",
	        isIntGtZero:"请输入大于0的整数"
	      },
	        product_unit:{
	        	required: "请选择单位",
	        }
	    },
	    errorLabelContainer:$("div.error"),
	     wrapper:"li",
	        submitHandler: function (form) {
	        	var loading = null;
	        	if($("select[name='supplier_A']").val()=="0" && $("select[name='supplier_B']").val()=="0" &&$("select[name='supplier_C']").val()=="0" ){
		            	 layer.msg("请先选择一个供应商",{icon: 2, time: 1000})

		         }else{
		         	$(form).ajaxSubmit({
		            url: '{{url("platform/need/release")}}',
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
      
      
      
   
      
      
      
   if(validatorAdd){
      	$(".ade-reset").on("click",function(){
     	   validatorAdd.resetForm();
     	
        });
      }else{
      	$(".ade-reset").on("click",function(){
     	   validatorEd.resetForm();
     	
        });
      }
      
    });
  </script>
@endsection