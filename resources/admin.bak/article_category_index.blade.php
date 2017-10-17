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
				<div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">该页面展示所有文章分类</span></div>
				<div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">可添加子分类以及编辑修改、删除分类。</span></div>
				<div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">选择分类中的 (数量) 代表分类下的文章数量</span></div>
			</div>
		</div>
		<div class="container-fluid mb-10">
			<button type="button" id="ztree_open_all" class="btn btn-secondary radius"><i class="Hui-iconfont">&#xe600;</i> 全部展开</button>
		</div>
		<div class="container-fluid cl">
			<div id="article_tree" class="ztree f-l col-xs-3"></div>
			<iframe class="f-r col-xs-9" id="article_tree_frame" name="article_tree_iframe" frameborder=0 scrolling=auto style="min-height: 540px" src="{{action('Admin\ArticleController@CategoryView')}}"></iframe>
		</div>
	</div>
	@include('admin.include.inc_footer')
@endsection

@section('MyJs')
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="{{asset('adminStatic/lib/jquery.zTree/js/jquery.ztree.all.min.js')}}"></script>
<script type="text/javascript">
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
	edit: {
		enable: true,
		showRenameBtn : false,
		removeTitle : "{{__('common.delete')}}",
	},
	callback: {
		beforeClick: function(treeId, treeNode) {
			var zTree = $.fn.zTree.getZTreeObj("article_tree");
			$('#article_tree_frame').attr("src","{{action('Admin\ArticleController@CategoryView')}}" + "/" + treeNode.id);
		},
		beforeRemove :function (treeId, treeNode)
		{
			var zTree = $.fn.zTree.getZTreeObj(treeId);

			layer.confirm('{{__('admin.deleteConfirm')}}',function(index){
				$.ajax({
					type: 'POST',
					url: '{{action('Admin\ArticleController@CategoryDeleteOne')}}',
					data:{
						"_token":"{{csrf_token()}}",
						"category_id":treeNode.id
					},
					dataType: 'json',
					success: function(data){
						if(data.code == 0)
						{
							layer.msg(data.messages,{icon:1,time:1000});
							zTree.removeNode(treeNode);
						}
						else
						{
							layer.msg(data.messages,{icon:2,time:1000});
						}
					}
				});
			});

			return false;
		}

	}
};
var zTreeNodes =[
	@foreach($category_tree as $item)
	{ id:'{{$item['category_id']}}', pId:'{{$item['parent_id']}}', name:'{{$item['name']}}' + '({{$item['article_count']}})'},
	@endforeach
];

$(document).ready(function(){
	var zTree = $.fn.zTree.init($("#article_tree"), zTreeSetting, zTreeNodes);

	/*全部展开*/
	var expandAll_index = false;
	$('#ztree_open_all').click(function()
	{
		if(!expandAll_index)
		{
			$.fn.zTree.getZTreeObj("article_tree").expandAll(true);
			expandAll_index = true;
		}
		else
		{
			$.fn.zTree.getZTreeObj("article_tree").expandAll();
			expandAll_index = false;
		}

	})
});

</script>
@endsection

