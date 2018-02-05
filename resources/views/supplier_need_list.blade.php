@extends('layouts.master')

@section('MyCss')
    <link rel="stylesheet" href="{{asset('webStatic/css/provider.css')}}">
    	<link rel="stylesheet" href="{{asset('webStatic/css/page.css')}}">
    	<style type="text/css">
 
    	</style>
@endsection
@section('content')
<section>
		<div class="refresh" style="top: 142px;">
  			<img src="{{asset('webStatic/images/refresh.png')}}" />
  		</div>
			<div style="line-height: 36px;margin-bottom: 20px;">
				
				<div class="pvr-shaixuan">
					<select name="" class="pvr-state">
						<option value="null" >全部</option>
						<option value="待回复"   @if($page_search['status'] == '待回复') selected="selected" @endif>待回复</option>
						<option value="待确认" @if($page_search['status'] == '待确认') selected="selected" @endif>待确认</option>
						<option value="待发货" @if($page_search['status'] == '待发货') selected="selected" @endif>待发货</option>
						<option value="已发货" @if($page_search['status'] == '已发货') selected="selected" @endif>已发货</option>
						<option value="已收货" @if($page_search['status'] == '已收货') selected="selected" @endif>已收货</option>
						<option value="已拒绝" @if($page_search['status'] == '已拒绝') selected="selected" @endif>已拒绝</option>
						<option value="已过期" @if($page_search['status'] == '已过期') selected="selected" @endif>已过期</option>
						

					</select>
					
				 	<input  autocomplete="off" class="pvr-time laydate-icon" name="cre_time" id="cre_time" onClick="laydate({istime: true, format: 'YYYY-MM-DD'})"   name="army_receive_time" id="army_receive_time" value=@if($page_search['create_time']=="null") "" @else "{{$page_search['create_time']}}"@endif placeholder="请选择日期"/>
				</div>
				<a class="pvr-btn">搜索</a>
				
			</div>
			<div>
				<input style="width: 120px; margin-left: 10px;"  autocomplete="off" style="margin-left: 15px;" onClick="laydate({format: 'YYYY-MM-DD' })" class="laydate-icon tre-time start_time"  name="army_receive_time" id="start_time"  placeholder="请选择日期"/>
				<span>-</span>
				<input style="width: 120px;margin-left: 0;"  autocomplete="off" style="margin-left: 15px;" onClick="laydate({format: 'YYYY-MM-DD' })" class="laydate-icon tre-time end_time"  name="army_receive_time" id="end_time"  placeholder="请选择日期"/>
				<a onclick="biaoge(this)" style="margin-left: 10px;color: blue;font-size: 14px;">导出表格到本地</a>	
			</div>
			<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 9%;"><span>序号</span></th>
						<th style="width: 18%;"><span>订单号</span></th>
						<th style="width: 10%;"><span>品名</span></th>
						<th style="width: 12%;"><span>到货时间</span></th>
						<th style="width: 14%;"><span style="">数量</span></th>
						<th style="width: 14%;"><span style="">报价</span></th>

						<th style=""><span style="">操作</span></th>
					</tr>
					  @foreach($offer_list as $item)
					<tr>
						<td>{{$item->offer_id}}</td>
						<td>{{$item['order_info']['order_sn']}}</td>
						<td>{{$item['order_info']['product_name']}}</td>
						<td>{{$item['platform_receive_date']}}</td>
						<td>{{$item['order_info']['product_number']}}{{$item['order_info']['spec_unit']}}</td>
						<!--<td>@if($item->status_text=="等待通过"){{$item->total_price}}元 @else{{$item->status_text}} @endif </td>-->
						<td>{{$item['status_text']}}</td>
						<td class="blueWord">
							@if($item['status'] == '0')
							<a class="pvr-caozuo" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  @elseif($item['status'] == '1')
							  	<a class="pvr-caozuo" style="" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  	  @elseif($item['status'] == '2')
							  	  <a class="pvr-caozuo" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  	  	<a class="pvr-caozuo" style="color: #4284c5;"onclick="SendGoods(this,{{$item->offer_id}})" >准备配货</a>
							  	  	  @elseif($item['status'] == '3')
							  	  	  <a class="pvr-caozuo" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  	  	  <a class="pvr-caozuo" >已发货</a>
							  	  	  @elseif($item['status'] == '4')
							  	  	  <a class="pvr-caozuo" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  	  	  <a class="pvr-caozuo"style="color: #333;"></a>
							  	  	   @elseif($item['status'] == '-1')
							  	  	   <a class="pvr-caozuo" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  	  	  <a class="pvr-caozuo"style="color: #333;"></a>
							  	  	    @elseif($item['status'] == '10')
							  	  	    <a class="pvr-caozuo" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  	  	  <a class="pvr-caozuo"style="color: #333;"></a>
							 	@endif
							 	
							 	
							 	<a>打印</a>	
						</td>
					</tr>
					
 @endforeach
				</tbody>
			</table>

			 @include('include.inc_pagination',['pagination'=>$offer_list])

		</section>
@endsection

@section('MyJs')
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery-calendar/js/laydate.js')}}"></script>
  <script>
  	function biaoge(){
		var start_date=$(".start_time").val();
		var end_date=$(".end_time").val();
		if(start_date=="" || end_date==""){
			alert("时间选择不能为空")
			
		}else{
			location.href="{{url('supplier/output/excel')}}"+"/"+start_date+"/"+end_date
			
		}
		
	
		
	}
  		!function(){

			laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库
		
			laydate({elem: '#army_receive_time'});//绑定元素
		
		}();
	/*刷新*/
$(".refresh").on("click",function(){
	location.reload();
})	
		
		
		
  	function supplierView(elm,offer_id){
  		  layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['660px' , '700px'],
		      fixed :false,
		      content: '{{url('supplier/offer/view')}}'+"/"+offer_id
		    });
  	}
  	
  	
  	  function SendGoods(elm,offer_id){
		  	if (confirm("确认配货吗？")){
		  		$.ajax({
		  			type:'post',
		  			data:{
		  				offer_id:offer_id,
	  					_token:'{{csrf_token()}}'
		  			},
		  			url:'{{url('supplier/send/product')}}',
		  			async:true,
		  			success: function (resData) {
		  				var res=JSON.parse(resData)
		  			
		            if(res.code==0){
		             	   layer.msg(res.messages, {icon: 1, time: 1000},function(){
		             	   	location.reload();
		             	   });
		             	
			        var index=parent.layer.getFrameIndex(window.name);
					setTimeout(function(){
						parent.layer.close(index);
		             	layer.closeAll('')
					},1200)
						
		             }else{
		             	   layer.msg(res.messages, {icon: 2, time: 1000});
		             }
		            }
		  		});
		  	}
		 	
		  }
  
  
  	
  	//搜索
  	
  	  $(".pvr-btn").on("click",function(){
    	
    	 if($(".pvr-time").val()==""){
	    	var time=null;
	    }else{
	    	var time=$(".pvr-time").val();
	    }
    	
	   var staus_val = $('.pvr-state option:selected').val();
    
    	var url="{{url('supplier/need/list')}}"+"/"+staus_val+"/"+time;
    	
    	location.replace(url);
    });
  </script>
  @endsection
