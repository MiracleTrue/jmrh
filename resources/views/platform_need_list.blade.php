@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
   
    <link rel="stylesheet" href="{{asset('webStatic/css/terrace.css')}}">
    	<link rel="stylesheet" href="{{asset('webStatic/css/page.css')}}">
<style>
	

.plat_shanixuan{
	width: 85px;height: 36px;line-height: 36px;color: #fff;font-size: 16px;text-indent: 18px;background-color: #fe8d01;float: left;
	border-radius:20px ;
}
.plat_stauschoose{
	float: left;
	background: #DDDDDD;
	border-radius:20px ;
	margin-left: -28px;
	padding-right: 20px;
}
.platshaixuan{
	width: auto;
}
</style>
@endsection
@section('content')

      <section>
			<div>
				<a href="#" class="tre-tianjia"></a>
				<div class="tre-shaixuan platshaixuan" style="background: none;">
					<div class="plat_shanixuan" >筛选</div>
					<div class="plat_stauschoose" >
						<span style="margin-left: 24px;font-size: 16px;">类型</span>
					<select name="" class="tre-state palt type_val" style="margin-left: 15px;">
					 <option value="0">全部</option>
                      <option value="2" @if($page_search['type'] == '2') selected="selected" @endif>平台</option>
                        <option value="1" @if($page_search['type'] == '1') selected="selected" @endif>军方</option>
						
					</select>
					<span style="margin-left: 24px;font-size: 16px;">状态</span>
					<select name="" class="tre-state staus_val" style="margin-left: 15px;">
						<option value="null"  @if($page_search['status'] == 'null') selected="selected" @endif>全部</option>
						<option value="待分配"  @if($page_search['status'] == '待分配') selected="selected" @endif>待分配</option>
						<option value="已分配"  @if($page_search['status'] == '已分配') selected="selected" @endif>已分配</option>
						<option value="库存供应"  @if($page_search['status'] == '库存供应') selected="selected" @endif>库存供应</option>
						<option value="交易成功"  @if($page_search['status'] == '交易成功') selected="selected" @endif>交易成功</option>
					</select>
					<span style="margin-left: 24px;font-size: 16px;">分配时间</span>
					<input style="margin-left: 15px;"value= @if($page_search['create_time']=="null") "" @else "{{$page_search['create_time']}}"@endif onClick="laydate({istime: true, format: 'YYYY-MM-DD' })" class="laydate-icon tre-time"  name="army_receive_time" id="army_receive_time"  placeholder="请选择日期"/>
					
					
					
					</div>
				</div>
				<a class="tre-btn">搜索</a>
			</div>
			
				<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 6%;"><span>序号</span></th>
						<th style="width: 15%;"><span>订单号</span></th>
						<th style="width: 7%;"><span>品名</span></th>
						<th style="width: 12%;"><span>军方到货时间</span></th>
						<th style="width: 12%;"><span style="">平台到货时间</span></th>
						<th style="width: 12%;"><span style="">数量</span></th>
						<th style="width: 12%;"><span style="">状态</span></th>
						<th style="width: 18%;""><span style="">操作</span></th>
					</tr>
					  @foreach($order_list as $item)
					<tr>
						<td>{{$item->order_id}}</td>
						<td>{{$item->order_sn}}</td>
						<td>{{$item->product_name}}</td>
						<td>{{$item->army_receive_time}}</td>
						<td>{{$item->platform_receive_time}}</td>
						<td>{{$item->product_number}}{{$item->product_unit}}</td>
						<td>{{$item->status_text}}</td>
						<td class="blueWord">
							@if($item['status'] == '0' || $item['status'] == '1' )
							<a class="tre-caozuo platfenpei" onclick="fenpei(this,'{{$item->order_id}}')">分配</a>
							<a style="margin-left: 5%;" onclick="InventorySupply(this,'{{$item->order_id}}')">库存供应</a>
						  @elseif($item['status'] == '100' || $item['status'] == '110' )
								<a class="tre-caozuo" onclick="chakanbaojia(this,'{{$item->order_id}}')" >查看报价</a>
						   @elseif($item['status'] == '120')
						   			<a class="tre-caozuo" onclick="ConfirmReceive(this,'{{$item->order_id}}')">已到货</a>
				   			 @elseif($item['status'] == '130'|| $item['status'] == '200')
				   			<a class="tre-caozuo"  onclick="sendArmy(this,'{{$item->order_id}}')">发货到军方</a>
						   	@endif		
						</td>
					</tr>
					  @endforeach

				</tbody>
			</table>
			 @include('include.inc_pagination',['pagination'=>$order_list])
		</section>

