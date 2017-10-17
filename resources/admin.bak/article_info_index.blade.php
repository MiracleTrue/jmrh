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
				<div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">选择分类中的 (数量) 代表分类下的文章数量</span></div>
			</div>
		</div>
		<div class="text-c">
			<label class="form-label col-xs-1 col-sm-1">选择分类：</label>
			<div class="formControls col-xs-3 col-sm-3">
				<div>
					<input class="input-text" id="parent_text"   name="parent_id" type="text" readonly value="{{$category_info['name'] or '全部文章'}}"/>
				</div>
				<div id="select_article_tree" class="ztree"></div>
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
				<a class="btn btn-primary radius"onclick="article_add('添加文章','{{action('Admin\ArticleController@InfoView')}}')" href="javascript:;"><i class="Hui-iconfont">&#xe600;</i> 添加文章</a>
			</span>
			<span class="r">{{__('admin.countData')}}：<strong>{{$article_list->total()}}</strong><i class="Hui-iconfont"> &#xe6c1;</i></span>
		</div>

		<table class="table table-border table-bordered table-bg table-hover table-sort">
			<thead>
			<tr>
				<th scope="col" colspan="20">文章列表</th>
			</tr>
			<tr class="text-c">
				<th width="25"><input type="checkbox" name="" value=""></th>
				<th width="80">ID</th>
				<th width="80">缩略图</th>
				<th width="80">排序</th>
				<th>标题</th>
				<th width="120">文章分类</th>
				<th width="120">发布时间</th>
				<th width="80">浏览次数</th>
				<th width="80">发布状态</th>
				<th width="120">操作</th>
			</tr>
			</thead>
			<tbody>
			@foreach($article_list as $item)
				<tr class="text-c">
					<td><input type="checkbox" value="" name=""></td>
					<td>{{$item['article_id']}}</td>
					<td><img width="100%" src="{{$item['article_thumb']}}"></td>
					<td>{{$item['article_sort']}}</td>
					<td class="text-l">{{$item['title']}}</td>
					<td>{{$item['article_category']['name']}}</td>
					<td>{{$item['created_at']}}</td>
					<td>{{$item['browse_count']}}</td>
					@if($item['audit_status'] == \App\Models\Article::AWAIT_AUDIT)
						<td class="td-status"><span class="label label-warning radius">等待审核</span></td>
					@elseif($item['audit_status'] == \App\Models\Article::SUCCESS_AUDIT)
						<td class="td-status"><span class="label label-success radius">发布成功</span></td>
					@else
						<td class="td-status"><span class="label label-success radius"></span></td>
					@endif
					<td class="f-14 td-manage">
						@if($item['audit_status'] == \App\Models\Article::AWAIT_AUDIT)
							<a style="text-decoration:none" onClick="article_start(this,'{{$item['article_id']}}')" href="javascript:;" title="发布"><i class="Hui-iconfont">&#xe603;</i></a>
						@else
							<a style="text-decoration:none" onClick="article_stop(this,'{{$item['article_id']}}')" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a>
						@endif
						<a style="text-decoration:none" class="ml-5" onClick="article_add('编辑文章','{{action('Admin\ArticleController@InfoView',$item['article_id'])}}')" href="javascript:;" title="编辑"><i class="Hui-iconfont">&#xe6df;</i></a>
						<a style="text-decoration:none" class="ml-5" onClick="article_del(this,'{{$item['article_id']}}')" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		@include('admin.include.inc_pagination',['pagination'=>$article_list])
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
//					$('#parent_text').val(treeNode.name);
//					$('#parent_hidden').val(treeNode.id);
//					$('#select_article_tree').hide();
					location.replace("{{action('Admin\ArticleController@InfoIndex')}}" + "/" + treeNode.id);
				}

			}
		};
		var zTreeNodes =[
			{ id:0, name:"全部文章", open:true},
			@foreach($category_tree as $item)
			{ id:'{{$item['category_id']}}', pId:'{{$item['parent_id']}}', name:'{{$item['name']}}' + '({{$item['article_count']}})'},
			@endforeach
		];

		/*资讯-添加*/
		function article_add(title,url)
		{
			var index = layer.open({
				type: 2,
				title: title,
				content: url
			});
			layer.full(index);
		}
		/*删除*/
		function article_del(obj,id){
			layer.confirm('确认要删除吗？',function(index){
				$.ajax({
					url: '{{action('Admin\ArticleController@InfoDeleteOne')}}',
					data:{ _token:"{{csrf_token()}}" , article_id : id },
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

		/*下架*/
		function article_stop(obj,id){
			layer.confirm('确认要下架吗？',function(index){
				$.ajax({
					url: '{{action('Admin\ArticleController@InfoAudit')}}',
					data:{ _token:"{{csrf_token()}}" , article_id : id , audit_status:"{{\App\Models\Article::AWAIT_AUDIT}}" },
					type: 'POST',
					dataType: 'JSON',
					beforeSubmit:function(){
						layer_index = layer.loading();
					},
					success:function(res){
						if(res.code == 0)
						{
							$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="article_start(this,' + id + ')" href="javascript:;" title="发布"><i class="Hui-iconfont">&#xe603;</i></a>');
							$(obj).parents("tr").find(".td-status").html('<span class="label label-warning radius">等待审核</span>');
							$(obj).remove();
							layer.msg('已下架!',{icon: 5,time:1000});
						}
						else
						{
							layer.msg(res.messages,{icon:2,time:1000});
						}
					}
				});
			});
		}

		/*发布*/
		function article_start(obj,id){
			layer.confirm('确认要发布吗？',function(index){
				$.ajax({
					url: '{{action('Admin\ArticleController@InfoAudit')}}',
					data:{ _token:"{{csrf_token()}}" , article_id : id , audit_status:"{{\App\Models\Article::SUCCESS_AUDIT}}" },
					type: 'POST',
					dataType: 'JSON',
					beforeSubmit:function(){
						layer_index = layer.loading();
					},
					success:function(res){
						if(res.code == 0)
						{
							$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="article_stop(this,' + id + ')" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a>');
							$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">发布成功</span>');
							$(obj).remove();
							layer.msg('发布成功!',{icon: 6,time:1000});
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
			var zTree = $.fn.zTree.init($('#select_article_tree'), zTreeSetting, zTreeNodes);
			$('#parent_btn').click(function()
			{
				$('#select_article_tree').toggle();
			});
		});
	</script>

@endsection

