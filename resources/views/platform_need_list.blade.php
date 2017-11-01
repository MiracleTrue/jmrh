@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
   
    <link rel="stylesheet" href="{{asset('webStatic/css/terrace.css')}}">
<style>
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
				<a href="#" class="tre-tianjia"></a>
				<div class="tre-shaixuan">
					<select name="" class="tre-state">
						<option value="">全部</option>
					</select>
					<select name="" class="tre-time">
						<option value="">全部</option>
					</select>
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
							<a class="tre-caozuo">分配</a>
							<a style="margin-left: 5%;">无需分配</a>
						  @elseif($item['status'] == '100' || $item['status'] == '110' )
								<a class="tre-caozuo">查看报价</a>
						   @elseif($item['status'] == '120')
						   			<a class="tre-caozuo">已到货</a>
				   			 @elseif($item['status'] == '130'|| $item['status'] == '200')
				   			<a class="tre-caozuo">发货到军方</a>
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
<script>
	 $('.tre-tianjia').on('click', function(){
		    layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['900px' , '800px'],
		      content: '{{url('platform/need/view')}}'
		    });
		  });
	
	
</script>
@endsection

