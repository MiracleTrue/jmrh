@extends('layouts.master')

@section('MyCss')
    <link rel="stylesheet" href="{{asset('webStatic/css/login.css')}}">
    	<style>
    		.error{
    			color: red;
    		}
    	</style>
@endsection
@section('content')
<div class="login-box">
			<div class="loginkuang">
				<div id="loginerror">
					<ul>
						
					</ul>
				</div>
				<form id="formlogin" action="" method="post">			
					<div class="username">
						<input type="text" name="user_name" id="user_name" value="" placeholder="用户名" />
					</div>
					<div class="possword">
						<input type="password" name="password" id="password" value="" placeholder="密码" />
					</div>
					<div class="remeword">
						<input type="checkbox" name="remember_password" id="remember_password" value="记住密码" />
						<label for="remember_password" style="font-size: 12px;">记住密码</label>
					</div>
					<input class="loginbtn" type="submit" name="" id="" value="登陆" />
				</form>
			
			
			</div>
		</div>
@endsection

@section('MyJs')
  <script type="text/javascript" src="{{asset('webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
  <script type="text/javascript" src="{{asset('webStatic/library/jquery.validation/1.14.0/validate-methods.js')}}"></script>
  <script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript">
  	
  	$(document).ready(function() {
				var iheight = $(window).height();
				$(".login-box").height(iheight)
      
      
        //记住密码
      
       //获取cookie的值
       $(".loginbtn").on("click",function(){
       	 if($('#remember_password').is(':checked')) {
		    localStorage.setItem('username', $("#user_name").val());
		    localStorage.setItem('password',  $("#password").val());
		}else{
		    localStorage.setItem('username', "");
		    localStorage.setItem('password', "");
		}
       })
		
		$("#user_name").val(localStorage.getItem('username'));
		$("#password").val(localStorage.getItem('password'));
		if(localStorage.getItem('username')==""){
		    $("#remember_password").attr("checked",false)
		}else{
		    $("#remember_password").attr("checked",true)
		}
		
		
		
      /**
       * 登录表单验证与异步提交
       */
      $("#formlogin").validate({
        rules: {
          user_name: {
            required: true,
            minlength: 4,
            maxlength: 16
          },
          password: {
            required: true,
            minlength: 6
          }
        },
        messages: {
	      user_name:{
	      	required:"请输入用户名",
	      	minlength:"用户名不能少于4位",
	      	maxlength:"用户名不能多于16位"
	      },
	      password: {
	        required: "请输入密码",
	        minlength: "密码长度不能小于 6 位"
	      }
	    },
        errorLabelContainer:$("#loginerror ul"),
	    wrapper:"li",
	    submitHandler: function (form) {
	    	
          $(form).ajaxSubmit({
            url: '{{url("login/submit")}}',
            dataType: 'JSON',
             type: 'POST',
             data:{_token:'{{csrf_token()}}'},
            success: function (res) {
            	console.log(res);
              if (res.code == 0) {
                window.location.replace("{{url('/')}}");
              }
              else {
                layer.msg(res.messages, {icon: 2, time: 1000});
              }
            }
          });
          
        }
       
      });
      
    
      
      
      
      
      


})
  
  </script>
@endsection