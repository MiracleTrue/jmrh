@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/css/goods-management.css')}}">


@endsection
@section('content')
  <section>
			<div>
				<a href="#" class="gmt-add"></a>

			</div>

			<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 20%;"><span>商品名称</span></th>
						<th style="width: 17%;"><span>商品图片</span></th>
						<th style="width: 16%;"><span>所属分类</span></th>
						<th style="width: 13%;"><span>排序</span></th>

						<th><span style="">操作</span></th>
					</tr>
					
					 @foreach($product_list as $item)
					<tr>
						<td>{{$item['product_name']}}</td>
						<td><img src="../../public/webStatic/images/goods.png"/></td>
						<td>蔬菜</td>
						<td>1</td>

						<td class="blueWord">
							<a class="mly-caozuo">编辑</a>
							<a style="margin-left: 5%;">删除</a>
						</td>
					</tr>
					 @endforeach
				</tbody>
			</table>

		</section>

@endsection
@section('MyJs')
<script>
	;
		! function() {

			//页面一打开就执行，放入ready是为了layer所需配件（css、扩展模块）加载完毕
			layer.ready(function() {
				$('.gmt-add').on('click', function() {
					
					layer.open({
						type: 2,
						title: false,
						maxmin: false,
						shadeClose: true, //点击遮罩关闭层
						area: ['920px', '913px'],
						content: '{{url('product/view')}}'
					});
				});
			});

		}();
</script>
@endsection