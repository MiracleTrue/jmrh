@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/passwd.css')}}">
<style>
	.error{
		color: red;
	}
</style>
@endsection
@section('content')
  <div class="pass-box">
			<div>修改密码</div>
			<div class="error"></div>
			<form id="passwordEd" action="" method="post">
				<p><span>原密码</span><input type="password" name="original_password" id="original_password" value="" /></p>
	
				<p><span>新密码</span><input type="password" name="password" id="password" value="" /></p>
	
				<p><span>确认密码</span><input type="password" name="password_confirmation" id="password_confirmation" value="" /></p>
				<div style="margin: 0 auto;">
				
				<input type="submit" class="pass-submit" name="" id="" value="提交" />
				
				<input style="width: 210px;" type="reset" class="pass-reset"  name="" id="" value="重置" />
			</div>
		
		</form>
		
		
		</div>
	
@endsection
@section('MyJs')

 <script type="text/javascript" src="{{asset('webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
  <script type="text/javascript" src="{{asset('webStatic/library/jquery.validation/1.14.0/validate-methods.js')}}"></script>
  <script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>
  

<script type="text/javascript">
  $().ready(function(){
  	//修改密码
       
       
		var validatorEd = $("#passwordEd").validate({
	        rules: {
	          original_password:{
	            required: true
	          },
	          password: {
	          	required:true,
	          	 minlength: 6
	          },
	          password_confirmation:{
	           required: true,
	           equalTo:"#password"

	          }	      
	        },
	         messages: {
		      original_password: "请输入原始密码",
		      password:{
		      	required:"请输入新密码",
		      	minlength:"密码不得少于6位"
		      },
		      password_confirmation: {
		        required: "请输入确认密码",
		        equalTo:"两次密码不相同"
		      },
		    },
		    errorLabelContainer:$(".error"),
		    wrapper:"li",
		        
	    submitHandler: function (form) {
	          $(form).ajaxSubmit({
	            url: '{{url("password/original/edit")}}',
	            type: 'POST',
	            dataType: 'JSON',
	            data:{
    				_token:'{{csrf_token()}}'
	            },
	            success: function (res) {
	             console.log(res);
	             if(res.code==0){
	             	
	             	 layer.msg(res.messages, {icon: 2, time: 1000});
	             	
		        var index=parent.layer.getFrameIndex(window.name);
				setTimeout(function(){
					parent.layer.close(index);
		             	layer.closeAll('');
		             	
				},1000);
				
				       parent.location.replace("{{url('/')}}");        

				
					
	             }else{
     	                layer.msg(res.messages, {icon: 2, time: 1000});

	             }
	            }
	          });
        }

		     
		     
	
	      });
	  	
      	$(".pass-reset").on("click",function(){
     	   validatorEd.resetForm();
     	
        });
    
    
     

      
      
    });
  </script>

@endsection