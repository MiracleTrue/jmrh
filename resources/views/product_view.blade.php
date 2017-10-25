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
			<header>添加分类</header>
			<form id="productAdd" action="" method="post">
				<div class="error"></div>
				<div class="productAdd_div1">
					<p>
						<span>所属分类</span>
						  
						<select name="category_id">
							@foreach($category_list as $item)
							 <option value="{{$item['category_id']}}">{{$item['category_name']}}</option>
							@endforeach
						</select>
						 
					</p>
					<p style="position: relative;">
						<span>商品图片</span>
						<input style="z-index: 1; filter: alpha(opacity=0);opacity: 0" type="file"  accept="image/*" name="product_image" id="product_image" value="" />
						<label for="product_image"><input  style="background-color: #FFFFFF;border: 1px solid #A9A9A9;position: absolute;top: 13px;left: 55px;" disabled="disabled" id="" value="请选择图片" /></lable>
						<a><label for="product_image"><img style="cursor: pointer;" src="../../public/webStatic/images/shizi.png" alt="浏览按钮" /></label></a>
					</p>
					<input type="hidden" name="sort" id="sort" value="{{$item['sort']}}" />
					

				</div>
	
				<div class="productAdd_div1">
					<p>
						<span>商品名称</span>
						<input type="" name="product_name" id="product_name" value="" />
					</p>
				</div>
				<script id="container" name="product_content" type="text/plain">
         	   
        </script>
				<div class="csy-ope" >
					<input class="csy-submit" type="submit" name="" id="" value="提交" />
					<input class="csy-reset" type="reset" name="" id="" value="重置" />
				</div>
				
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
	  var editor = UE.getEditor('container',{serverUrl: "{{env('APP_URL').'/phpPlugins/ueditor/controller.php'}}"});
	  //表单验证
	  	var validatorEd = $("#productAdd").validate({
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
	        },
	         messages: {
		      product_name: "请输入商品名称",
		      product_image:{
		      	required:"请上传图片"
		      	
		      },
		      product_content:{
		      	required:"请输入内容"
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
		             	   layer.msg(res.messages, {icon: 2, time: 1000});
		             	
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

</script>


@endsection