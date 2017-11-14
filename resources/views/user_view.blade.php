@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/adduser.css')}}">
<style>.error{
	color: red;
	
}
.error li{
	
}
</style>
@endsection
@section('content')
    <div class="adr-box">
    	@if(!empty($user_info))
    	<form id="UserEdit" action="" method="post">
    		@else
    	<form id="userAdd" action="" method="post">
    		@endif
    		{{csrf_field()}}
    		@if(!empty($user_info))
    			<header>修改用户</header>
			@else
    			<header>添加账户</header>
    		@endif
			<div class="error"></div>
			@if(!empty($user_info))
				<p><span>用户名</span><input type="text" name="user_name" id="user_name" value="{{$user_info['user_name'] or ''}}" placeholder="" disabled="disabled"/></p>
			@else
				<p><span>用户名</span><input type="text" name="user_name" id="user_name" value="{{$user_info['user_name'] or ''}}" placeholder="" /></p>
			@endif
			<p><span>手机</span><input type="text" name="phone" id="phone" value="{{$user_info['phone'] or ''}}" /></p>

			<p><span>姓名</span><input type="text" name="nick_name" id="nick_name" value="{{$user_info['nick_name'] or ''}}" /></p>

			<p><span>账户分类</span>
				
					@if(!empty($user_info))
					<select name="identity" disabled="disabled">
					<option value="1" @if($user_info['identity'] == '1') selected="selected" @endif >超级管理员</option>
                    <option value="2" @if($user_info['identity'] == '2') selected="selected" @endif>平台运营员</option>
                    <option value="3" @if($user_info['identity'] == '3') selected="selected" @endif>供货商</option>
                    <option value="4" @if($user_info['identity'] == '4') selected="selected" @endif>军方</option>
                    @else
                    <select name="identity">
                    <option value="1" >超级管理员</option>
                    <option value="2" >平台运营员</option>
                    <option value="3">供货商</option>
                    <option value="4" >军方</option>
                    @endif
				</select>
			</p>

			<p><span>密码</span><input type="password" name="password" id="password" value="" /></p>

			<p><span>确认密码</span><input type="password" name="password_confirmation" id="password_confirmation" value="" /></p>
			

		
			<div style="margin: 0 auto;">
				<input type="submit" class="adr-submit" name="adr-submit" id="" value="提交" />
				<input type="hidden" name="user_id" id="user_id" value="{{$user_info['user_id'] or 0}}" />
    			<input class="adr-reset" type="reset" value="重置" />

				
				
				
			</div>
		
		</form>
		</div>
@endsection

