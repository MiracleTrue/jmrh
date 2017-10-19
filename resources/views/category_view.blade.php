@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/addclassify.css')}}">
<style>
	.error{
		color: red;
	}
</style>
@endsection
@section('content')
   <div class="csy-box">
   	<form action="" method="post" id="categoryForm">
   		
   		
   
			<header>添加分类</header>
			<div class="error"></div>
			<div>
				<p>
					<span>名称</span>
				 	<input  type="" name="category_name" id="category_name" value="" />
				</p>
				<p>
					<span>数量单位</span>
				 	<input  type="" name="unit" id="unit" value="" />
				 	<a><img src="img/shizi.png" alt=""/></a>
				</p>
			</div>
			
			<div>
				<p>
					<span>排序</span>
				 	<input type="" name="sort" id="sort" value="" />
				</p>
				
				
			</div>
			
			
			
			<div class="csy-ope">
				<input type="submit" class="csy-submit" name="csy-submit" id="csy-submit" value="提交" />
				<input type="reset" class="csy-reset" name="csy-reset" id="csy-reset" value="重置" />

			</div>	
			</form>
		</div>
@endsection

@section('MyJs')
 
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery.validation/1.14.0/validate-methods.js')}}"></script>
  
  <script type="text/javascript">
  $().ready(function() 
    {
      /**
       * 添加用户表单验证与异步提交
       */     
     var validator = $("#categoryForm").validate({
        rules: {
          category_name: {
            required: true
          },
          unit: {
          	required:true,
          },
          sort:{
           required: true
          }
        },
         messages: {
	      category_name: "请输入名称",
	      unit:{
	      	required:"请输入数量单位"
	      	
	      },
	      sort: {
	        required: "请输入排序"
	      }
	    
	    },
	    errorLabelContainer:$("#categoryForm div.error"),
	     wrapper:"li",
	     
	     
	     {{-- submitHandler: function (form) {
          var index = parent.layer.getFrameIndex(window.name);
          $(form).ajaxSubmit({
            url: '{{action('Admin\IndexController@LoginSubmit')}}',
            type: 'POST',
            dataType: 'JSON',
            beforeSubmit: function () {
              if (!NetStatus) return false;
              NetStatus = false;
            },
            success: function (res) {
              if (res.code == 0) {
                NetStatus = true;
                window.location.replace("{{action('Admin\IndexController@Index')}}");
              }
              else {
                NetStatus = true;
                layer.msg(res.messages, {icon: 2, time: 1000});
              }
            }
          });
        }--}}

      });
      
     $(".adr-reset").on("click",function(){
     	validator.resetForm();
     }) 
      
      
    });
  </script>
@endsection