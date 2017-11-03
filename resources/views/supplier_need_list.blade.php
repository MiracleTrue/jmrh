@extends('layouts.master')

@section('MyCss')
    <link rel="stylesheet" href="{{asset('webStatic/css/provider.css')}}">
    	<style type="text/css">
    			/*分页样式*/
.userlist_pag{
	height: 45px;
	text-align: center;
	margin-top: 57px;
}
.userlist_pag ul{
	overflow: hidden;
	height: 43px;
	text-align: center;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    width: 85%;
  -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
  
}
.userlist_pag ul li{
	
	height: 41px;
	line-height: 41px;
	text-align: center;
	color: #0e99dc;
	font-size: 15px;
	border-left:1px solid #dddddd ;
	border-top:1px solid #dddddd ;
	border-bottom:1px solid #dddddd ;
	width: 46px;
   
	
	
}
.userlist_pag ul li a, .userlist_pag ul li span{
	display: inline-block;
	width: 100%;
	height: 100%;
}
.userlist_pag ul li a{
	color:#0e99dc ;
}
.userlist_pag ul li:nth-child(1),.userlist_pag ul li:last-child{
	width: 89px;
}
.userlist_pag .active{
	background-color: #FE8D01;
	color: #FFFFFF;
}
.userlist_pag ul li:last-child{
	border-right:1px solid #dddddd;
}
    	</style>
@endsection
@section('content')
<section>
			<div>
				
				<div class="pvr-shaixuan">
					<select name="" class="pvr-state">
						<option value="null" >全部</option>
						<option value="待报价"  @if($page_search['status'] == '待报价') selected="selected" @endif>待报价</option>
						<option value="等待确认" @if($page_search['status'] == '等待确认') selected="selected" @endif>等待确认</option>
						<option value="待发货" @if($page_search['status'] == '待发货') selected="selected" @endif>待发货</option>
						<option value="已发货" @if($page_search['status'] == '已发货') selected="selected" @endif>已发货</option>
						<option value="未通过" @if($page_search['status'] == '未通过') selected="selected" @endif>未通过</option>
						<option value="已过期" @if($page_search['status'] == '已过期') selected="selected" @endif>已过期</option>
						

					</select>
					
				 	<input class="pvr-time" name="cre_time" id="cre_time" onClick="laydate({istime: true, format: 'YYYY-MM-DD'})" class="laydate-icon"  name="army_receive_time" id="army_receive_time" value=@if($page_search['create_time']=="null") "" @else "{{$page_search['create_time']}}"@endif placeholder="请选择日期"/>
				</div>
				<a class="pvr-btn">搜索</a>
			</div>
			
			<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 9%;"><span>序号</span></th>
						<th style="width: 18%;"><span>订单号</span></th>
						<th style="width: 10%;"><span>品名</span></th>
						<th style="width: 6%;"><span>到货时间</span></th>
						<th style="width: 14%;"><span style="">数量</span></th>
						<th style="width: 14%;"><span style="">报价</span></th>

						<th style="width: 19%"><span style="">操作</span></th>
					</tr>
					  @foreach($offer_list as $item)
					<tr>
						<td>{{$item->offer_id}}</td>
						<td>{{$item['order_info']['order_sn']}}</td>
						<td>{{$item['order_info']['product_name']}}</td>
						<td>{{$item['order_info']['army_receive_time']}}</td>
						<td>{{$item['order_info']['product_number']}}{{$item['order_info']['product_unit']}}</td>
						<td>@if($item->status_text=="等待通过"){{$item->total_price}}元 @else{{$item->status_text}} @endif </td>
						<td class="blueWord">
							@if($item['status'] == '0')
							<a class="pvr-caozuo" onclick="supplierView(this,{{$item->offer_id}})">报价</a>
							  @elseif($item['status'] == '1')
							  	<a class="pvr-caozuo" style="color: #000000;">等待通过</a>
							  	  @elseif($item['status'] == '2')
							  	  	<a class="pvr-caozuo" style="color: #000000;">未通过</a>
							  	  	  @elseif($item['status'] == '3')
							  	  	  <a class="pvr-caozuo" onclick="SendGoods(this,{{$item->offer_id}})" >准备配货</a>
							  	  	  @elseif($item['status'] == '4')
							  	  	  <a class="pvr-caozuo"style="color: #000000;">已配货</a>
							 	@endif		
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
  		!function(){

			laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库
		
			laydate({elem: '#army_receive_time'});//绑定元素
		
		}();
		
		
		
		
  	function supplierView(elm,offer_id){
  		  layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['900px' , '800px'],
		      content: '{{url('supplier/offer/view')}}'+"/"+offer_id
		    });
  	}
  	
  	
  	  function SendGoods(elm,offer_id){
		  	if (confirm("确认配货吗？")){
		  		$.ajax({
		  			data:{
		  				offer_id:offer_id,
	  					_token:'{{csrf_token()}}'
		  			},
		  			url:'{{url('supplier/send/goods')}}',
		  			async:true,
		  			success: function (resData) {
		  				var res=JSON.parse(resData)
		  				console.log(res)
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
