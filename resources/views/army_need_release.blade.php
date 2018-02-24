@extends('layouts.master')
@section('MyCss')
<link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
<link rel="stylesheet" href="{{asset('webStatic/library/editable-select/jquery.editable-select.min.css')}}">
<link rel="stylesheet" href="{{asset('webStatic/css/choosename.css')}}">

<style type="text/css">#ary-submit {
	background: #fe8d01;
	color: #FFFFFF;
	margin-left: 45px;
	width: 210px;
	height: 64px;
	font-size: 19px;
	font-weight: bolder;
	line-height: 64px;
	text-align: center;
	display: inline-block;
}

#ary-reset {
	background: #EEEEEE;
	color: #000000;
	margin-left: 45px;
	width: 210px;
	height: 64px;
	font-size: 19px;
	font-weight: bolder;
	line-height: 64px;
	text-align: center;
	display: inline-block;
}

.ary-ope {
	width: 514px;
	margin: 57px auto 0 auto;
	padding-bottom: 60px;
}

.error {
	color: red;
	margin-left: 84px;
}

.error li {
	margin-left: 20px;
}

#product_number {
	position: relative;
	height: 43px;
	width: 305px;
	outline: 0;
	margin-left: 18px;
}

#product_unit {
	height: 30px;
	width: 60px;
	outline: 0;
	position: absolute;
	top: 9px;
	right: 6px;
}

/*input {
	border: 1px solid #ccc;
	font-size: 14px;
}*/

.ary_adddiv {
	overflow: hidden;
	margin-top: 38px;
}

.div_floatleft {
	float: left;
	font-size: 16px;
}

.div_floatleft input {
	height: 43px;
	width: 305px;
	outline: 0;
	margin-left: 18px;
}

.div_floatright {
	float: right;
	font-size: 16px;
}

.div_floatright input {
	height: 43px;
	width: 305px;
	outline: 0;
	margin-left: 18px;
	line-height: 43px;
}

li {
	list-style: none;
	
}

</style>
@endsection
@section('content')

