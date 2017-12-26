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
  <div class="csy-box firststep">
  
			<header>修改商品</header>
		
			
			
	
		<form id="productEdit" action="" method="post">
		
			
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
						<input style="z-index: 1; filter: alpha(opacity=0);opacity: 0" type="file"  accept="image/gif, image/jpeg,image/jpg,image/png" name="product_thumb" id="product_image" value="" />
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
					<div class="productAdd_div1">
					<!--<p style="text-indent: 30px;">
						<span>价格</span>
						<input type="" name="product_price" id="product_price" value="{{$product_info['product_price'] or ''}}" onkeyup="test(this.value)"/>
					</p>-->
					<p style="padding-right: 49px;">
						<span >单位</span>
							<select id="product_unit" name="product_unit">
								<!--@foreach($unit_list as $item)
								 <option value="{{$item}}" >{{$item}}</option>
								@endforeach-->
									@if(!empty($product_info))
										@foreach($unit_list as $item)
										 <option value="{{$item}}" @if($item == $product_info['product_unit']) selected="selected" @endif >{{$item}}</option>
										@endforeach
									@else
									@foreach($unit_list as $item)
									 <option value="{{$item}}" >{{$item}}</option>
									@endforeach
								@endif
							</select>					
					</p>
					
				</div>
				<script id="container" name="product_content" type="text/plain">
         	   		
        </script>
				<div class="csy-ope">
					<input class="csy-submit" type="submit" name="" id="" value="下一步" />
					<input class="csy-reset" type="reset" name="" id="" value="重置" />
				</div>
				
					@if(!empty($product_info))
				<input type="hidden" name="product_id" id="product_id" value="{{$product_info['product_id']}}" />
				<input type="hidden" name="category_id" id="category_id" value="{{$product_info['category_id']}}" />
				<input type="hidden" name="product_content1" id="product_content1" value="{{$product_info['product_content']}}" />
				@endif
			</form>
		</div>
		<div class="seccendstep">
			<div class="addguige">
					增加
				</div>
				<form class="addspec" action="" method="post">
					
				
				
				<div class="seccendstep_div1">
					<p>
						<span>规格名</span>
						<input type="text" name="spec_name" id="" value="" />
					</p>
					<p>
						<span>规格图片</span>
					<input type="file"  accept="image/gif, image/jpeg,image/jpg,image/png" name="spec_image" id="spec_image" value="" />
					</p>
					<p>
						<span >公开价</span>
						<input type="text" name="product_price" id="" value="" />
						<input type="button"  class="xieyiguanli" onclick="xieyiguanli(this)" name="" id="" value="协议价管理" />
					</p>
					<span class="deleguige" onclick="ProductSpecDelete(this)">删除</span>
					<input type="submit" class="addspecsubmit" name="" id="" value="确认" onclick="myform(this,formclass)"/>
				</div>
			</form>
		<div class="netxstep2">下一步</div>
		</div>
		<div class="thirdstep">
			<p>确认提交吗</p>
			<button type="submit">提交</button>
			<div class="backstep">上一步</div>
		</div>
	
@endsection
@section('MyJs')
<script type="text/javascript" src="{{asset('webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
<script type="text/javascript" src="{{asset('webStatic/library/jquery.validation/1.14.0/validate-methods.js')}}"></script>
<script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>
<script src="{{asset('webStatic/library/ueditor/1.4.3/ueditor.config.js')}}" type="text/javascript" charset="utf-8"></script>
<script src="{{asset('webStatic/library/ueditor/1.4.3/ueditor.all.min.js')}}" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="{{asset('/webStatic/library/editable-select/jquery.editable-select.min.js')}}"></script>

<script>
	
	$('#product_unit').editableSelect({
	effects: 'slide'
});
$(".es-input").css("border","1px solid #AAAAAA");
$(".es-input").css("margin-left","48px");
$(".es-input").css("width","268px");


$(".es-input").attr("placeholder","请选择单位");
$(".es-input").val("{{$product_info['product_unit'] or ''}}");


	$(".firststep").show().siblings().hide();
	
	
	
/*	$(".deleguige").click(function(){
			$(this).parent().remove();
	})*/
	var specnum=0;
	var formclass
	$(".addguige").click(function(){
		specnum++;
		 formclass="addspec"+specnum;
		 console.log(formclass)
		$(".seccendstep").append('<form class=formclass><div class="seccendstep_div1"><p><span>规格名</span><input type="text" name="spec_name" id="" value="" /></p><p><span>规格图片</span><input type="file"  accept="image/gif, image/jpeg,image/jpg,image/png" name="spec_image" id="spec_image" value="" /></p><p><span >公开价</span><input type="text" name="product_price" id="" value="" /><input type="button"  class="xieyiguanli" name="" id="" value="协议价管理" onclick="xieyiguanli(this)"/></p><span class="deleguige" onclick="ProductSpecDelete(this)">删除</span><input type="submit"  class="addspecsubmit" name="" id="" value="确认" onclick="myform(this,formclass)"/></div></form>');
		console.log($(".deleguige").length);
		/*$(".deleguige").on("click",function(){
			$(this).parent().remove();
		});*/
		
		   
	})	

   /*删除商品规格*/
  
  	function ProductSpecDelete(elm){
  		var spec_id = $(elm).parent().parent().attr("spec_id");
  		console.log(spec_id);
  			if(spec_id){
  				$.ajax({
		  			  url: '{{url("product/spec/delete")}}',
				      type: 'POST',
				      dataType: 'JSON',
				      data:{
				        spec_id:spec_id,
				        _token:'{{csrf_token()}}'
				      },
				      success:function(res){
				      	console.log(res);
				      	if(res.code==0){
				      		 layer.msg(res.messages, {icon: 1, time: 1000},function(){
				      	 	$(elm).parent().remove();
				      	 });
				      	}
				      	
				      }
		  		});
  			}else{
  				$(elm).parent().remove();
  			}
  		
  	}
  
  	

