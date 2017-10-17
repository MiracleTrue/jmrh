@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}

@section('content')
	@include('admin.include.inc_nav')
	<div class="page-container">
		<div class="text-c">
			<input type="text" class="input-text" style="width:250px" placeholder="" id="" name="">
			<button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> {{__('common.search')}}</button>
		</div>
		<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l">
				<a href="javascript:;" onclick="admin_add('{{__('admin.privilege.managerAdd')}}','{{action('Admin\PrivilegeController@ManagerView')}}')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> {{__('admin.privilege.managerAdd')}}</a>
			</span>
			<span class="r">{{__('admin.countData')}}：<strong>{{$manager_count}}</strong><i class="Hui-iconfont"> &#xe6c1;</i></span>
		</div>
		<table class="table table-border table-bordered table-bg">
			<thead>
			<tr>
				<th scope="col" colspan="9">{{__('admin.privilege.managerM')}}</th>
			</tr>
			<tr class="text-c">
				<th width="25"><input type="checkbox" name="" value=""></th>
				<th width="40">ID</th>
				<th width="150">{{__('admin.privilege.managerName')}}</th>
				<th width="120">{{__('admin.privilege.roleName')}}</th>
				<th>{{__('common.mobilePhone')}}</th>
				<th>{{__('common.email')}}</th>
				<th width="140">{{__('common.lastLogin')}}</th>
				<th width="100">{{__('common.status')}}</th>
				<th width="140">{{__('common.manage')}}</th>
			</tr>
			</thead>
			<tbody>
			@foreach($manager_list as $item)
				<tr class="text-c">
					<td><input type="checkbox" value="1" name=""></td>
					<td>{{$item['admin_id']}}</td>
					<td>{{$item['admin_name']}}</td>
					<td title="{{$item['admin_role']['role_description']}}">{{$item['admin_role']['role_name']}}</td>
					<td>{{$item['phone']}}</td>
					<td>{{$item['email']}}</td>
					<td>{{\Carbon\Carbon::createFromTimestamp($item['last_login'])}}</td>
					@if($item['is_enable'] == \App\Models\Rbac::MANAGER_IS_ENABLE)
						<td class="td-status"><span class="label label-success radius">{{__('admin.enable')}}</span></td>
						<td class="td-manage">
							<a style="text-decoration:none" onClick="admin_stop(this,'{{$item['admin_id']}}')" href="javascript:;" title="{{__('admin.disable')}}"><i class="Hui-iconfont">&#xe631;</i></a>
					@elseif($item['is_enable'] == \App\Models\Rbac::MANAGER_NO_ENABLE)
						<td class="td-status"><span class="label radius">{{__('admin.disable')}}</span></td>
						<td class="td-manage">
							<a style="text-decoration:none" onClick="admin_start(this,'{{$item['admin_id']}}')" href="javascript:;" title="{{__('admin.enable')}}"><i class="Hui-iconfont">&#xe615;</i></a>
					@else
						<td class="td-manage">
					@endif
							<a title="{{__('common.view')}}"   href="{{action('Admin\PrivilegeController@AdminLogIndex' , $item['admin_id'])}}" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe725;</i></a>
							<a title="{{__('common.editor')}}" href="javascript:;" onclick="admin_add('{{__('admin.privilege.managerEdit')}}','{{action('Admin\PrivilegeController@ManagerView' , $item['admin_id'])}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
							<a title="{{__('common.delete')}}" href="javascript:;" onclick="admin_del(this,'{{$item['admin_id']}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
						</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	@include('admin.include.inc_footer')
	@endsection

	@section('MyJs')
			<!--请在下方写此页面业务相关的脚本-->
	<script type="text/javascript" src="{{asset('adminStatic/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
	<script type="text/javascript" src="{{asset('adminStatic/lib/datatables/1.10.0/jquery.dataTables.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('adminStatic/lib/laypage/1.2/laypage.js')}}"></script>
	<script type="text/javascript">

		function admin_add(title,url,w,h){
			layer_show(title,url,w,h);
		}

		/*管理员-删除*/
		function admin_del(obj,id){
			layer.confirm('{{__('admin.deleteConfirm')}}',function(index){
				$.ajax({
					type: 'POST',
					url: '{{action('Admin\PrivilegeController@ManagerDeleteOne')}}',
					dataType: 'json',
					data:{
						"_token":"{{csrf_token()}}",
						"admin_id":id
					},
					success: function(data)
					{
						if(data.code == 0)
						{
							layer.msg(data.messages,{icon:1,time:1000},function () {
								location.replace(location.href);
							});
						}
						else
						{
							layer.msg(data.messages,{icon:2,time:1000});
						}
					}
				});
			});
		}

		/*管理员-编辑*/
		function admin_edit(title,url,id,w,h){
			layer_show(title,url,w,h);
		}
		/*管理员-停用*/
		function admin_stop(obj, id) {
			var layer_index;
			$.ajax({
				url:"{{action('Admin\PrivilegeController@ManagerQuickEdit')}}",    //请求的url地址
				dataType:"json",   //返回格式为json
				data:{"_token":"{{csrf_token()}}", "admin_id":id , "is_enable":"{{\App\Models\Rbac::MANAGER_NO_ENABLE}}"},
				type:"POST",   //请求方式
				beforeSend:function(){
					layer_index = layer.loading();
				},
				success:function(res){
					if (res.code == 0)
					{
						$(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_start(this,'+ id +')" href="javascript:;" title="{{__('admin.enable')}}" style="text-decoration:none"><i class="Hui-iconfont">&#xe615;</i></a>');
						$(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">{{__('admin.disable')}}</span>');
						$(obj).remove();
						layer.msg('{{__('admin.disable')}}!', {icon: 5, time: 1000});
					}
					else
					{
						layer.msg(res.messages, {icon: 2, time: 1000});
					}
				},
				complete:function(){
					layer.close(layer_index);
				}
			});
		}

		/*管理员-启用*/
		function admin_start(obj, id) {
			var layer_index;
			$.ajax({
				url:"{{action('Admin\PrivilegeController@ManagerQuickEdit')}}",    //请求的url地址
				dataType:"json",   //返回格式为json
				data:{"_token":"{{csrf_token()}}", "admin_id":id , "is_enable":"{{\App\Models\Rbac::MANAGER_IS_ENABLE}}"},    //参数值
				type:"POST",   //请求方式
				beforeSend:function(){
					layer_index = layer.loading();
				},
				success:function(res){
					if (res.code == 0)
					{
						$(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_stop(this,'+ id +')" href="javascript:;" title="{{__('admin.disable')}}" style="text-decoration:none"><i class="Hui-iconfont">&#xe631;</i></a>');
						$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">{{__('admin.enable')}}</span>');
						$(obj).remove();
						layer.msg('{{__('admin.enable')}}!', {icon: 6, time: 1000});
					}
					else
					{
						layer.msg(res.messages, {icon: 2, time: 1000});
					}
				},
				complete:function(){
					layer.close(layer_index);
				}
			});
		}
	</script>

@endsection

