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
						<input type="text" name="product_name" class="product_name" value="{{$item['product_name']}}" />
						<img class="moreName" style="position: absolute;left: 360px;top: 10px;"  src="{{asset('webStatic/images/morepinming.png')}}" alt="选择品名" />
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
						<input type="text" class="army_contact_person" name="" id="" value="{{$item['contact_person']}}" />
					</p>
					<p class="div_floatright">
						<span>电话</span>
						<input type="text"class="army_contact_tel" value="{{$item['contact_tel']}}"/>
					</p>

				</div>

				<div class="ary_adddiv">

					<p class="div_floatright">
						<span>到货时间</span>
						<input style="width: 286px;"  autocomplete="off" onClick="laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss',min: laydate.now()})" class="laydate-icon army_receive_time"  name="army_receive_time"  value="{{$item['army_receive_date']}}" placeholder="请选择日期(必须大于现在时间)"/>
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
						<input type="text" name="product_name" class="product_name" value="" />
						<img class="moreName" style="position: absolute;left: 360px;top: 10px;"  src="{{asset('webStatic/images/morepinming.png')}}" alt="选择品名" />
					</p>
					<p class="div_floatleft" style="text-indent: 15px;">
						<span>数量</span>
						<input type="text" name="product_number" class="product_number" value="" />
					</p>
				</div>
				<div class="ary_adddiv">
					<p class="div_floatleft">
						<span>联系人</span>
						<input type="text" class="army_contact_person" name="" id="" value="" />
					</p>
					<p class="div_floatright">
						<span>电话</span>
						<input type="text"class="army_contact_tel"/>
					</p>

				</div>
				<div class="ary_adddiv">

					<p class="div_floatright">
						<span>到货时间</span>
						<input autocomplete="off" type="text" onClick="laydate({format: 'YYYY-MM-DD hh:mm:ss',istime: true})" class="laydate-icon army_receive_time"  name="army_receive_time" id="" value="{{$order_info['army_receive_time'] or ''}}" id style="width: 286px;" placeholder="请选择日期(必须大于现在时间)"/>
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
{{--$(".moreName").on("click", function() {
			layer.open({
				fixed: false,
				title: false,
				area: ['919px', '500px'], //宽高
				content: '<div class="box"><div class="head">添加品名</div><div class="className"><span category_id="0">全部</span>@foreach($product_category as $item)<span category_id="{{$item['
				category_id ']}}">{{$item['
				category_name ']}}</span>@endforeach</div><div class="productinfo"><div class="imgposition"><img src="{{asset('
				webStatic / images / moreclass.png ')}}"/></div><ul class="productNameul"></ul><ul class="nameul"></ul></div></div>',
				success: function() {
					$(".className span").eq(0).addClass("actived");
					$(".nameul").eq(0).show().siblings().not(".imgposition").not(".productNameul").hide();
					$(".className span").on("click", function() {
						$(this).addClass("actived").siblings().removeClass("actived");
						var getdata = $(this).attr("data");
						/*	console.log(getdata)*/
						$(".nameul").eq(getdata).show().siblings().not(".imgposition").not(".productNameul").hide();

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

								var myData = resData.data;

								$(".nameul").empty();
								for(var i in myData) {
									$(".nameul").append('<li pricedata="' + myData[i].product_price + '"><img src="/uploads/' + myData[i].product_thumb + '"  onerror="this.src=`{{asset('
										webStatic / images / noimg.png ')}}`"/><span id="">' + myData[i].product_name + '</span></li>')
								}

								$(".productinfo li").on("click", function() {
									$(this).css("border", "1px solid #fe8d01").siblings().css("border", "1px solid #dddddd")
									productname = $(this).find("span").text();
									productprice = $(this).attr("pricedata");
									/*	 console.log(productname)*/
								})
								$(".layui-layer-btn0").on("click", function() {
									$("#product_name").val(productname);
									$("#product_price").val(productprice);
								})
							},
							complete: function() {

							}
						});
					}

				}
			});
		})--}}

$(function() {
	
	$(".moreaddxuiqu").click(function() {
		$(".xuqiuparent").eq(0).clone().prependTo(".xuqiugrandpa");
	})

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
			obj.spec_name = $(".spec_name").eq(i).val();

			arr.push(obj)

		})
		strjson = JSON.parse($.toJSON(arr));

		spec_json = JSON.stringify(strjson);
		/*	console.log(spec_json);*/

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
			jsonData();
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
					console.log(res);

					if(res.code == 0) {
						layer.msg(res.messages, {
							icon: 1,
							time: 1000
						}, function() {
							var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
							parent.layer.close(index); //再执行关闭
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

