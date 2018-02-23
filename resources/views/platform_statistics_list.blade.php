@extends('layouts.master')

@section('MyCss')
<link rel="stylesheet" href="{{asset('webStatic/css/terrace.css')}}">
	<link rel="stylesheet" media='print' href="{{asset('webStatic/css/terrace.css')}}">
	<link rel="stylesheet" type='text/css' href="{{asset('webStatic/css/print2.css')}}">
		<link rel="stylesheet" type='text/css' media='print' href="{{asset('webStatic/css/print.css')}}">
<style>
	.daochubiaoge{
	width: 160px;
	height: 38px;
	text-align: center;
	line-height: 38px;
	background: #feb501;
	border-radius: 20px;
	display: inline-block;
	color: #FFFFFF;
}
.printdingdan,.printAll,.cancelprint,.tongji{
	width: 90px;
	height: 38px;
	text-align: center;
	line-height: 38px;
	background: #F3570D;
	border-radius: 20px;
	color: #FFFFFF;	
	display: inline-block;
}
</style>
@endsection
@section('content')
<div style="height: 50px;float: right;padding-right: 20%;line-height: 50px;font-size: 14px;">
	<input style="width: 120px; margin-left: 10px;"  autocomplete="off" style="margin-left: 15px;" onClick="laydate({format: 'YYYY-MM-DD',elem:'#start_time' })" class="laydate-icon tre-time start_time"  name="army_receive_time" id="start_time"  placeholder="请选择日期"/>
				<span>-</span>
				<input style="width: 120px;margin-left: 0;"  autocomplete="off" style="margin-left: 15px;" onClick="laydate({format: 'YYYY-MM-DD',elem:'#end_time' })" class="laydate-icon tre-time end_time"  name="army_receive_time" id="end_time"  placeholder="请选择日期"/>
	
			<a onclick="biaoge(this)" style="">导出表格到本地</a>
			
			<a class="tongji" onclick="tongji(this)" style="margin-left: 20px;">统计</a>
			
			<a class="printdingdan" style="margin-left: 10px;font-size: 14px;" >打印</a>
			<a class="printAll" style="margin-left: 10px;font-size: 14px;background: #0e99dc;">确认打印</a>
			<a class="cancelprint" style="margin-left: 10px;font-size: 14px;">取消打印</a>
		
			</div>
			
<table style="width: 85%;margin-left: 40px;">
	<tbody>
		<tr class="tr1">
			<th style="width: 6%;"><span>序号</span></th>
			<th style="width: 15%;"><span>交易时间</span></th>
			<th style="width: 14%;"><span>供应商名称</span></th>
			<th style="width: 20%;"><span>订单号</span></th>
			<th style="width: 12%;"><span>品名</span></th>
			<th style="width: 10%;"><span style="">单价</span></th>
			<th style="width: 12%;"><span style="">数量</span></th>
			<th style=""><span style="">总价</span></th>
			<th style=""><span style="">操作</span></th>
		</tr>
  @foreach($list as $item)
		<tr>
			<td>{{$item->order_id}}</td>
			<td>{{$item->create_date}}</td>
			<td>{{$item->user_info['nick_name']}}</td>
			<td>{{$item->order_info['order_sn']}}</td>
			<td>{{$item->order_info['product_name']}}</td>
			<td>{{$item->price}}元/{{$item->order_info['spec_unit']}}</td>
			<td>{{$item->product_number}}{{$item->order_info['spec_unit']}}</td>
			<td>{{$item->total_price}}元</td>
			<td><a style="margin-left: 5px;" onclick="print(this,'{{$item['offer_id']}}')">打印</a>
            <a style="margin-left: 5px;float: right;margin-right: 5px;">
            <input type="checkbox" class="printcheck" name="" id="" value="" order_id="{{$item['offer_id']}}"/></a></td>

		</tr>
  @endforeach
	</tbody>
</table>
	<div id="myprint">
			
		</div>
@endsection

@section('MyJs')
<script type="text/javascript" src="{{asset('/webStatic/library/jquery-calendar/js/laydate.js')}}"></script>
	<script src="http://www.jq22.com/jquery/jquery-migrate-1.2.1.min.js"></script>
 <script type="text/javascript" src="{{asset('/webStatic/library/jquery.jqprint/jquery.jqprint-0.3.js')}}"></script>
