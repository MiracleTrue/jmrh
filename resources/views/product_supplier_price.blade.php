@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/css/goods-management.css')}}">
 	<link rel="stylesheet" href="{{asset('webStatic/css/page.css')}}">
<style>
	
</style>
@endsection
<div class="layer3">
	<div class="addguige">增加</div>
	<div class="seccendstep_div1">
		<form class="addxieyi" action="" method="post">
			
			<p><span>供货商</span>
		<select name="user_id">
			@foreach($supplier_list as $item)
				<option value="{{$item['user_id']}}">{{$item['nick_name']}}</option>
			@endforeach
		</select>
		<span>公开价</span><input type="text" name="price" id="" value="" />
		<span class="deleguige" onclick="ProductSpecDelete(this)">删除</span>
		<input type="submit" class="xieyisubmit" name="" id="" value="确认" onclick="xieyiform(this)" />
		</p>
	
	<input type="hidden" name="spec_id" id="" value="{{$spec_id}}" />
		
		</form>
	
	</div>
	
</div>
@section('content')
@endsection
@section('MyJs')
<script type="text/javascript" src="{{asset('webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
<script type="text/javascript" src="{{asset('webStatic/library/jquery.validation/1.14.0/validate-methods.js')}}"></script>
<script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>
<script>
	$(".addguige").click(function(){
			      		$(".layer3").append('<div class="seccendstep_div1"><form class="addxieyi" action="" method="post"><p><span>供货商</span><select name="user_id">@foreach($supplier_list as $item)<option value="{{$item['user_id']}}">{{$item['nick_name']}}</option>@endforeach</select><span >公开价</span><input type="text" name="price" id="" value="" /><span class="deleguige" onclick="ProductSpecDelete(this)">删除</span><input type="submit" class="xieyisubmit" name="" id="" value="确认" onclick="xieyiform(this)" /></p><input type="hidden" name="spec_id" id="" value="{{$spec_id}}" /></form></div>')
			      	
			      	$(".deleguige").on("click",function(){
						$(this).parent().parent().remove();
					})
			      	
			      	})
			      	
		   //新增供应商协议价
	   function xieyiform(elm){
		   	that=$(elm);
		   	var addspec= $(elm).parent().parent().validate({
		        rules: {
		        	price:{
				        required: true,
				        isIntGtZero:true,
			            number: true
		          	}
		        },
		        messages: {
			       price:{
			      	required: "请输入价格",
		        	isIntGtZero:"请输入大于0的整数",
		        	number:"请输入一个数字"
			      }
			    }, 
			    submitHandler: function (form) {
			        $(form).ajaxSubmit({
			            url: '{{url("product/supplier/price/add")}}',
			            type: 'POST',
			            dataType: 'JSON',
			            data:{
			            	_token:'{{csrf_token()}}'
			            },
			            beforeSend:function(res){
			            	
			            	/* $(".xieyisubmit").attr("disabled","true");*/
			            
			            	
			            },
			            success: function (res) {
			        	console.log(res);
			        	
			             if(res.code==0){
			             	price_id=res.data.price_info.price_id;
			      	  that.parent().parent().attr("price_id",price_id);
			             	  layer.msg(res.messages, {icon: 1, time: 1000},function(){  
			             	/* $(".xieyisubmit").not(that).removeAttr("disabled");*/
			             	
			             	   });
							addspecstate=true;
			             }else{
			             	   layer.msg(res.messages, {icon: 2, time: 1000},function(){
			             	 		/*that.removeAttr("disabled");*/
			             	   });
			             }
			            }
			          });
		        }
		
		      });
	    

	   }
	      	 	      	
		/*删除供应商协议价*/
			function ProductSpecDelete(elm){
		  		var price_id = $(elm).parent().parent().attr("price_id");
		  	/*	console.log(price_id);*/
		  			if(price_id){
		  				$.ajax({
				  			  url: '{{url("product/supplier/price/delete")}}',
						      type: 'POST',
						      dataType: 'JSON',
						      data:{
						        price_id:price_id,
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
        	
		
</script>


@endsection