@extends('layouts.master')

@section('MyCss')
 <link rel="stylesheet" href="{{asset('webStatic/css/provider.css')}}">
<style>
	h1{
		font-size: 24px;
		height: 80px;
		color: #0e99dc;
		line-height: 80px;
		border-bottom: 1px solid #dddddd;
	}
	.submit{
		width: 210px;
		height: 64px;
		line-height: 64px;
		background: #FE8D01;
		margin:100px auto 0 auto ;
		text-align: center;
		font-size: 20px;
		color: #FFFFFF;
		cursor: pointer;
	}
</style>
@endsection
@section('content')
	<div style="padding: 0 40px;">
		<h1>确认到货</h1>
	<table style="width: 100%;">
		 @foreach($offer_list as $item)
	  <tr>
	  	<td>{{$item['user_info']['nick_name']}}</td>
	  	<td>到货时间：{{$item['platform_receive_date']}}</td>
	  	<td>应到货：{{$item['product_number']}}{{$order_info['spec_unit']}}</td>
	  	@if($item['status']=="2")
	  	<td>待发货</td>
	  	@elseif($item['status']=="3")
	  	<td><a onclick="confirm(this,{{$item['offer_id']}},{{$item['order_id']}})">确认到货</a></td>
	  	@elseif($item['status']=="4")
	  	<td>已到货</td>
	  		@endif
	  </tr>
  	   @endforeach
	</table>
	 <div class="submit">提交</div>
  	 </div>
@endsection
@section('MyJs')
	<script>
		function confirm(elm,offer_id,order_id){
			$.ajax({
		  			type:'post',
		  			data:{
		  				offer_id:offer_id,
		  				order_id:order_id,
	  					_token:'{{csrf_token()}}'
		  			},
		  			url:'{{url('platform/confirm/receive')}}',
		  			async:true,
		  			success: function (resData) {
		  				var res=JSON.parse(resData)
		  			
		            if(res.code==0){
		             	   layer.msg(res.messages, {icon: 1, time: 1000},function(){
		             	   	location.reload();
		             	   });
		             	
			         index=parent.layer.getFrameIndex(window.name);
					/*setTimeout(function(){
						parent.layer.close(index);
		             	layer.closeAll('')
					},1200)*/
						
		             }else{
		             	   layer.msg(res.messages, {icon: 2, time: 1000});
		             }
		            }
		  		});
		}
		
		$(".submit").click(function(){
			parent.location.reload();
		})
	</script>
@endsection