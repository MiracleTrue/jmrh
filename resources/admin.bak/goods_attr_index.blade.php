﻿@extends('admin.layouts.master')
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
				<div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">选择分类中的 (数量) 代表分类下的商品属性数量</span></div>
			</div>
		</div>
		<div class="text-c">
			<label class="form-label col-xs-1 col-sm-1">选择分类：</label>
			<div class="formControls col-xs-3 col-sm-3">
				<div>
					<input class="input-text" id="parent_text"   name="parent_id" type="text" readonly value="{{$category_info['name'] or '全部商品属性'}}"/>
				</div>
				<div id="goods_category_tree" class="ztree"></div>
			</div>
			<div class="col-xs-1 col-sm-1">
				<button type="button" id="parent_btn" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe642;</i> {{__('admin.browse')}}</button>
			</div>
			日期范围：
			<input type="text" onfocus="WdatePicker({ maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}' })" id="logmin" class="input-text Wdate" style="width:120px;">
			-
			<input type="text" onfocus="WdatePicker({ minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'%y-%M-%d' })" id="logmax" class="input-text Wdate" style="width:120px;">
			<button type="submit" class="btn btn-success ml-10" id="" name=""><i class="Hui-iconfont">&#xe665;</i> {{__('common.search')}}</button>
		</div>

		<div class="cl pd-5 bg-1 bk-gray mt-20">
			<span class="l">
				<a class="btn btn-primary radius"onclick="article_add('添加商品属性','{{action('Admin\GoodsController@AttributesView')}}')" href="javascript:;"><i class="Hui-iconfont">&#xe600;</i> 添加商品属性</a>
			</span>
			<span class="r">{{__('admin.countData')}}：<strong>{{$attr_list->total()}}</strong><i class="Hui-iconfont"> &#xe6c1;</i></span>
		</div>

		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
			<tr>
				<th scope="col" colspan="20">商品属性列表</th>
			</tr>
			<tr class="text-c">
				<th width="25"><input type="checkbox" name="" value=""></th>
				<th width="80">ID</th>
				<th width="120">属性名称</th>
				<th>商品展示属性</th>
				<th>商品购买属性</th>
				<th width="120">商品分类</th>
				<th width="80">属性数量</th>
				<th width="120">操作</th>
			</tr>
			</thead>
			<tbody>
			@foreach($attr_list as $item)
				<tr class="text-c">
					<td><input type="checkbox" value="" name=""></td>
					<td>{{$item['attr_id']}}</td>
					<td>{{$item['attr_name']}}</td>
					<td class="text-l">{{$item['show_attr']}}</td>
					<td class="text-l">{{$item['select_attr']}}</td>
					<td>{{$item['goods_category']['name']}}</td>
					<td>{{$item['attr_count']}}</td>
					<td class="f-14 td-manage">
						<a style="text-decoration:none" class="ml-5" onClick="article_add('编辑属性','{{action('Admin\GoodsController@AttributesView',$item['attr_id'])}}')" href="javascript:;" title="编辑"><i class="Hui-iconfont">&#xe6df;</i></a>
						<a style="text-decoration:none" class="ml-10" onClick="article_del(this,'{{$item['attr_id']}}')" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		@include('admin.include.inc_pagination',['pagination'=>$attr_list])
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
			"aaSorting": [[ 1, "asc" ]],//默认第几个排序
			"bStateSave": true,//状态保存
			"aoColumnDefs": [
				//{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
				{"orderable":false,"aTargets":[0,2,3,4,5,7]}// 制定列不参与排序
			],
			language: {
				sEmptyTable: "{{__('admin.dataTable.sEmptyTable')}}",
				sZeroRecords:"{{__('admin.dataTable.sZeroRecords')}}",
				search:"{{__('admin.dataTable.search')}}"
			}
		});

		/*分类树*/
		var zTreeSetting = {
			view: {
				selectedMulti: false,
			},
			data: {
				simpleData: {
					enable:true,
					idKey: "id",
					pIdKey: "pId"
				}
			},
			callback: {
				beforeClick: function(treeId, treeNode) {
					location.replace("{{action('Admin\GoodsController@AttributesIndex')}}" + "/" + treeNode.id);
				}

			}
		};
		var zTreeNodes =[
			{ id:0, name:"全部商品属性", open:true},
			@foreach($category_tree as $item)
			{ id:'{{$item['category_id']}}', pId:'{{$item['parent_id']}}', name:'{{$item['name']}}' + '({{$item['my_count']}})'},
			@endforeach
		];

		/*品牌编辑*/
		function article_add(title,url){
			layer_show(title,url);
		}
		/*删除*/
		function article_del(obj,id){
			layer.confirm('确认要删除吗？',function(index){
				$.ajax({
					url: '{{action('Admin\GoodsController@AttributesDeleteOne')}}',
					data:{ _token:"{{csrf_token()}}" , attr_id : id },
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