var that;
var addspecstate=false;
   //新增商品规格验证
	   function myform(elm,formclass){
	   	console.log($(elm).parent().parent());
	  	that=$(elm);
	   	var addspec= $(elm).parent().parent().validate({
	        rules: {
	          spec_name: {
	            required: true
	          },
	            product_price:{
		           required: true,
		           isIntGtZero:true,
	               number: true
	          },
	            spec_image:{
	          	 required: true
	          }
	        
	        },
	         messages: {
		      spec_name: "请输入商品名称",
		       product_price:{
		      	required: "请输入价格",
	        	isIntGtZero:"请输入大于0的整数",
	        	number:"请输入一个数字"
		      },
		       spec_image:{
		       	 required: "请上传图片",
		       }
		      
		    }, 
		    submitHandler: function (form) {
		    	/*console.log(product_id);*/
		          $(form).ajaxSubmit({
		            url: '{{url("product/spec/add")}}',
		            type: 'POST',
		            dataType: 'JSON',
		            data:{
		            	product_id:product_id,
		            	_token:'{{csrf_token()}}'
		            },
		            beforeSend:function(res){
		            	
		            	 $(".addspecsubmit").attr("disabled","true");
		            
		            	
		            },
		            success: function (res) {
		        	console.log(res);
		        	spec_id=res.data.spec_info.spec_id;
		        that.parent().parent().attr("spec_id",spec_id);
		             if(res.code==0){
		             	  layer.msg(res.messages, {icon: 1, time: 1000},function(){  
		             	 $(".addspecsubmit").not(that).removeAttr("disabled");
		             	
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
	    

	   }
	      	  

$(".netxstep2").click(function(){
	if(addspecstate){
		$(".thirdstep").show().siblings().hide();
	}else{
	
		  layer.msg("添加的规格没有确认", {icon: 2, time: 1000});
	}
})







function xieyiguanli(elm){
	var xieyi_spec_id=$(elm).parent().parent().parent().attr("spec_id");
	console.log(xieyi_spec_id);
	if(xieyi_spec_id){
		 layer.open({
			      type: 2,
			      title: false,
			      maxmin: false,
			       fixed :false,
			      shadeClose: true, //点击遮罩关闭层
			      area : ['800px' , '500px'],
			      content: '{{url('product/supplier/price')}}'+'/'+ xieyi_spec_id
			      ,success:function(){
			      	
			      }
			    });
	}
	
}
	

	
	
	
	
	//自定义validate验证输入的数字小数点位数不能大于两位
        jQuery.validator.addMethod("minNumber",function(value, element){
            var returnVal = true;
            inputZ=value;
            var ArrMen= inputZ.split(".");    //截取字符串
            if(ArrMen.length==2){
                if(ArrMen[1].length>2){    //判断小数点后面的字符串长度
                    returnVal = false;
                    return false;
                }
            }
            return returnVal;
        },"小数点后最多为两位");         //验证错误信息

	
		//单价输入保留4位小数
		function test(str){
		    var pos;
		    var fst
		    var lst;
		    if (str == "") return;
		    pos = str.indexOf(".");
		    if (pos != -1){
		        fst = str.substring(0,pos);
		        lst = str.substring(pos+1,pos.length);
		        if (lst.length > 2){             
		             var sub = lst.substring(0,2);
		          document.getElementById("product_price").value=fst+"."+sub;
		        }
		    }    
		}
	
	
	
	
	
	
	
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
	
	
	var product_id;
	      //修改商品表单验证
	      var validatorEd = $("#productEdit").validate({
	        rules: {
	          product_name: {
	            required: true
	          },
	            sort:{
	           required: true,
	           isIntGtZero:true
	          },
	            product_price:{
	          	 required: true,
	          	  isIntGtZero:true,
	          	   number: true
	          	  
	          },
	          product_unit:{
	          	required: true
	          }
	        },
	         messages: {
		      product_name: "请输入商品名称",
		      sort:{
		      	required: "请输入排序",
	        	isIntGtZero:"请输入大于0的整数"
		      },
		       product_price:{
		      	required: "请输入价格",
	        	isIntGtZero:"请输入大于0的整数",
	        	number:"请输入一个数字"
		      },
		      product_unit:{
		      	required: "请输入单位"
		      }
		    },
		    errorLabelContainer:$(".error"),
		    wrapper:"li",		     
		    submitHandler: function (form) {
		          $(form).ajaxSubmit({
		            url: '{{url("product/edit/submit")}}',
		            type: 'POST',
		            dataType: 'JSON',
		            data:{
		            	_token:'{{csrf_token()}}'
		            },
		            beforeSend:function(res){
		            	$("input[type='submit']").attr("disabled","true");
		            	
		            },
		            success: function (res) {
		        
		             if(res.code==0){
		             	  layer.msg(res.messages, {icon: 1, time: 1000},function(){  
		             	   parent.location.reload();	 
		             	   	  layer.closeAll('');
		             	   });
						 
		             }else{
		             	   layer.msg(res.messages, {icon: 2, time: 1000},function(){
		             	   $("input[type='submit']").removeAttr("disabled");
		             	   });
		             }
		            }
		          });
	        }
	
	      });
	      
		      	$(".csy-reset").on("click",function(){
		     	   validatorEd.resetForm();
		     	
		        });
		     

</script>


@endsection