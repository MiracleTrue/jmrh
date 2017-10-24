@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
  
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
				<a href="#" class="mly-tianjia"></a>
				<div class="mly-shaixuan">
					<select name="" class="mly-state">
						<option value="">全部</option>
					</select>
					<select name="" class="mly-time">
						<option value="">全部</option>
					</select>
				</div>
				<a class="mly-btn">搜索</a>
			</div>

			<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 9%;"><span>序号</span></th>
						<th style="width: 18%;"><span>订单号</span></th>
						<th style="width: 10%;"><span>品名</span></th>
						<th style="width: 6%;"><span>到货时间</span></th>
						<th style="width: 14%;"><span style="">数量</span></th>
						<th style="width: 14%;"><span style="">状态</span></th>

						<th style="width: 19%"><span style="">操作</span></th>
					</tr>
					<tr>
						<td>1</td>
						<td>20170926110753</td>
						<td>白菜</td>
						<td>20170928</td>
						<td>500斤</td>
						<td>待确认</td>
						<td class="blueWord">
							<a class="mly-caozuo">修改</a>
							<a style="margin-left: 5%;">删除</a>
						</td>
					</tr>
					<tr>
						<td>2</td>
						<td>20170926110753</td>
						<td>白菜</td>
						<td>20170928</td>
						<td>500斤</td>
						<td>待确认</td>
						<td class="blueWord">详情</td>
					</tr>
					<tr>
						<td>3</td>
						<td>20170926110753</td>
						<td>白菜</td>
						<td>20170928</td>
						<td>500斤</td>
						<td>待确认</td>
						<td class="blueWord">详情</td>
					</tr>
					<tr>
						<td>4</td>
						<td>20170926110753</td>
						<td>白菜</td>
						<td>20170928</td>
						<td>500斤</td>
						<td>待确认</td>
						<td class="blueWord">详情</td>
					</tr>
					<tr>
						<td>5</td>
						<td>20170926110753</td>
						<td>白菜</td>
						<td>20170928</td>
						<td>500斤</td>
						<td>待确认</td>
						<td class="blueWord">详情</td>
					</tr>
					<tr>
						<td>6</td>
						<td>20170926110753</td>
						<td>白菜</td>
						<td>20170928</td>
						<td>500斤</td>
						<td>待确认</td>
						<td class="blueWord">详情</td>
					</tr>
					<tr>
						<td>7</td>
						<td>20170926110753</td>
						<td>白菜</td>
						<td>20170928</td>
						<td>500斤</td>
						<td>已确认</td>
						<td class="blueWord">详情</td>
					</tr>
					<tr>
						<td>8</td>
						<td>20170926110753</td>
						<td>白菜</td>
						<td>20170928</td>
						<td>500斤</td>
						<td>已发货</td>
						<td class="blueWord">
							<a class="mly-caozuo">已到货</a>
						</td>
					</tr>
					<tr>
						<td>9</td>
						<td>20170926110753</td>
						<td>白菜</td>
						<td>20170928</td>
						<td>500斤</td>
						<td>待确认</td>
						<td class="blueWord">详情</td>
					</tr>

				</tbody>
			</table>

		
		</section>
@endsection

@section('MyJs')
layer.ready(function(){ 
  $('.mly-tianjia').on('click', function(){
    layer.open({
      type: 2,
      title: false,
      maxmin: false,
      shadeClose: true, //点击遮罩关闭层
      area : ['900px' , '500px'],
      content: 'iftianjiaxuqiu.html'
    });
  });
});



}();
</script>
@endsection