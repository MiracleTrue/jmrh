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
						<th style="width: 20%;"><span>分类名称</span></th>
						<th style="width: 17%;"><span>商品数量</span></th>
						<th style="width: 16%;"><span>数量单位</span></th>
						<th style="width: 13%;"><span>排序</span></th>

						<th><span style="">操作</span></th>
					</tr>
					<tr>
						<td>蔬菜</td>
						<td>52</td>
						<td>斤</td>
						<td>1</td>

						<td class="blueWord">
							<a class="mly-caozuo">编辑</a>
							<a style="margin-left: 5%;">删除</a>
						</td>
					</tr>
					<tr>
						<td>肉食</td>
						<td>53</td>
						<td>斤</td>
						<td>2</td>

						<td class="blueWord">
							<a class="mly-caozuo">编辑</a>
							<a style="margin-left: 5%;">删除</a>
						</td>
					</tr>
					<tr>
						<td>牛奶</td>
						<td>41</td>
						<td>斤</td>
						<td>3</td>

						<td class="blueWord">
							<a class="mly-caozuo">编辑</a>
							<a style="margin-left: 5%;">删除</a>
						</td>
					</tr>
					<tr>
						<td>食用油</td>
						<td>5</td>
						<td>袋/箱</td>
						<td>4</td>

						<td class="blueWord">
							<a class="mly-caozuo">编辑</a>
							<a style="margin-left: 5%;">删除</a>
						</td>
					</tr>
					<tr>
						<td>牛奶</td>
						<td>56</td>
						<td>斤</td>
						<td>5</td>

						<td class="blueWord">
							<a class="mly-caozuo">编辑</a>
							<a style="margin-left: 5%;">删除</a>
						</td>
					</tr>
					<tr>
						<td>食用油</td>
						<td>57</td>
						<td>袋/箱</td>
						<td>6</td>

						<td class="blueWord">
							<a class="mly-caozuo">编辑</a>
							<a style="margin-left: 5%;">删除</a>
						</td>
					</tr>
					<tr>
						<td>食用油</td>
						<td>65</td>
						<td>桶/斤</td>
						<td>7</td>

						<td class="blueWord">
							<a class="mly-caozuo">编辑</a>
							<a style="margin-left: 5%;">删除</a>
						</td>
					</tr>
					<tr>
						<td>肉食</td>
						<td>53</td>
						<td>斤</td>
						<td>8</td>

						<td class="blueWord">
							<a class="mly-caozuo">编辑</a>
							<a style="margin-left: 5%;">删除</a>
						</td>
					</tr>
					<tr>
						<td>蔬菜</td>
						<td>52</td>
						<td>斤</td>
						<td>9</td>

						<td class="blueWord">
							<a class="mly-caozuo">编辑</a>
							<a style="margin-left: 5%;">删除</a>
						</td>
					</tr>

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
						area: ['920px', '413px'],
						content: '{{url('category/view')}}'
					});
				});
			});

		}();
</script>
@endsection