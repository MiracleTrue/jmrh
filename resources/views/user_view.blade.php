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
    	<form id="userAdd" action="" method="post">
    		{{csrf_field()}}
			<header>添加账户</header>
			<div class="error"></div>
			<p><span>用户名</span><input type="text" name="user_name" id="user_name" value="" placeholder=""/></p>

			<p><span>手机</span><input type="text" name="phone" id="phone" value="" /></p>

			<p><span>姓名</span><input type="text" name="nick_name" id="nick_name" value="" /></p>

			<p><span>账户分类</span>
				<select name="identity">
					<option value="2">平台运营员 </option>
					<option value="3">供货商</option>
					<option value="4">军方</option>
				</select>
			</p>

			<p><span>密码</span><input type="password" name="password" id="password" value="" /></p>

			<p><span>确认密码</span><input type="password" name="password_confirmation" id="password_confirmation" value="" /></p>
			

		
			<div style="margin: 0 auto;">
				<input type="submit" class="adr-submit" name="adr-submit" id="" value="提交" />
				
				<input class="adr-reset" type="reset" name="adr-reset" id="" value="重置" />
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
  		//用户名检测提交
  		$("#user_name").blur(function(){
  			if($("#user_name").val()!=""){
				$.ajax({
		  			url:"{{url('user/check/name')}}",
		  			async:true,
		  			data:{
		  				user_name:$("#user_name").val()
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
		var validator = $("#userAdd").validate({
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
	            success: function (res) {
	             console.log(res);
	             if(res.code==0){
	             	alert("用户添加成功");
	             	
		        var index=parent.layer.getFrameIndex(window.name);
				
				parent.layer.close(index);
	             	layer.closeAll('')
	             }
	            }
	          });
        }

      });
      
     $(".adr-reset").on("click",function(){
     	validator.resetForm();
     });
     
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