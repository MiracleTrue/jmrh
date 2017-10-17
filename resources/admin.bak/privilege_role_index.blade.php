@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}

@section('content')
    @include('admin.include.inc_nav')
    <div class="page-container">
        <div class="cl pd-5 bg-1 bk-gray">
            <span class="l">
                <a class="btn btn-primary radius" href="javascript:;" onclick="admin_role_edit('{{__('admin.privilege.roleAdd')}}','{{action('Admin\PrivilegeController@RoleView')}}')"><i class="Hui-iconfont">&#xe600;</i> {{__('admin.privilege.roleAdd')}}</a>
                <a href="javascript:;" onclick="_UpdatePrivilege()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6bd;</i> {{__('admin.privilege.update')}}</a>
            </span>
            <span class="r">{{__('admin.countData')}}：<strong>{{$count}}</strong><i class="Hui-iconfont"> &#xe6c1;</i></span>
        </div>
        <table class="table table-border table-bordered table-hover table-bg">
            <thead>
            <tr>
                <th scope="col" colspan="6">{{__('admin.privilege.roleM')}}</th>
            </tr>
            <tr class="text-c">
                <th width="25"><input type="checkbox" value="" name=""></th>
                <th width="60">ID</th>
                <th width="200">{{__('admin.privilege.roleName')}}</th>
                <th>{{__('admin.privilege.roleDescription')}}</th>
                <th width="100">{{__('admin.privilege.roleNumber')}}</th>
                <th width="70">{{__('common.manage')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($admin_role_list as $item)
                <tr class="text-c">
                    <td><input type="checkbox" value="" name=""></td>
                    <td>{{$item['role_id']}}</td>
                    <td>{{$item['role_name']}}</td>
                    <td>{{$item['role_description']}}</td>
                    <td>{{$item['admin_user_count']}}</td>
                    <td class="f-14">
                        <a title="{{__('common.editor')}}" href="javascript:;" onclick="admin_role_edit('{{__('admin.privilege.roleEdit')}}','{{action('Admin\PrivilegeController@RoleView' , $item['role_id'])}}')" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                        <a title="{{__('common.delete')}}" href="javascript:;" onclick="admin_role_del(this,'{{$item['role_id']}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
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
        /*权限更新按钮请求函数*/
        function _UpdatePrivilege()
        {
            $.ajax({
                url:"{{action('Admin\PrivilegeController@PrivilegeUpdate')}}",//请求的url地址
                dataType:"json",   //返回格式为json
                data:{"_token":"{{csrf_token()}}"},    //参数值
                type:"POST",   //请求方式
                beforeSend:function(){
                    if(!NetStatus) return false;
                    NetStatus = false;
                },
                success:function(res){
                    if(res.code == 0)
                    {
                        layer.msg(res.messages,{icon:1,time:1000},function()
                        {
                            parent.location.replace(parent.location.href);
                        });
                    }
                    else
                    {
                        layer.msg(res.messages,{icon:2,time:1000});
                    }
                },
                complete:function(){
                    NetStatus = true;
                }
            });
        }

        /*管理员-角色-编辑*/
        function admin_role_edit(title,url,w,h){
            layer_show(title,url,w,h);
        }
        /*管理员-角色-删除*/
        function admin_role_del(obj,id){
            layer.confirm('{{__('admin.privilege.roleDeletePrompt')}}',function(index){
                $.ajax({
                    type: 'POST',
                    url: '{{action('Admin\PrivilegeController@RoleDeleteOne')}}',
                    dataType: 'json',
                    data:{
                        "_token":"{{csrf_token()}}",
                        "role_id":id
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
    </script>

@endsection

