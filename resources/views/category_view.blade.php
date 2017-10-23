@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/addclassify.css')}}">
<style>
	.error{
		color: red;
	}
	.csy-box div p input{
		color: #000000;
	}
</style>
@endsection
@section('content')
   <div class="csy-box">
   	 	@if(!empty($category_info))
    		<form action="" method="post" id="CategoryEdit">
			@else
    			<form action="" method="post" id="categoryForm">
    		@endif  	  	  	 	
  		   	{{csrf_field()}}
   		   	
   		   	
   		   	@if(!empty($category_info))
    			<header>编辑分类</header>
			@else
    			<header>添加分类</header>
    		@endif
   		  
			<div class="error"></div>
			<div>
				<p>
					<span>名称</span>
				 	<input  type="" name="category_name" id="category_name" value="{{$category_info['category_name'] or ''}}" />
				</p>
				<p>
					<span>数量单位</span>
				 	<input  type="" name="unit" id="unit" value="{{$category_info['unit'] or ''}}" />
				 	
				</p>
			</div>
			
			<div>
				<p>
					<span>排序</span>
				 	<input type="" name="sort" id="sort" value="{{$category_info['sort'] or ''}}" />
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
   <script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>

  <script type="text/javascript">
  $().ready(function() 
    {
      /**
       * 添加用户表单验证与异步提交
       */     
     var validatorAdd = $("#categoryForm").validate({
        rules: {
          category_name: {
            required: true
          },
          unit: {
          	required:true,
          },
          sort:{
           required: true,
           isIntGtZero:true
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
	        submitHandler: function (form) {
		          $(form).ajaxSubmit({
		            url: '{{url("category/add")}}',
		            type: 'POST',
		            dataType: 'JSON',
		            success: function (res) {
		             console.log(res);
		             if(res.code==0){
		             	alert("分类添加成功");
		             	
			        var index=parent.layer.getFrameIndex(window.name);
					
					parent.layer.close(index);
		             	layer.closeAll('')
		             }
		            }
		          });
	        }
	
	     

      });
      
      
      
      //编辑用户
      
        var validatorEd = $("#CategoryEdit").validate({
        rules: {
          category_name: {
            required: true
          },
          unit: {
          	required:true,
          },
          sort:{
           required: true,
           isIntGtZero:true
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
	        submitHandler: function (form) {
		          $(form).ajaxSubmit({
		            url: '{{url("category/edit")}}',
		            type: 'POST',
		            dataType: 'JSON',
		            success: function (res) {
		             console.log(res);
		             if(res.code==0){
		             	alert("分类编辑成功");
		             	
			        var index=parent.layer.getFrameIndex(window.name);
					
					parent.layer.close(index);
		             	layer.closeAll('')
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
      
    });
  </script>
@endsection