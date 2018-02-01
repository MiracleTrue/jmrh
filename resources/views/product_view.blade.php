@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/addclassify.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/library/editable-select/jquery.editable-select.min.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/css/product_view.css')}}">

<style>
	.error{
	color: red;
	
}
.pruduct_box{
	padding-left: 40px;
	padding-right: 40px;
	
}
.product_h1{
	height: 93px;
	line-height: 93px;
	font-size: 24px;
	color: #0e99dc;
	border-bottom: 1px solid #dddddd;
}
select{
	width: 284px;
	height: 33px;
	margin-left: 15px;
}
input{
	width: 284px;
	height: 39px;
	margin-left: 15px;
}p{
	font-size: 15px;
	height: 59px;
	line-height: 59px;
}
.add_agreementprice{
	margin: 0 auto;
	text-align: center;
	height: 44px;
	line-height: 44px;
	color: #0e99dc;
	font-size: 15px;
	cursor: pointer;
}
.conadd{
	text-align: center;
	cursor: pointer;
	height:60px;
	line-height: 60px;
	color: #0e99dc;
	font-size: 15px;
}
.addspecsubmit{
	width: 210px;
	height: 64px;
	background: #fe8d01;
	color: #FFFFFF;
	font-size: 20px;
	text-align: center;
	line-height: 64px;
	font-weight: bold;
}
.reset{
	width: 210px;
	height: 64px;
font-weight: bold;
	font-size: 20px;
	text-align: center;
	line-height: 64px;
}
.addguige{
	margin-top: 10px;
	padding-bottom: 25px;
	border-bottom: 1px dashed #333333;
}
</style>
@endsection

