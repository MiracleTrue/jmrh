@extends('layouts.master')

@section('MyCss')
<link rel="stylesheet" href="{{asset('webStatic/css/terrace.css')}}">

@endsection
@section('content')
<div style="height: 50px;float: right;padding-right: 20%;line-height: 50px;font-size: 14px;">
	<input style="width: 120px; margin-left: 10px;"  autocomplete="off" style="margin-left: 15px;" onClick="laydate({format: 'YYYY-MM-DD' })" class="laydate-icon tre-time start_time"  name="army_receive_time" id="army_receive_time"  placeholder="请选择日期"/>
				<span>-</span>
				<input style="width: 120px;margin-left: 0;"  autocomplete="off" style="margin-left: 15px;" onClick="laydate({format: 'YYYY-MM-DD' })" class="laydate-icon tre-time end_time"  name="army_receive_time" id="army_receive_time"  placeholder="请选择日期"/>
	
	<a onclick="biaoge(this)" style="color: blue;">导出表格到本地</a><a style="margin-left: 20px;color: blue;">打印</a></div>
<table style="width: 100%;">
	<tbody>
		<tr class="tr1">
			<th style="width: 6%;"><span>序号</span></th>
			<th style="width: 15%;"><span>交易时间</span></th>
			<th style="width: 17%;"><span>供应商名称</span></th>
			<th style="width: 20%;"><span>订单号</span></th>
			<th style="width: 12%;"><span>品名</span></th>
			<th style="width: 10%;"><span style="">单价</span></th>
			<th style="width: 12%;"><span style="">数量</span></th>
			<th style=""><span style="">总价</span></th>
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

		</tr>
  @endforeach
	</tbody>
</table>

@endsection

@section('MyJs')
<script type="text/javascript" src="{{asset('/webStatic/library/jquery-calendar/js/laydate.js')}}"></script>
<script>
		laydate.skin('molv');
	function biaoge(){
		var start_date=$(".start_time").val();
		var end_date=$(".end_time").val();
		if(start_date=="" && end_date==""){
			alert("时间选择不能为空")
			
		}else{
			location.href="{{url('platform/statistics/output/excel')}}"+"/"+start_date+"/"+end_date
			
		}
		
	
		
	}
</script>
@endsection