@endsection
 
@section('MyJs')
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery-calendar/js/laydate.js')}}"></script>

<script>
	!function(){

	laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库

	laydate({elem: '#army_receive_time'});//绑定元素

}();
	
	//搜索
	
	  $(".tre-btn").on("click",function(){
    	
    	 if($("#army_receive_time").val()==""){
	    	var time=null;
	    }else{
	    	var time=$("#army_receive_time").val();
	    }
    	
	   var staus_val = $('.type_val option:selected').val();
    	var status_val=$(".staus_val option:selected").val();	
    	var url="{{url('platform/need/list')}}"+"/"+staus_val+"/"+status_val+"/"+time;
    	
    	location.replace(url);
    });
	
	



	 $('.tre-tianjia').on('click', function(){
		    layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['910px' , '600px'],
		      content: '{{url('platform/need/view')}}'
		    });
		  });
		  //分配
		  
		  function fenpei(elm,order_id){
		  
		    layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['900px' , '600px'],
		      content: '{{url('platform/allocation/view')}}'+'/'+order_id
		    });
		  }
		    function chakanbaojia(elm,order_id){
		    layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['900px' , '500px'],
		      content: '{{url('platform/offer/view')}}'+'/'+order_id
		    });
		  }
		  
		  function sendArmy(elm,order_id){
		  	if (confirm("确认发货到军方吗？")){
		  		$.ajax({
		  			type:'post',
		  			data:{
		  				order_id:order_id,
	  					_token:'{{csrf_token()}}'
		  			},
		  			url:'{{url('platform/send/army')}}',
		  			async:true,
		  			 beforeSend:function(res){
		            	if(!networkState){
		            		return false;
		            	}
		            	networkState=false;
		        	},
		  			success: function (resData) {
		  				var res=JSON.parse(resData)
		  				console.log(res)
		            if(res.code==0){
		             	   layer.msg(res.messages, {icon: 1, time: 1000},function(){
		             	   	networkState=true;
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
		  
		   function ConfirmReceive(elm,order_id){
		   	alert();
		  	if (confirm("确认已到货吗？")){
		  		$.ajax({
		  			type:'post',
		  			data:{
		  				order_id:order_id,
	  					_token:'{{csrf_token()}}'
		  			},
		  			url:'{{url('platform/confirm/receive')}}',
		  			async:true,
		  			 beforeSend:function(res){
		            	if(!networkState){
		            		return false;
		            	}
		            	networkState=false;
		       		},
		  			success: function (resData) {
		  				var res=JSON.parse(resData)
		            if(res.code==0){
		             	   layer.msg(res.messages, {icon: 1, time: 1000},function(){
		             	   	networkState=true;
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
		  
		  
		     function InventorySupply(elm,order_id){
		  	if (confirm("确认库存供应吗？")){
		  		$.ajax({
		  			type:'post',
		  			data:{
		  				order_id:order_id,
	  					_token:'{{csrf_token()}}'
		  			},
		  			url:'{{url('platform/inventory/supply')}}',
		  			async:true,
		  			 beforeSend:function(res){
		            	if(!networkState){
		            		return false;
		            	}
		            	networkState=false;
		        },
		  			success: function (resData) {
		  				var res=JSON.parse(resData)
		            if(res.code==0){
		             	   layer.msg(res.messages, {icon: 1, time: 1000},function(){
		             	   	networkState=true;
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
		  
		  

	
</script>
@endsection