@section('content')
	
	<div class="pruduct_box">
		
		
		
		
		
		 @if(!empty($product_info))
    	<form id="product_editform" action="" method="post">
    		<h1 class="product_h1">编辑分类</h1>
    		@else
    		<h1 class="product_h1">添加分类</h1>
    	<form id="product_addform" action="" method="post">
    		@endif
		
		<div class="error"></div>
		
		
		
		<div>
			<p style="height: 60px;line-height: 60px;font-size: 15px;margin-top: 30px;position: relative;">
				<label>所属分类</label>
				<select name="category_id">
					
					@foreach($category_list as $item)
						@if(!empty($product_info))
							<option value="{{$item['category_id']}}" @if($item['category_id'] == $product_info['category_id']) selected="selected" @endif >{{$item['category_name']}}</option>
						@else
							<option value="{{$item['category_id']}}"  >{{$item['category_name']}}</option>
						@endif
					@endforeach
				</select>
				<label style="margin-left: 28px;">商品图片</label>
				<input style="z-index: 1; filter: alpha(opacity=0);opacity: 0" type="file" accept="image/gif, image/jpeg,image/jpg,image/png" name="product_thumb" id="product_thumb" class="upimgclass"/>
				<input  style="background-color: #FFFFFF;border: 1px solid #A9A9A9;position: absolute;top: 13px;right: 70px;" disabled="disabled" class="faker" value="点击右边上传按钮" />
				<a><label for="product_thumb"><img style="cursor: pointer;" src="{{asset('webStatic/images/shizi.png')}}" alt="浏览按钮" /></label></a>
			</p>
			<p style="height: 60px;line-height: 60px;font-size: 15px;">
				<label style="">商品名称</label>
				<input style="height: 35px;width: 280px;" type="text" name="product_name" id="" value="{{$product_info['product_name'] or ''}}" />
				<label style="margin-left: 50px;">排序</label>
				<input type="text" name="sort" id="" value="{{$product_info['sort'] or ''}}" style="height: 35px;width: 280px;margin-left: 15px;" />

			</p>
		</div>
		<!--添加规格-->
		<div id="addguigefa">
			
			<p style="height: 48px;line-height: 48px;color:#0e99dc ;font-size: 16px;">添加规格：</p>
			
		<!--循环商品规格-->	
	 @if(!empty($product_info))
	 	<!--商品id-->
	 	<input type="hidden" name="product_id" id="product_id" value="{{$product_info['product_id']}}" />
    	@foreach($product_info['spec_info'] as $item)
			<div class="addguige">
				<p style="height: 60px;line-height: 47px;font-size: 15px;position: relative;">
					<label>规格图片</label>
					<input style="padding-top: 10px;height: 29px;" class="upimgclass product_img spec_image" style="z-index: 1; filter: alpha(opacity=0);opacity: 0" type="file" onchange="upimg(this)"  accept="image/gif,image/jpeg,image/jpg,image/png" name="spec_image" id="spec_image" value="" />
					<input class="faker" style="background-color: #FFFFFF;border: 1px solid #A9A9A9;position: absolute;top: 5px;left: 61px;" disabled="disabled" value="点击右边上传按钮" />
					<a><label class="labelid"><img style="cursor: pointer;" src="{{asset('webStatic/images/shizi.png')}}" alt="浏览按钮" /></label></a>
					<label>规格名称</label><input class="spec_name" type="text" name="spec_name" id="" value="{{$item['spec_name'] or ''}}" />
	
				</p>
				<p>
					<label>计量单位</label><input class="spec_unit" type="text" name="spec_unit" id="" value="{{$item['spec_unit'] or ''}}" />
				</p>
				<!--供应商协议价-->
				<div style="height: 48px;line-height: 48px;color:#0e99dc ;font-size: 16px;">供应商协议价：</div>
				
				<div class="fa_agreementprice">
					<div class="agreementprice">
						@foreach($item['supplier_price'] as $items)	
							<p class="suliper" style="position: relative;">
								<label>选择供应商</label>
								<select class="supplier_list supplier2" name="supplier_list">
										@foreach($supplier_list as $itema)
											<option value="{{$itema['user_id']}}" @if($itema['user_id'] == $items['user_id']) selected="selected" @endif  >{{$itema['nick_name']}}</option>
										@endforeach
								</select>
								<label style="margin-left: 40px;">价格</label>
								<input type="text" class="supplier_price supplier2" name="supplier_price"  value="{{$items['price'] or ''}}" />
							<a style="position: absolute;right: 69px;top: 3px;"><span>元/</span><span class="myspec_unit">{{$item['spec_unit']}}</span></a>	
							</p>
							
						@endforeach
					</div>
					
					<div class="add_agreementprice" onclick="addjiage(this)"><a>增加供应商协议价</a></div>
				</div>
				
				<!--军方协议价-->
				<div style="position: relative;">
					<label style="font-size: 15px;">军方协议价</label><input class="army_price" name="army_price" type="text" value="{{$item['product_price'] or ''}}" />
					<div style="position: absolute;left: 337px;top: 12px;"><span>元/</span><span class="myspec_unit">{{$item['spec_unit']}}</span></div>
	
				</div>
				
			</div>
		@endforeach
    		@else
    		
    		<!--添加商品-->
    		
    	<div class="addguige">
				<p style="height: 60px;line-height: 47px;font-size: 15px;position: relative;">
					<label>规格图片</label>
					<input style="padding-top: 10px;height: 29px;" class="upimgclass spec_image product_img" type="file" onchange="upimg(this)"  accept="image/gif,image/jpeg,image/jpg,image/png" name="spec_image" id="spec_image" value="" />
					
					<input style="background-color: #FFFFFF;border: 1px solid #A9A9A9;position: absolute;top: 5px;left: 61px;" class="faker" disabled="disabled" value="点击右边上传按钮" />
					<a><label class="labelid"><img style="cursor: pointer;" src="{{asset('webStatic/images/shizi.png')}}" alt="浏览按钮" /></label></a>
					
					
					<label style="margin-left:23px;">规格名称</label>
					<input class="spec_name" type="text" name="spec_name" id="" value="" style=""/>
	
				</p>
				<p>
					<label>计量单位</label><input class="spec_unit" type="text" name="spec_unit" id="" value="" />
				</p>
				<!--供应商协议价-->
				<div style="height: 48px;line-height: 48px;color:#0e99dc ;font-size: 16px;">供应商协议价：</div>
				
				<div class="fa_agreementprice">
					<div class="agreementprice" style="position: relative;">
						<p class="suliper" style="position: relative;">
							<label>选择供应商</label>
							<select class="supplier_list supplier2" name="supplier_list">
								@foreach($supplier_list as $item)
									<option value="{{$item['user_id']}}">{{$item['nick_name']}}</option>
								@endforeach
							</select>
							<label style="margin-left: 40px;">价格</label>
							<input style="position: relative;" type="text" class="supplier_price supplier2" name="supplier_price" id="" value="" />
						<a style="position: absolute;right: 76px;top: 3px;"><span>元/</span><span class="myspec_unit">斤</span></a>
							
						</p>

					</div>
					
						<div  class="add_agreementprice" onclick="addjiage(this)">增加供应商协议价</div>
				</div>
				
				<!--军方协议价-->
				<div style="position: relative;"><label style="font-size: 15px;">军方协议价</label><input class="army_price" type="text" name="army_price" id="" value="" />
					<div style="position: absolute;left:335px;top: 13px;font-size: 14px;"><span>元/</span><span class="myspec_unit">斤</span></div>
	
				</div>
				
			</div>
    		@endif
		
		
		
		<!--循环规格结束-->
		
		</div>
			<div class="conadd">继续添加规格</div>
				<script id="container" name="product_content" type="text/plain">
         	   		
        </script>
		<div style="text-align: center;margin-top: 50px;">
			<input class="addspecsubmit" type="submit" name="" id="" value="提交" />
			<input class="reset" type="reset" name="" id="" value="重置" />
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
<script type="text/javascript" src="{{asset('/webStatic/library/editable-select/jquery.editable-select.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/webStatic/library/ajaxfileupload/ajaxfileupload.js')}}"></script>
<script type="text/javascript" src="{{asset('/webStatic/library/jqueryJson/jquery.json.js')}}"></script>

