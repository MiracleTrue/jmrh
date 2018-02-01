@extends('layouts.master')

@section('MyCss')
  <style>
  	#box{
  		padding: 0 40px;
  		
  	}
  		#box h1{
  			height: 85px;
  		border-bottom: 1px solid #dddddd;
  		font-size: 24px;
  		color: #0e99dc;
  		line-height: 85px;
  		}
  		.biaoti{
  			width: 104px;
  			height: 40px;
  			line-height: 40px;
  			text-align: center;
  			background: #feb501;
  			color: #FFFFFF;
  			border-radius: 20px;
  			font-size: 14px;
  			display: inline-block;
  		}
  </style>
@endsection
@section('content')
<div id="box">
	<h1>详细信息</h1>
	<div style="overflow: hidden;">
		<div style="height: 85px;line-height: 85px;"><span class="biaoti" style="">军方备注</span></div>
		<div style="font-size: 14px;">{{$order_info['army_note']}}</div>
	</div>
	<!--操作日志-->
	<div>
		<p class="biaoti" style="margin-top: 40px;margin-bottom: 20px;">操作日志</p>
			  @foreach($log_list as $item)
			  <p style="line-height: 32px;font-size: 14px;"><span>{{$item['create_date']}}</span><span>{{$item['log_desc']}}</span></p>
			   @endforeach
	</div>
	
</div>
			
@endsection
 
@section('MyJs')

@endsection

