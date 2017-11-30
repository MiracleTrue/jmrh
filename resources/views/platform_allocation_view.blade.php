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
				 	<input  style="width: 282px;" autocomplete="off" type="" name="platform_receive_time" id="platform_receive_time" value="" class="laydate-icon" placeholder="请选择时间"/>
				</p>
				<p>
					<span>确认时间</span>
				 	<input style="width: 282px;" autocomplete="off"  placeholder="请选择时间" type="" name="confirm_time" id="confirm_time" class="laydate-icon" value="" />
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

/*确认时间到货时间限制*/
/*var confirmTime={
	elem: '#confirm_time',
	format:'YYYY-MM-DD hh:mm:ss',
	 istime: true,
	max:'{{\Carbon\Carbon::createFromFormat("Y-m-d H:i:s",$order_info['army_receive_time'])->subSecond()->toDateTimeString()}}',
	choose: function(datas){
        receiveTime.max = datas; //开始日选好后，重置结束日的最小日期
        receiveTime.start = datas //将结束日的初始值设定为开始日
		
    }
};*/
/*到货时间*/
/*var receiveTime={
	elem: '#platform_receive_time',
	format:  'YYYY-MM-DD hh:mm:ss',
	 istime: true,
	max:'{{\Carbon\Carbon::createFromFormat("Y-m-d H:i:s",$order_info['army_receive_time'])->subSecond()->toDateTimeString()}}',
	choose: function(datas){

        confirmTime.max = datas; //结束日选好后，充值开始日的最大日期
		
		$("#confirm_time").focus(function(){
			laydate(confirmTime);
		})
    }
}


laydate(receiveTime);
$("#platform_receive_time").click(function(){
	$("#confirm_time").val("");
	
})
$("#confirm_time").click(function(){
	if($("#platform_receive_time").val()==""){
		//alert("请先选择到货时间");
		  layer.msg("请先选择到货时间",{icon: 2, time: 1000})
		
	}
})*/
var timeType={{$order_info['type']}};

/*console.log(timeType)*/
if(timeType==2){
		$("#platform_receive_time").click(function(){
			 dateCompare=false;
		if($("#confirm_time").val()==""){
			laydate({
					elem: '#platform_receive_time',
					format: 'YYYY-MM-DD hh:mm:ss',
					istime: true,
					min: laydate.now()
					
				
				});
		
		}else{
			laydate({
					elem: '#platform_receive_time',
					format: 'YYYY-MM-DD hh:mm:ss',
					istime: true,
					min: laydate.now(),
					
				
				})
			$("#confirm_time").val("")
			
		}
		
})


	$("#confirm_time").click(function(){
		 dateCompare=false;
		if($("#platform_receive_time").val()==""){
			//alert("请先选择到货时间");
			  layer.msg("请先选择到货时间",{icon: 2, time: 1000})
			
		}else{
			var mymaxtime=$("#platform_receive_time").val().substring(0,10);
			
			/*console.log(mymaxtime)*/
			
			laydate({
					elem: '#confirm_time',
					format: 'YYYY-MM-DD hh:mm:ss',
					istime: true,
					min: laydate.now(),
					max: mymaxtime
					
			});
		}
	});
}else if(timeType==1){
	$("#platform_receive_time").click(function(){
		 dateCompare=false;
		if($("#confirm_time").val()==""){
			laydate({
					elem: '#platform_receive_time',
					format: 'YYYY-MM-DD hh:mm:ss',
					istime: true,
					min: laydate.now(),
					max:'{{\Carbon\Carbon::createFromFormat("Y-m-d H:i:s",$order_info['army_receive_time'])->subSecond()->toDateTimeString()}}'
					
				
				});
		
		}else{
			laydate({
					elem: '#platform_receive_time',
					format: 'YYYY-MM-DD hh:mm:ss',
					istime: true,
					min: laydate.now(),
					max:'{{\Carbon\Carbon::createFromFormat("Y-m-d H:i:s",$order_info['army_receive_time'])->subSecond()->toDateTimeString()}}'
					
				
				})
			$("#confirm_time").val("")
			
		}
		
})


	$("#confirm_time").click(function(){
		 dateCompare=false;
		if($("#platform_receive_time").val()==""){
			//alert("请先选择到货时间");
			  layer.msg("请先选择到货时间",{icon: 2, time: 1000})
			
		}else{
			var mymaxtime=$("#platform_receive_time").val().substring(0,10);
			
			/*console.log(mymaxtime)*/
			
			laydate({
					elem: '#confirm_time',
					format: 'YYYY-MM-DD hh:mm:ss',
					istime: true,
					min: laydate.now(),
					max: mymaxtime
					
			});
		}
	});
}

	$("#clt-submit").click(function(){
			    var reg = /^\s*|\s*$/g;
			    var t1 = document.getElementById("platform_receive_time").value.replace(reg, "");
			    var t2 = document.getElementById("confirm_time").value.replace(reg, "");
			    reg = /^(\d+)\-(\d+)\-(\d+)\s+(\d+)\:(\d+)\:(\d*)$/;
			   /* if (!reg.test(t1) || !reg.test(t2)) {
			         throw new Error("Date Format is Error !");
			         return;
			    }*/
			    var d1 = new Date(t1.replace(reg, "$1"), parseInt(t1.replace(reg, "$2")) - 1, t1.replace(reg, "$3"));
			    d1.setHours(t1.replace(reg, "$4"), t1.replace(reg, "$5"), t1.replace(reg, "$6"));
			    var d2 = new Date(t2.replace(reg, "$1"), parseInt(t2.replace(reg, "$2")) - 1, t2.replace(reg, "$3"));
			    d2.setHours(t2.replace(reg, "$4"), t2.replace(reg, "$5"), t2.replace(reg, "$6"));
			    if (d1 > d2) {
			       /* alert("true");*/
			        dateCompare=true;
			    }
			
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
	        	if($("select[name='supplier_A']").val()=="0" && $("select[name='supplier_B']").val()=="0" &&$("select[name='supplier_C']").val()=="0" ){
		            	 layer.msg("请先选择一个供应商",{icon: 2, time: 1300})

		        }else if(dateCompare==false){
					$("#confirm_time").val("");
		         	 layer.msg("确认时间应该小于到货时间",{icon: 2, time: 1300})
		         }else{
		         	$(form).ajaxSubmit({
			            url: '{{url("platform/allocation/offer")}}',
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
      
      $(".clt-reset").on("click",function(){
     	   validatorAdd.resetForm();
     	
        });

  </script>
@endsection