@section('MyJs')
 
  <script type="text/javascript" src="{{asset('webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
  <script type="text/javascript" src="{{asset('webStatic/library/jquery.validation/1.14.0/validate-methods.js')}}"></script>
  
  <!--http://malsup.github.io/jquery.form.js-->
  <script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>
  
  <script type="text/javascript">
  $().ready(function(){
  	
  		//修改用户
  	
  	/**
       * 添加用户表单验证与异步提交
       */     
		var validatorEd = $("#UserEdit").validate({
	        rules: {
	          nick_name: {
	            required: true
	          },
	          phone: {
	          	required:true,
	          	isMobile:true
	          },
	          user_name:{
	           required: true
	          },
	          password: {
	            minlength: 6
	          },
	          password_confirmation:{
	          	minlength: 6,
	          	equalTo:"#password"
	          }
	        },
	         messages: {
		      nick_name: "请输入用户名",
		      phone:{
		      	required:"请输入手机号",
		      	isMobile:"请输入正确的手机号"
		      },
		      user_name: {
		        required: "请输入姓名"
		      },
		      password: {
		        required: "请输入密码",
		        minlength: "密码长度不能小于 6 位"
		      },
		      password_confirmation: {
		        required: "请输入确认密码",
		        minlength: "密码长度不能小于 6 个字母",
		        equalTo: "两次密码输入不一致"
		      }
		    
		    },
		    errorLabelContainer:$("#UserEdit div.error"),
		    wrapper:"li",
		     
		     
		    submitHandler: function (form) {
		          $(form).ajaxSubmit({
		            url: '{{url("user/edit")}}',
		            type: 'POST',
		            data:{
		            	user_id:$('#user_id').val()
		            },
		            dataType: 'JSON',
		             beforeSend:function(res){
		            	if(!networkState){
		            		return false;
		            	}
		            	networkState=false;
		            },
		            success: function (res) {
		             console.log(res);
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
	  	
  		
  	
  	//用户添加
  		//用户名检测提交
  		$("#user_name").blur(function(){
  			if($("#user_name").val()!=""){
				$.ajax({
		  			url:"{{url('user/check/name')}}",
		  			async:true,
		  			type:'POST',
		  			data:{
		  				user_name:$("#user_name").val(),
		  				_token:'{{csrf_token()}}'
		  			},
		  			success:function(res){
		  				var resData=JSON.parse(res);
		  				/*console.log(resData.code);
		  				console.log($("#user_name").val());*/
			  			if(resData.code==1){
			  				$("#user_name").val("用户名已被占用!!!").css("color","red");
			  				$("#user_name").focus(function(){
			  					$("#user_name").val("");
			  				})
			  			}
		  					
		  				
		  				
		  				
		  			}
		  		});
  			}
  		
  	
  		})
     
      /**
       * 添加用户表单验证与异步提交
       */     
		var validatorAdd = $("#userAdd").validate({
        rules: {
          nick_name: {
            required: true
          },
          phone: {
          	required:true,
          	isMobile:true
          },
          user_name:{
           required: true
          },
          password: {
            required: true,
            minlength: 6
          },
          password_confirmation:{
          	required: true,
          	minlength: 6,
          	equalTo:"#password"
          }
        },
         messages: {
	      nick_name: "请输入用户名",
	      phone:{
	      	required:"请输入手机号",
	      	isMobile:"请输入正确的手机号"
	      },
	      user_name: {
	        required: "请输入姓名"
	      },
	      password: {
	        required: "请输入密码",
	        minlength: "密码长度不能小于 6 位"
	      },
	      password_confirmation: {
	        required: "请输入确认密码",
	        minlength: "密码长度不能小于 6 个字母",
	        equalTo: "两次密码输入不一致"
	      }
	    
	    },
	    errorLabelContainer:$("#userAdd div.error"),
	    wrapper:"li",
	     
	     
	    submitHandler: function (form) {
	          $(form).ajaxSubmit({
	            url: '{{url("user/add")}}',
	            type: 'POST',
	            dataType: 'JSON',
	             beforeSend:function(res){
		            	if(!networkState){
		            		return false;
		            	}
		            	networkState=false;
		        },
	            success: function (res) {
	             console.log(res);
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
      	$(".adr-reset").on("click",function(){
     	   validatorAdd.resetForm();
     	
        });
      }else{
      	$(".adr-reset").on("click",function(){
     	   validatorEd.resetForm();
     	
        });
      }
    
     
//   $("#userAdd").validate({
////              rules:{
////                  role_name:"required",
////                  is_super_management_group : "required"
////              },
//              onkeyup:false,
//              focusCleanup:true,
//              success:"valid",
//              submitHandler:function(form){
////                  var index = parent.layer.getFrameIndex(window.name);
//
//					alert(1);
//                  $(form).ajaxSubmit({
//                      url: '111',
//                      type: 'POST',
//                      dataType: 'JSON',
//                      success:function(res){
//                          if(res.code == 0)
//                          {
//                              layer.msg(res.messages,{icon:1,time:1000},function()
//                              {
//                                  NetStatus = true;
//                                  parent.location.replace(parent.location.href);
//                                  parent.layer.close(index);
//                              });
//                          }
//                          else
//                          {
//                              NetStatus = true;
//                              layer.msg(res.messages,{icon:2,time:1000});
//                          }
//                      }
//                  });
//                  //parent.$('.btn-refresh').click();
//                  //parent.layer.close(index);
//              }
//          });
      
      
    });
  </script>
@endsection