<div class="mly-more">
	<form id="arym_form" action="" method="post">

		<div class="arymy_div1">
			添加需求
		</div>

		<div class="xuqiugrandpa">

			@if(!empty($cart_order))
			@foreach($cart_order as $item)
			<div class="xuqiuparent">

				<div class="ary_adddiv" style="position: relative;">

					<p style="text-indent: 15px;" class="div_floatleft" >
						<span>品名</span>
						<input style="width: 313px;margin-left: 33px;" type="text" name="product_name" class="product_name" value="{{$item['product_name']}}" readonly="readonly"/>
						<img class="moreName" style="position: absolute;left: 360px;top: 10px;"  src="{{asset('webStatic/images/morepinming.png')}}" alt="选择品名" />
						<span class="form_spec_name" style="position: absolute;left: 180px;top: 10px;">{{$item['spec_name']}}</span>
					</p>
					<p class="div_floatleft" style="text-indent: 15px;position: relative;">
						<span>数量</span>
						<input type="text" name="product_number" class="product_number" value="{{$item['product_number']}}" />
						<span style="position: absolute;right: 20px;top: 10px;">{{$item['spec_unit']}}</span>
					</p>
				</div>

				<div class="ary_adddiv">
					<p class="div_floatleft">
						<span>联系人</span>
						<input style="margin-left: 33px;width: 313px;" type="text" class="army_contact_person" name="" id="" value="{{$item['contact_person']}}" />
					</p>
					<p class="div_floatright">
						<span>电话</span>
						<input type="text"class="army_contact_tel" value="{{$item['contact_tel']}}"/>
					</p>

				</div>

				<div class="ary_adddiv">

					<p class="div_floatright">
						<span>到货时间</span>
						<input style="width: 296px;"  autocomplete="off" onClick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss',min: laydate.now()})" class="laydate-icon army_receive_time"  name="army_receive_time"  value="{{$item['army_receive_date']}}" placeholder="请选择日期(必须大于现在时间)"/>
					</p>

				</div>

				<div class="ary_adddiv" style="font-size: 16px;">
					<p style="text-indent: 12px;">
						<span>备注</span>
						<input class="army_note" style="height: 43px;width: 746px;outline: 0;margin-left: 18px;"  type="text" value="{{$item['note']}}"/>
					</p>

				</div>
				<input type="hidden" class="spec_name" name="" id="" value="{{$item['spec_name']}}" />
			</div>
			@endforeach

			@else

			<div class="xuqiuparent">

				<div class="ary_adddiv" style="position: relative;">

					<p style="text-indent: 15px;" class="div_floatleft" >
						<span>品名</span>
						<input style="margin-left: 33px;" type="text" name="product_name" class="product_name" value="" readonly="readonly"/>
						<img class="moreName" style="position: absolute;left: 360px;top: 10px;"  src="{{asset('webStatic/images/morepinming.png')}}" alt="选择品名" />
					<span class="form_spec_name" style="position: absolute;left: 180px;top: 10px;"></span>
					
					
					</p>
					<p class="div_floatleft" style="text-indent: 15px;position: relative;">
						<span>数量</span>
						<input type="text" name="product_number" class="product_number" value="" />
						<span style="position: absolute;right: 20px;top: 10px;" class="my_unit"></span>
						
					</p>
				</div>
				<div class="ary_adddiv">
					<p class="div_floatleft">
						<span>联系人</span>
						<input  style="margin-left: 33px" class="army_contact_person" name="" id="" value="" />
					</p>
					<p class="div_floatright">
						<span>电话</span>
						<input type="text"class="army_contact_tel"/>
					</p>

				</div>
				<div class="ary_adddiv">

					<p class="div_floatright">
						<span>到货时间</span>
						<input autocomplete="off" type="text" class="laydate-icon army_receive_time"  name="army_receive_time"  value="{{$order_info['army_receive_time'] or ''}}" style="width: 286px;" placeholder="请选择日期(必须大于现在时间)"/>
					</p>

				</div>

				<div class="ary_adddiv" style="font-size: 16px;">
					<p style="text-indent: 12px;">
						<span>备注</span>
						<input class="army_note" style="height: 43px;width: 746px;outline: 0;margin-left: 18px;"  type="text"/>
					</p>

				</div>
			</div>

			@endif

		</div>
		<div style="text-align: center;padding-top: 33px;">
			<img class="moreaddxuiqu" src="{{asset('webStatic/images/morebtn.png')}}"/>
		</div>
		<div class="ary-ope" style="">
			<input type="submit" class="ary-submit" name="ary-submit" id="ary-submit" value="提交" />
			<input type="reset" class="ary-reset" name="ary-reset" id="ary-reset" value="重置" />
		</div>
	</form>
</div>
@endsection

@section('MyJs')

<script type="text/javascript" src="{{asset('/webStatic/library/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
<script type="text/javascript" src="{{asset('/webStatic/library/jquery.validation/1.14.0/validate-methods.js')}}"></script>
<script src="{{asset('webStatic/library/jquery.form/jquery.form.js')}}" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="{{asset('/webStatic/library/jquery-calendar/js/laydate.js')}}"></script>
<script type="text/javascript" src="{{asset('/webStatic/library/editable-select/jquery.editable-select.min.js')}}"></script>
<script type="text/javascript" src="{{asset('/webStatic/library/jqueryJson/jquery.json.js')}}"></script>

<script type="text/javascript">/*选择品名*/
laydate.skin('molv');



