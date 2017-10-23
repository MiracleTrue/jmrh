@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/css/goods-management.css')}}">
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
				<a href="#" class="gmt-add"></a>

			</div>

			<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 20%;"><span>分类名称</span></th>
						<th style="width: 17%;"><span>商品数量</span></th>
						<th style="width: 16%;"><span>数量单位</span></th>
						<th style="width: 13%;"><span>排序</span></th>

						<th><span style="">操作</span></th>
					</tr>
					
					  @foreach($category_list as $item)
					<tr>
						<td>{{$item['category_name']}}</td>
						<td>{{$item['product_count']}}</td>
						<td>{{$item['unit']}}</td>
						<td>{{$item['sort']}}</td>

						<td class="blueWord">
							<a class="mly-caozuo" onclick="CategoryEdit(this,'{{$item['category_id']}}')">编辑</a>
							<a style="margin-left: 5%;">删除</a>
						</td>
					</tr>
					 @endforeach					
				</tbody>
			</table>
        @include('include.inc_pagination',['pagination'=>$category_list])

		</section>
@endsection

@section('MyJs')
<script>
	function CategoryEdit(elm,category_id){
		
		layer.open({
						type: 2,
						title: false,
						maxmin: false,
						shadeClose: true, //点击遮罩关闭层
						area: ['920px', '413px'],
						content: '{{url('category/view')}}'+'/'+category_id
					});
		
		
		
		
	}
		! function() {

			//页面一打开就执行，放入ready是为了layer所需配件（css、扩展模块）加载完毕
			layer.ready(function() {
				$('.gmt-add').on('click', function() {
					
					layer.open({
						type: 2,
						title: false,
						maxmin: false,
						shadeClose: true, //点击遮罩关闭层
						area: ['920px', '413px'],
						content: '{{url('category/view')}}'
					});
				});
			});

		}();
</script>
@endsection