@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}
@section('MyCss')
	<link rel="stylesheet" href="{{URL::asset('adminStatic/lib/jquery.zTree/css/metroStyle/metroStyle.css')}}">
@endsection
@section('content')
	@include('admin.include.inc_nav')
	<div class="page-container">
		<div class="pd-5 mb-10 bg-1 bk-gray prompt">
			<div class="pl-30"><i class="Hui-iconfont c-primary">&#xe64b;</i><span class="pl-5 f-12 c-primary">{{__('admin.operationPrompt')}}</span></div>
			<div class="pl-30 pr-30 cl">
				<div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">商品在回收站删除则永久销毁</span></div>
			</div>
		</div>

		<div class="cl pd-5 bg-1 bk-gray mt-20">
			<span class="r">{{__('admin.countData')}}：<strong>{{$goods_list->total()}}</strong><i class="Hui-iconfont"> &#xe6c1;</i></span>
		</div>

		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
			<tr>
				<th scope="col" colspan="20">商品回收站列表</th>
			</tr>
			<tr class="text-c">
				<th width="25"><input type="checkbox" name="" value=""></th>
				<th width="80">ID</th>
				<th width="60">类型</th>
				<th width="100">店铺</th>
				<th width="80">缩略图</th>
				<th width="80">排序</th>
				<th width="80">商品分类</th>
				<th width="60">商品品牌</th>
				<th>商品名称</th>
				<th width="80">商品价格</th>
				<th width="80">商品数量</th>
				<th width="80">销售总数</th>
				<th width="70">添加时间</th>
				<th width="120">操作</th>
			</tr>
			</thead>
			<tbody>
			@foreach($goods_list as $item)
				<tr class="text-c">
					<td><input type="checkbox" value="" name=""></td>
					<td>{{$item['goods_id']}}</td>
					@if($item['goods_type'] == \App\Models\Goods::NORMAL_GOODS)
						<td class="td-status"><span class="label label-primary radius">普通</span></td>
					@elseif($item['goods_type'] == \App\Models\Goods::PRE_GOODS)
						<td class="td-status"><span class="label label-warning radius">预售</span></td>
					@elseif($item['goods_type'] == \App\Models\Goods::EXCHANGE_GOODS)
						<td class="td-status"><span class="label label-success radius">积分</span></td>
					@else
						<td class="td-status"><span class="label label-default radius">数据错误</span></td>
					@endif
					<td>{{$item['merchant_info']['shop_name']}}</td>
					<td><img width="100%" src="{{$item['goods_thumb']}}"></td>
					<td>{{$item['goods_sort']}}</td>
					<td>{{$item['goods_category']['name']}}</td>
					<td title="{{$item['goods_brand']['brand_name']}}"><img width="100%" src="{{$item['goods_brand']['brand_logo']}}"></td>
					<td>{{$item['goods_name']}}</td>
					<td>{{$item['goods_price']}}</td>
					<td>{{$item['goods_number']}}</td>
					<td>{{$item['buy_count']}}</td>
					<td>{{$item['add_time']}}</td>
					<td class="f-14 td-manage">
						<a style="text-decoration:none" onClick="goods_recovery(this,'{{$item['goods_id']}}')" href="javascript:;" title="恢复"><i class="Hui-iconfont">&#xe6f7;</i></a>
						<a style="text-decoration:none" class="ml-5" onClick="goods_destroy(this,'{{$item['goods_id']}}')" href="javascript:;" title="销毁"><i class="Hui-iconfont">&#xe6e2;</i></a>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		@include('admin.include.inc_pagination',['pagination'=>$goods_list])
	</div>
	@include('admin.include.inc_footer')
	@endsection

	@section('MyJs')
	<!--请在下方写此页面业务相关的脚本-->
	<script type="text/javascript" src="{{asset('adminStatic/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
	<script type="text/javascript" src="{{asset('adminStatic/lib/datatables/1.10.0/jquery.dataTables.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('adminStatic/lib/laypage/1.2/laypage.js')}}"></script>
	<script type="text/javascript" src="{{asset('adminStatic/lib/jquery.zTree/js/jquery.ztree.all.min.js')}}"></script>
	<script type="text/javascript">
		$('.table-sort').dataTable({
			"lengthMenu":false,//显示数量选择
			"bFilter": true,//过滤功能
			"bPaginate": false,//翻页信息
			"bInfo": false,//数量信息
			"aaSorting": [[ 1, "desc" ]],//默认第几个排序
			"bStateSave": true,//状态保存
			"aoColumnDefs": [
				//{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
				{"orderable":false,"aTargets":[0,2,4,9]}// 制定列不参与排序
			],
			language: {
				sEmptyTable: "{{__('admin.dataTable.sEmptyTable')}}",
				sZeroRecords:"{{__('admin.dataTable.sZeroRecords')}}",
				search:"{{__('admin.dataTable.search')}}"
			}
		});

		/*商品*/
		function goods_add(title,url)
		{
			var index = layer.open({
				type: 2,
				title: title,
				content: url
			});
			layer.full(index);
		}
		/*删除*/
		function goods_destroy(obj,id){
			layer.confirm('永久销毁,确认要销毁吗？',function(index){
				$.ajax({
					url: '{{action('Admin\GoodsController@GoodsRecycleDestroyOne')}}',
					data:{ _token:"{{csrf_token()}}" , goods_id : id },
					type: 'POST',
					dataType: 'JSON',
					beforeSubmit:function(){
						layer_index = layer.loading();
					},
					success:function(res){
						if(res.code == 0)
						{
							$(obj).parents("tr").remove();
							layer.msg(res.messages,{icon:1,time:1000});
						}
						else
						{
							layer.msg(res.messages,{icon:2,time:1000});
						}
					}
				});
			});
		}

		/*上架*/
		function goods_recovery(obj,id){
			layer.confirm('确认要恢复吗？',function(index){
				$.ajax({
					url: '{{action('Admin\GoodsController@GoodsRecycleRecovery')}}',
					data:{ _token:"{{csrf_token()}}" , goods_id : id},
					type: 'POST',
					dataType: 'JSON',
					beforeSubmit:function(){
						layer_index = layer.loading();
					},
					success:function(res){
						if(res.code == 0)
						{
							$(obj).parents("tr").remove();
							layer.msg('已恢复!',{icon: 6,time:1000});
						}
						else
						{
							layer.msg(res.messages,{icon:2,time:1000});
						}
					}
				});
			});
		}

		$(document).ready(function()
		{
			var zTree = $.fn.zTree.init($('#goods_category_tree'), zTreeSetting, zTreeNodes);
			$('#parent_btn').click(function()
			{
				$('#goods_category_tree').toggle();
			});
		});
	</script>

@endsection