<script>
	$(".labelid").click(function(){
		$(this).parent().siblings(".spec_image").trigger("click");
	});
	$(".conadd").click(function(){
		var addguige=$(this).parent().find(".addguige").eq(0);
			addguige.clone(true).appendTo("#addguigefa");
		
	})
	function addjiage(elm){
		/*	$(".agreementprice").eq(0).clone().prependTo($(elm).parent());*/
		$(".suliper").eq(0).clone(true).prependTo($(elm).parent());
		/*$(elm).parent().jQueryprepend('<div class="agreementprice"><p class="suliper"><label>选择供应商</label><select class="supplier_list supplier2" name="supplier_list"><option value="2">AA</option><option value="3">BB</option><option value="5">蔬菜366</option></select><label>价格</label><input type="text" class="supplier_price supplier2" name="" id="" value=""></p><div><span>元/</span><span>斤</span></div><p></p></div>')
	*/
	}
	
	
	
	
	
	//获取上传图片名称
	var imgName;
	$(".upimgclass").change(function(){
		var imgName=$(this).val();
		var arr=imgName.split('\\');
		var my=arr[arr.length-1];
		console.log(my)
		/*$(this).siblings("input.faker").val(my);*/
		 $(this).parent().find(".faker").val(my);
		
	})
	
	
	
	
	
	$(".spec_unit").blur(function(){
		$(this).parent().parent().find(".myspec_unit").text($(this).val());
		/*$(".myspec_unit").text($(".spec_unit").val())*/
	})
	 if ($.validator) {
	   $.validator.prototype.elements = function () {
	    var validator = this,
	     rulesCache = {};
	    return $(this.currentForm)
	    .find("input, select, textarea")
	    .not(":submit, :reset, :image, [disabled]")
	    .not(this.settings.ignore)
	    .filter(function () {
	     if (!this.name && validator.settings.debug && window.console) {
	      console.error("%o has no name assigned", this);
	     }
	     rulesCache[this.name] = true;
	     return true;
	    });
	   }
  }
	
	
	
	
	
	
	
	

	
		

	
	
		/*文本编辑器*/
		var editor = UE.getEditor('container',{serverUrl: "{{env('APP_URL').'/phpPlugins/ueditor/controller.php'}}"});


