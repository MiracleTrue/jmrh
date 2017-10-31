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
  	@if(!empty($product_info))
			<header>修改商品</header>
			@else
			<header>添加商品</header>
			@endif
			
				@if(!empty($product_info))
		<form id="productEdit" action="" method="post">
			@else
		<form id="productAdd" action="" method="post">
			@endif
			
				<div class="error"></div>
				<div class="productAdd_div1">
					<p>
						<span>所属分类</span>
						  
						<select name="category_id">
							@if(!empty($product_info))
								@foreach($category_list as $item)
								 <option value="{{$item['category_id']}}" @if($product_info['category_id'] == $item['category_id']) selected="selected" @endif >{{$item['category_name']}}</option>
								@endforeach
							@else
								@foreach($category_list as $item)
								 <option value="{{$item['category_id']}}">{{$item['category_name']}}</option>
								@endforeach
							@endif
						</select>
						 
					</p>
					<p style="position: relative;">
						<span>商品图片</span>
						<input style="z-index: 1; filter: alpha(opacity=0);opacity: 0" type="file"  accept="image/*" name="product_image" id="product_image" value="" />
						<label for="product_image"><input  style="background-color: #FFFFFF;border: 1px solid #A9A9A9;position: absolute;top: 13px;left: 55px;" disabled="disabled" id="faker" value="点击右边上传按钮" /></lable>
						<a><label for="product_image"><img style="cursor: pointer;" src="{{asset('webStatic/images/shizi.png')}}" alt="浏览按钮" /></label></a>
					</p>
					
					

				</div>
	
				<div class="productAdd_div1">
					<p>
						<span>商品名称</span>
						<input type="" name="product_name" id="product_name" value="{{$product_info['product_name'] or ''}}" />
					</p>
					<p style="padding-right: 49px;">
						<span >排序</span>
						<input type="text" name="sort" id="sort" value="{{$product_info['sort'] or ''}}" />
					</p>
				</div>
				<script id="container" name="product_content" type="text/plain">
         	   		
        </script>
				<div class="csy-ope" >
					<input class="csy-submit" type="submit" name="" id="" value="提交" />
					<input class="csy-reset" type="reset" name="" id="" value="重置" />
				</div>
				
					@if(!empty($product_info))
				<input type="hidden" name="product_id" id="product_id" value="{{$product_info['product_id']}}" />
				<input type="hidden" name="category_id" id="category_id" value="{{$product_info['category_id']}}" />
				<input type="hidden" name="product_content1" id="product_content1" value="{{$product_info['product_content']}}" />
				@endif
			</form>
		</div>
	
@endsection
@section('MyJs')
<script type="text/javascript" src="{{asset('webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
<script type="text/javascript" src="{{asset('webStatic/library/jquery.validation/1.14.0/validate-methods.js')}}"></script>
<script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>
<script src="{{asset('webStatic/library/ueditor/1.4.3/ueditor.config.js')}}" type="text/javascript" charset="utf-8"></script>
<script src="{{asset('webStatic/library/ueditor/1.4.3/ueditor.all.min.js')}}" type="text/javascript" charset="utf-8"></script>
<script>
	//获取上传图片名称
	var imgName;
	$("#product_image").change(function(){
		var imgName=$(this).val();
		var arr=imgName.split('\\');
		var my=arr[arr.length-1]
		$("#faker").val(my)
	})
	
	
	var editor = UE.getEditor('container',{serverUrl: "{{env('APP_URL').'/phpPlugins/ueditor/controller.php'}}"});
	$("#container").text($("#product_content1").val())
	  //添加商品表单验证
	  	var validatorAdd = $("#productAdd").validate({
	        rules: {
	          product_name: {
	            required: true
	          },
	          product_image: {
	          	required:true
	          	
	          },
	          product_content:{
	          	 required: true
	          },
	            sort:{
	           required: true,
	           isIntGtZero:true
	          }
	        },
	         messages: {
		      product_name: "请输入商品名称",
		      product_image:{
		      	required:"请上传图片"
		      	
		      },
		      product_content:{
		      	required:"请输入内容"
		      },
		      sort:{
		      	required: "请输入排序",
	        	isIntGtZero:"请输入大于0的整数"
		      }
		    },
		    errorLabelContainer:$(".error"),
		    wrapper:"li",		     
		    submitHandler: function (form) {
		          $(form).ajaxSubmit({
		            url: '{{url("product/add")}}',
		            type: 'POST',
		            dataType: 'JSON',
		            data:{
		            	_token:'{{csrf_token()}}'
		            },
		            success: function (res) {
		             console.log(res);
		             if(res.code==0){
		             	   layer.msg(res.messages, {icon: 2, time: 1000},function(){
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
	      
	      //修改商品表单验证
	      var validatorEd = $("#productEdit").validate({
	        rules: {
	          product_name: {
	            required: true
	          },
	            sort:{
	           required: true,
	           isIntGtZero:true
	          }
	        },
	         messages: {
		      product_name: "请输入商品名称",
		      sort:{
		      	required: "请输入排序",
	        	isIntGtZero:"请输入大于0的整数"
		      }
		    },
		    errorLabelContainer:$(".error"),
		    wrapper:"li",		     
		    submitHandler: function (form) {
		          $(form).ajaxSubmit({
		            url: '{{url("product/edit")}}',
		            type: 'POST',
		            dataType: 'JSON',
		            data:{
		            	_token:'{{csrf_token()}}'
		            },
		            success: function (res) {
		             console.log(res);
		             if(res.code==0){
		             	   layer.msg(res.messages, {icon: 2, time: 1000},function(){
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
      	$(".csy-reset").on("click",function(){
     	   validatorAdd.resetForm();
        });
      }else{
      	$(".csy-reset").on("click",function(){
     	   validatorEd.resetForm();
     	
        });
      }

</script>


@endsection