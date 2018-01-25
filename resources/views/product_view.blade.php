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
</style>
@endsection

@section('content')
	
	<div class="pruduct_box">
		
		 @if(!empty($product_info))
    	<form id="product_editform" action="" method="post">
    		@else
    	<form id="product_addform" action="" method="post">
    		@endif
		
		
		
		
		
		<div>
			<p>
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
				<label>商品图片</label>
				<input type="file" accept="image/gif, image/jpeg,image/jpg,image/png"   name="product_thumb" id="" value="" />

			</p>
			<p>
				<label>商品名称</label><input type="text" name="product_name" id="" value="{{$product_info['product_name'] or ''}}" />
				<label>排序</label><input type="text" name="sort" id="" value="{{$product_info['sort'] or ''}}" />

			</p>
		</div>
		<!--添加规格-->
		<div id="addguigefa">
			
			<p>添加规格：</p>
			
		<!--循环商品规格-->	
	 @if(!empty($product_info))
	 	<!--商品id-->
	 	<input type="hidden" name="product_id" id="product_id" value="{{$product_info['product_id']}}" />
    	@foreach($product_info['spec_info'] as $item)
			<div class="addguige">
				<p>
					<label>商品图片</label>
					<input type="file" class="product_img spec_image" onchange="upimg()"  accept="image/gif,image/jpeg,image/jpg,image/png" name="spec_image" id="spec_image" value="" />
					<label>规格名称</label><input class="spec_name" type="text" name="spec_name" id="" value="{{$item['spec_name'] or ''}}" />
	
				</p>
				<p>
					<label>计量单位</label><input class="spec_unit" type="text" name="spec_unit" id="" value="{{$item['spec_unit'] or ''}}" />
				</p>
				<!--供应商协议价-->
				<div>供应商协议价：</div>
				
				<div class="fa_agreementprice">
					<div class="agreementprice">
						@foreach($item['supplier_price'] as $items)	
							<p class="suliper"><label>选择供应商</label>
								<select class="supplier_list supplier2" name="supplier_list">
										@foreach($supplier_list as $itema)
											<option value="{{$itema['user_id']}}" @if($itema['user_id'] == $items['user_id']) selected="selected" @endif  >{{$itema['nick_name']}}</option>
										@endforeach
								</select>
								<label>价格</label>
								<input type="text" class="supplier_price supplier2" name="supplier_price"  value="{{$items['price'] or ''}}" />
								<div><span>元/</span><span>斤</span></div>
							</p>
						@endforeach
					</div>
					
					<div class="add_agreementprice" onclick="addjiage(this)">增加供应商协议价</div>
				</div>
				
				<!--军方协议价-->
				<div>
					<label>军方协议价</label><input class="army_price" name="army_price" type="text" value="{{$item['product_price'] or ''}}" />
					<div><span>元/</span><span>斤</span></div>
	
				</div>
				
			</div>
		@endforeach
    		@else
    	<div class="addguige">
				<p>
					<label>商品图片</label>
					<input type="file" class="spec_image product_img" onchange="upimg()"  accept="image/gif,image/jpeg,image/jpg,image/png" name="spec_image" id="spec_image" value="" />
					<label>规格名称</label><input class="spec_name" type="text" name="spec_name" id="" value="" />
	
				</p>
				<p>
					<label>计量单位</label><input class="spec_unit" type="text" name="spec_unit" id="" value="" />
				</p>
				<!--供应商协议价-->
				<div>供应商协议价：</div>
				
				<div class="fa_agreementprice">
					<div class="agreementprice">
						<p class="suliper"><label>选择供应商</label>
							<select class="supplier_list supplier2" name="supplier_list">
								@foreach($supplier_list as $item)
									<option value="{{$item['user_id']}}">{{$item['nick_name']}}</option>
								@endforeach
							</select>
							<label>价格</label>
							<input type="text" class="supplier_price supplier2" name="supplier_price" id="" value="" />
							<div><span>元/</span><span>斤</span></div>
						</p>
					</div>
					
					<div class="add_agreementprice" onclick="addjiage(this)">增加供应商协议价</div>
				</div>
				
				<!--军方协议价-->
				<div><label>军方协议价</label><input class="army_price" type="text" name="army_price" id="" value="" />
					<div><span>元/</span><span>斤</span></div>
	
				</div>
				
			</div>
    		@endif
		
		
		
		<!--循环规格结束-->
		
		</div>
			<div class="conadd">继续添加规格</div>
				<script id="container" name="product_content" type="text/plain">
         	   		
        </script>
		<div>
			<input class="addspecsubmit" type="submit" name="" id="" value="提交" />
			<input type="reset" name="" id="" value="重置" />
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
	
	
	
	
	
	
	
	
	$(".conadd").click(function(){
		var addguige=$(this).parent().find(".addguige").eq(0);
			addguige.clone().appendTo("#addguigefa");
		
	})
	function addjiage(elm){
			$(".agreementprice").eq(0).clone().prependTo($(elm).parent());
		/*$(elm).parent().jQueryprepend('<div class="agreementprice"><p class="suliper"><label>选择供应商</label><select class="supplier_list supplier2" name="supplier_list"><option value="2">AA</option><option value="3">BB</option><option value="5">蔬菜366</option></select><label>价格</label><input type="text" class="supplier_price supplier2" name="" id="" value=""></p><div><span>元/</span><span>斤</span></div><p></p></div>')
	*/
	}

	
		

	
	
		/*文本编辑器*/
		var editor = UE.getEditor('container',{serverUrl: "{{env('APP_URL').'/phpPlugins/ueditor/controller.php'}}"});


/*上传图片接口*/
	
	var image_original1=[];
	var image_thumb1=[];
	 var k=0;
	function upimg(){
	
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
	          	
	          	spec_image:{
	          		required: true,
	          	},spec_name:{
	          		required: true,
	          	},spec_unit:{
	          		required: true,
	          	},supplier_price:{
	          		required: true,
	          	},army_price:{
	          		required: true,
	          		number:true
	          	}
	        
	        },
	         messages: {
		      sort: {
		      	required:"请输入排序",
		      	digits:"请输入整数",
		      	range:"范围在-9999和9999之间"
		      },
		       product_thumb:{
		      	required: "请上传图片",
	        	
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