<script>
		laydate.skin('molv');
	function biaoge(){
		var start_date=$(".start_time").val();
		var end_date=$(".end_time").val();
		if(start_date=="" || end_date==""){
			alert("时间选择不能为空")
			
		}else{
			location.href="{{url('platform/statistics/output/excel')}}"+"/"+start_date+"/"+end_date
			
		}
		
	
		
	}
	function tongji(){
		var start_date=$(".start_time").val();
		var end_date=$(".end_time").val();
		var url="{{url('platform/statistics/list')}}"+"/"+start_date+"/"+end_date
		location.replace(url);
		
	}
	
	
	
	var printData="";
		var allPrintData="";
		$(".printcheck").hide();
		$(".printAll").hide();
		$(".cancelprint").hide();
		
		
		$(".cancelprint").click(function(){
			$(".printAll").hide();
			$(".printdingdan").show();
			$(this).hide();
			$(".printcheck").hide();
			
		})
		
		$(".printdingdan").click(function(){
			$(".printcheck").show();
			$(this).hide();
			$(".cancelprint").show();
			
			$(".printAll").show();
		})
		
		$(".printAll").click(function(){
			
					$(".printcheck").each(function(i,index){
								if($(index).is(":checked")){
									printData += $(index).attr("order_id")+",";
								}
						
							
						})
					
						if(printData.length>0){
						 	 allPrintData=printData.substr(0,printData.length-1);
						 }	
				
				
						$.ajax({
						type:"post",
						url:"{{url('platform/statistics/output/print')}}",
						async:true,
						data:{
					    		offer_ids:allPrintData,
					    		_token:'{{csrf_token()}}'
					    	},
						success:function(resData){
							printData="";
							var data=JSON.parse(resData)
							console.log(data)
							var mydata=data.data;
							$("#myprint").empty();
							for(var i in mydata){
								$("#myprint").append('<table style="width: 800px;" class="printone"><tbody><tr style="border: 1px solid #333333;"><td style="width: 10%;border-right:1px solid #333333 ;">序号</td><td style="width: 20%;border-right:1px solid #333333 ;">'+mydata[i].order_id+'</td><td style="border-right:1px solid #333333 ;width: 20%;" cellspacing="20px">订单编号</td><td>'+mydata[i].order_info.order_sn+'</td></tr><tr style="border: 1px solid #000000;"><td>商品名称</td><td>'+mydata[i].order_info.product_name+'</td><td>商品数量</td><td>'+mydata[i].order_info.product_number+''+mydata[i].order_info.spec_unit+'</td></tr><tr style="border: 1px solid #000000;"><td>商品单价</td><td>'+mydata[i].price+'元</td><td style="border-right:1px solid #333333;">商品总价</td><td colspan="3">'+mydata[i].total_price+'元</td></tr><tr style="border: 1px solid #000000;"><td>订单创建时间</td><td>'+mydata[i].create_date+'</td><td>商品规格</td><td>'+mydata[i].order_info.spec_name+'</td></tr><tr style="border: 1px solid #000000;"><td>订单状态</td><td colspan="3">'+mydata[i].status_text+'</td></tr><tr style="border: 1px solid #000000;"><td style="border:1px solid #333333 ;" rowspan="2">备注</td><td style="border:1px solid #333333 ;" colspan="3" rowspan="2"></td></tr><tr></tr></tbody></table>')
							}
							
							allprint();	
						}
					});
			
			
			
		})
	
		function print(elm,orer_id){
			$.ajax({
						type:"post",
						url:"{{url('platform/statistics/output/print')}}",
						async:true,
						data:{
					    		offer_ids:orer_id,
					    		_token:'{{csrf_token()}}'
					    	},
						success:function(resData){
							var data=JSON.parse(resData)
							console.log(data)
							var mydata=data.data;
							$("#myprint").empty();
							for(var i in mydata){
								$("#myprint").append('<table style="width: 800px;" class="printone"><tbody><tr style="border: 1px solid #333333;"><td style="width: 10%;border-right:1px solid #333333 ;">序号</td><td style="width: 20%;border-right:1px solid #333333 ;">'+mydata[i].order_id+'</td><td style="border-right:1px solid #333333 ;width: 20%;" cellspacing="20px">订单编号</td><td>'+mydata[i].order_info.order_sn+'</td></tr><tr style="border: 1px solid #000000;"><td>商品名称</td><td>'+mydata[i].order_info.product_name+'</td><td>商品数量</td><td>'+mydata[i].order_info.product_number+''+mydata[i].order_info.spec_unit+'</td></tr><tr style="border: 1px solid #000000;"><td>商品单价</td><td>'+mydata[i].price+'元</td><td style="border-right:1px solid #333333;">商品总价</td><td colspan="3">'+mydata[i].total_price+'元</td></tr><tr style="border: 1px solid #000000;"><td>订单创建时间</td><td>'+mydata[i].create_date+'</td><td>商品规格</td><td>'+mydata[i].order_info.spec_name+'</td></tr><tr style="border: 1px solid #000000;"><td>订单状态</td><td colspan="3">'+mydata[i].status_text+'</td></tr><tr style="border: 1px solid #000000;"><td style="border:1px solid #333333 ;" rowspan="2">备注</td><td style="border:1px solid #333333 ;" colspan="3" rowspan="2"></td></tr><tr></tr></tbody></table>')
							}
							
							allprint();	
						}
					});
		}	
				
	
	

	
	function allprint(elm){
		$("#myprint").jqprint({
		     debug: false, //如果是true则可以显示iframe查看效果（iframe默认高和宽都很小，可以再源码中调大），默认是false
		     importCSS: true, //true表示引进原来的页面的css，默认是true。（如果是true，先会找$("link[media=print]")，若没有会去找$("link")中的css文件）
		     printContainer: true, //表示如果原来选择的对象必须被纳入打印（注意：设置为false可能会打破你的CSS规则）。
		     operaSupport: true//表示如果插件也必须支持歌opera浏览器，在这种情况下，它提供了建立一个临时的打印选项卡。默认是true
		});
		
	}
</script>
@endsection