$(".moreName").on("click", function() {
	var that=$(this);
			layer.open({
				fixed: false,
				title: false,
				area: ['919px', '500px'], //宽高
				moveOut:true,
				content: '<div class="box">' +
				'<div class="head">添加品名</div>' +
				'<div class="className">' +
				'<span category_id="0">全部</span>'+
				"@foreach($product_category as $item)<span category_id='{{$item['category_id']}}'>{{$item['category_name']}}</span>@endforeach"+
				'</div><div class="productinfo"><div class="imgposition"><img src="{{asset('webStatic/images/moreclass.png ')}}"/></div><ul class="productNameul"></ul><ul class="nameul"></ul><ul class="choose_spec_name"></ul></div></div>',
				success: function() {
					$(".className span").eq(0).addClass("actived");
					$(".nameul").eq(0).show().siblings().not(".imgposition").not(".productNameul").not(".choose_spec_name").hide();
					$(".className span").on("click", function() {
						$(this).addClass("actived").siblings().removeClass("actived");
						var getdata = $(this).attr("data");
						/*	console.log(getdata)*/
						$(".nameul").eq(getdata).show().siblings().not(".imgposition").not(".productNameul").not(".choose_spec_name").hide();

						getprocuct($(this), $(this).attr("category_id"));
					})
					var heightauto = true;

					var productname;
					var productprice;
					$(".imgposition").on("click", function() {

						if(heightauto) {
							heightauto = false;
						} else {
							heightauto = true;
						}
						if(heightauto) {
							$(".className").css("height", "55px");
						} else {
							$(".className").css("height", "auto")
						}
					})

					getprocuct("elm", 0);

					function getprocuct(elm, id) {
						$.ajax({
							type: "post",
							url: "{{url('product/ajax/list')}}",
							async: true,
							data: {
								category_id: id,
								_token: '{{csrf_token()}}'
							},
							success: function(res) {
								var resData = JSON.parse(res)
								//console.log(resData);
								var myData = resData.data;

								$(".nameul").empty();
								for(var i in myData) {
									$(".nameul").append('<li datai="'+i+'" pricedata="'+myData[i].product_id+'"><img src="/uploads/'+myData[i].product_thumb+'"  onerror="this.src=`{{asset('webStatic/images/noimg.png ')}}`"/><span id="">' + myData[i].product_name + '</span></li>')
									
									
								}
								
							
									/*for(var k in myData[0].product_spec){
										$(".choose_spec_name").append('<li><img src="/uploads/'+myData[0].product_spec[k].image_thumb +'"  onerror="this.src=`{{asset('webStatic/images/noimg.png ')}}`"/><span>' + myData[0].product_spec[k].spec_name + '</span></li>')
									}*/
							
								
											var pinmingdata="";
											var specname="";
											 $(".layui-layer-btn0").hide();
								$(".nameul li").on("click", function() {
									$(this).css("border", "1px solid #fe8d01").siblings().css("border", "1px solid #dddddd")
									productname = $(this).find("span").text();
									productprice = $(this).attr("pricedata");
									/*	 console.log(productname)*/
									$(".choose_spec_name").empty();
									var datai=$(this).attr("datai");
									
									for(var k in myData[datai].product_spec){
										$(".choose_spec_name").append('<li unit="' + myData[datai].product_spec[k].spec_unit + '"><img src="/uploads/'+myData[datai].product_spec[k].image_thumb +'"  onerror="this.src=`{{asset('webStatic/images/noimg.png ')}}`"/><span>' + myData[datai].product_spec[k].spec_name + '</span></li>')
									}	
								
									pinmingdata=productname;
									
								$(".choose_spec_name li").on("click",function(){
									$(this).css("border", "1px solid #fe8d01").siblings().css("border", "1px solid #dddddd");
									
									pinmingdata=pinmingdata;
									specname=$(this).find("span").text();
								
									 my_unit=$(this).attr("unit");
								
								 $(".layui-layer-btn0").show();
								
								})
								
								
								})
								
								
								
								$(".layui-layer-btn0").on("click", function() {
								that.siblings('.product_name').val(pinmingdata);
								
								that.siblings(".form_spec_name").text(specname);
									/*$("#product_price").val(productprice);*/
									that.parent().parent().find(".my_unit").text(my_unit);
										
								
									
								})
							},
							complete: function() {

							}
						});
					}

				}
			});
		})
				$(".moreaddxuiqu").click(function() {
						$(".xuqiuparent").eq(0).clone(true).prependTo(".xuqiugrandpa");
						addId();
					})
				addId();
					function addId(){
							$(".army_receive_time").each(function(i,index){
								$(index).attr("id","army_receive_time"+i);
									
								var dateId=$(index).attr("id");
						
						
							})
					}
					
						
						$(".army_receive_time").on("click",function(){
								
								
								laydate({
											format: 'YYYY-MM-DD hh:mm:ss',
											istime: true, 
											min: laydate.now(),
											elem:'#'+$(this).attr("id")
										})	
							
								
						})
						
					
					
					
					
					