/*上传图片接口*/
	
	var image_original1=[];
	var image_thumb1=[];
	 var k=0;
	/* $(".spec_image").change(function(){
	 	
	 })*/ 
	 
	function upimg(elm){
		
		$(elm).parent().find(".faker").val($(elm).val().split('\\')[$(elm).val().split('\\').length-1]);
		
		$.ajaxFileUpload({
			url: '{{url("product/upload/spec/image")}}',
		    type: 'POST',
		    dataType: 'JSON',
		    fileElementId : 'spec_image',
		    data:{
		    	_token:'{{csrf_token()}}'
		    },
		    success:function(resData){
		    	var resData=JSON.parse(resData);
		    console.log(resData);
		    if(resData.code==0){
		    	
		    
		    				             	
			             	  layer.msg(resData.messages, {icon: 1, time: 1000},function(){ });
			             }else{
			             	 layer.msg(resData.messages, {icon: 2, time: 1000},function(){ });
			             }
		  		
		  			image_original1[k]=resData.data.spec_image.image_original;
		  			image_thumb1[k]=resData.data.spec_image.image_thumb;
		  			k++;
		   	
		
		  
		   	
		     
		   /* image_original=resData.data.spec_image.image_original;
		    image_thumb=resData.data.spec_image.image_thumb;*/
		    
		   
	    
		    }
		});
		
	}
	

	  
	/*json生成*/
	  var bigarry=[];
	  var supplier_price=[];

//    var obj2=new Object();
	
        var strjson
     
      var spec_json;
  
    
   //  $(".submitadd").click(function(){
     //json()
      	  function json(){
    	
		      	$(".addguige").each(function(i,elm){
		      		var obj = new Object();
		      	
		    
			  	 	obj.spec_name =$(".spec_name").eq(i).val();
			  	 	obj.spec_unit =$(".spec_unit").eq(i).val();
		            obj.product_price = $(".army_price").eq(i).val();
		            obj.image_thumb = image_thumb1[i];
		            obj.image_original=image_original1[i];
		            obj.supplier_price=[];
		            
		            $(elm).find(".suliper").each(function(k,elm2){ 
				             
					  		var obj2 = new Object();
					  		obj2.user_id=$(elm2).find(".supplier_list  option:selected").val();
					  		obj2.price=$(elm2).find(".supplier_price").val();			  		
					  		obj.supplier_price.push(obj2)
			  		});
		     
				  		             bigarry.push(obj);
			  
			  	});
			  	
		       strjson = JSON.parse($.toJSON(bigarry));
			
		      spec_json=JSON.stringify(strjson);
		      //console.log(JSON.parse(strjson))
		     // console.log(spec_json);
	       
	  	  }
    
      
      
   // })