$(function() {
	
	
		
	




	var arr = [];

	function jsonData() {
		$('.xuqiuparent').each(function(i, index) {
			var obj = new Object();
			obj.product_number = $(".product_number").eq(i).val();
			obj.army_receive_time = $(".army_receive_time").eq(i).val();
			obj.army_contact_person = $(".army_contact_person").eq(i).val();
			obj.army_contact_tel = $(".army_contact_tel").eq(i).val();
			obj.army_note = $(".army_note").eq(i).val();
			obj.product_name = $(".product_name").eq(i).val();
			obj.spec_name=$(".form_spec_name").eq(i).text();
		/*	$(".form_spec_name").eq(i).text()*/
			

			arr.push(obj)

		})
		strjson = JSON.parse($.toJSON(arr));

		spec_json = JSON.stringify(strjson);
	

	}
	function jsonData2() {
	
		$('.xuqiuparent').each(function(i, index) {
			var obj = new Object();
			obj.product_number = $(".product_number").eq(i).val();
			obj.army_receive_time = $(".army_receive_time").eq(i).val();
			obj.army_contact_person = $(".army_contact_person").eq(i).val();
			obj.army_contact_tel = $(".army_contact_tel").eq(i).val();
			obj.army_note = $(".army_note").eq(i).val();
			obj.product_name = $(".product_name").eq(i).val();
			obj.spec_name=$(".form_spec_name").eq(i).text();
			arr.push(obj)

		})
		strjson = JSON.parse($.toJSON(arr));

		spec_json = JSON.stringify(strjson);
			
			

	}
	
	
	
	
	

	var addspec = $("#arym_form").validate({
		rules: {
			product_number: {
				required: true,
				min: 0.01,
				number: true,

			},
			army_receive_time: {
				required: true,
			},
			product_name: {
				required: true
			},
			spec_name: {
				required: true
			}
		},
		messages: {
			product_number: {
				required: "请输入商品数量",
				min: "最小值为0.01"

			},
			army_receive_time: {
				required: "请选择到货时间",

			},
			product_name: {
				required: "请输入商品名称",
			},
			spec_name: {
				required: "请选择分类"
			}

		},
		errorLabelContainer: $("div.errordata"),
		wrapper: "li",
		submitHandler: function(form) {
			
			@if(!empty($cart_order))
				jsonData()
			@else
				jsonData2()
			@endif
			
			
			$(form).ajaxSubmit({
				url: '{{url("army/need/release")}}',
				type: 'POST',
				dataType: 'JSON',
				data: {
					order_json: spec_json,
					_token: '{{csrf_token()}}'
				},
				beforeSend: function(res) {

					$(".ary-submit").attr("disabled", "true");

				},
				success: function(res) {
					//console.log(res);

					if(res.code == 0) {
						layer.msg(res.messages, {
							icon: 1,
							time: 1000
						}, function() {
							var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
							parent.layer.close(index); //再执行关闭
							parent.location.reload();
							$(".ary-submit").removeAttr("disabled");

						});
						addspecstate = true;
					} else {

						layer.msg(res.messages, {
							icon: 2,
							time: 1000
						}, function() {
							$(".ary-submit").removeAttr("disabled");
						});
					}
				}
			});
		}

	});
		$(".ary-reset").on("click",function(){
     	   addspec.resetForm();
     	
        });
})</script>
@endsection