$(function(){
	 /*验证添加商品 */
	  	var addspec= $("#product_addform").validate({
	        rules: {
	          sort: {
	            required: true,
	            digits:true,
		        number:true,
		        range:[-9999,9999]    
	          },
	            product_thumb:{
		           required: true,
	          },
	            product_name:{
	          	 required: true
	          },
	          	category_id:{
	          		required: true
	          	},
	          	
	          	spec_name:{
	          		required: true,
	          	},spec_unit:{
	          		required: true,
	          	},supplier_price:{
	          		required: true,
	          	},army_price:{
	          		required: true,
	          		number:true
	          	},
	      
	        },
	         messages: {
		      sort: {
		      	required:"请输入排序",
		      	digits:"请输入整数",
		      	range:"范围在-9999和9999之间"
		      },
		       product_thumb:{
		      	required: "请上传商品图片",
	        	
		      },
		       product_name:{
		       	 required: "请输入商品名称",
		       },
		       category_id:{
		       	required:"请选择分类"
		       },
		       spec_image:{
		       	required:"请上传规格图片"
		       },
		       spec_name:{
		       	required:"请填写规格名称"
		       }
		       ,
		       spec_unit:{
		       	required:"请填写计量单位"
		       } ,
		       supplier_price:{
		       	required:"请选择供应商协议价"
		       } ,
		       army_price:{
		       	required:"请输入军方协议价",
		       	number:"价格格式不正确"
		       }
		       
		      
		    }, 
		      errorLabelContainer:$(".error"),
		        wrapper:"li",		     
		    submitHandler: function (form) {
		    		json();
		          $(form).ajaxSubmit({
		            url: '{{url("product/add/submit")}}',
		            type: 'POST',
		            dataType: 'JSON',
		            data:{
		            	spec_json:spec_json,
		            	_token:'{{csrf_token()}}'
		            },
		            beforeSend:function(res){
		            	
		            	 $(".addspecsubmit").attr("disabled","true");
		            
		            	
		            },
		            success: function (res) {
		        	/*console.log(res);*/
		        	
			           if(res.code==0){			             	
			             	  layer.msg(res.messages, {icon: 1, time: 1000},function(){
			             	  	 parent.location.reload();
			             	  	  layer.closeAll('');  
			             	 $(".addspecsubmit").removeAttr("disabled");			            
			             	   });
							addspecstate=true;
			             }else{
			             	
			             
			             	   layer.msg(res.messages, {icon: 2, time: 1000},function(){
			             	 			 $(".addspecsubmit").removeAttr("disabled");
			             	   });
			             }
		            }
		          });
	        }
	
	      });
	      
	      /*验证编辑商品*/
	    
	     
	     var addspec= $("#product_editform").validate({
	        rules: {
	          sort: {
	            required: true,
	            digits:true,
		        number:true,
		        range:[-9999,9999]    
	          },
	           
	            product_name:{
	          	 required: true
	          },
	          	category_id:{
	          		required: true
	          	},
	          	spec_image:{
	          		required: true
	          	},
		       spec_name:{
		       	required:true
		       }
		       ,
		       spec_unit:{
		       	required:true
		       } ,
		       supplier_price:{
		       	required:true
		       } ,
		       army_price:{
		       	required:true,
		       	number:true
		       }
		       
	        
	        },
	         messages: {
		      sort: {
		      	required:"请输入排序",
		      	digits:"请输入整数",
		      	range:"范围在-9999和9999之间"
		      },
		     
		       product_name:{
		       	 required: "请输入商品名称",
		       },
		       category_id:{
		       	required:"请选择分类"
		       },
		       spec_image:{
		       	required:"请上传规格图片"
		       },
		       spec_name:{
		       	required:"请填写规格名称"
		       }
		       ,
		       spec_unit:{
		       	required:"请填写计量单位"
		       } ,
		       supplier_price:{
		       	required:"请选择供应商协议价"
		       } ,
		       army_price:{
		       	required:"请输入军方协议价",
		       	number:"请输入正确价格格式"
		       }
		      
		    }, 
		    submitHandler: function (form) {
		    		json();
		          $(form).ajaxSubmit({
		            url: '{{url("product/edit/submit")}}',
		            type: 'POST',
		            dataType: 'JSON',
		            data:{
		            	spec_json:spec_json,
		            	_token:'{{csrf_token()}}'
		            },
		            beforeSend:function(res){
		            	
		            	 $(".addspecsubmit").attr("disabled","true");
		            
		            	
		            },
		            success: function (res) {
		        	console.log(res);
		        	
			           if(res.code==0){			             	
			             	  layer.msg(res.messages, {icon: 1, time: 1000},function(){  
			             	 $(".addspecsubmit").removeAttr("disabled");
			             	  parent.location.reload();  
			             	  layer.closeAll('');
			             	   });
							addspecstate=true;
			             }else{
			             	
			             
			             	   layer.msg(res.messages, {icon: 2, time: 1000},function(){
			             	 			 $(".addspecsubmit").removeAttr("disabled");
			             	   });
			             }
		            }
		          });
	        }
	
	      });
	     
})
	 
	     
	     
	     
	      
	      
	      
	
	
</script>
	

@endsection