@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}

@section('content')
    @include('admin.include.inc_nav')
    <div class="page-container">
        <div class="cl pd-5 bg-1 bk-gray mt-20">
		<span class="l">
		    <a href="javascript:;" onclick="_del_admin_log()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> {{__('common.batchDelete')}}</a>
		</span>
            <span class="r">{{__('admin.countData')}}：<strong>{{$log_list->total()}}</strong><i class="Hui-iconfont"> &#xe6c1;</i></span>
        </div>
        <table class="table table-border table-bordered table-bg table-hover table-sort admin_log_table">
            <thead>
            <tr>
                <th scope="col" colspan="10">{{__('admin.privilege.logM')}}</th>
            </tr>
            <tr class="text-c">
                <th width="25"><input type="checkbox" name="" value=""></th>
                <th width="80">ID</th>
                <th width="100">{{__('admin.privilege.roleName')}}</th>
                <th width="100">{{__('admin.privilege.managerName')}}</th>
                <th>{{__('admin.privilege.logDescription')}}</th>
                <th width="120">{{__('common.ipAddress')}}</th>
                <th width="120">{{__('common.date')}}</th>
                <th width="70">{{__('common.manage')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($log_list as $item)
                <tr class="text-c">
                    <td><input class="delete_input" type="checkbox" value="{{$item['log_id']}}" name="delete_id" /></td>
                    <td>{{$item['log_id']}}</td>
                    <td>{{$item['admin_role']['role_name']}}</td>
                    <td>{{$item['admin_user']['admin_name']}}</td>
                    <td>{{$item['log_info']}}</td>
                    <td>{{$item['ip_address']}}</td>
                    <td>{{$item['created_at']}}</td>
                    <td>
                        <a title="{{__('common.view')}}" href="{{action('Admin\PrivilegeController@AdminLogIndex',$item['admin_id'])}}" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe665;</i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @include('admin.include.inc_pagination',['pagination'=>$log_list])
    </div>
    @include('admin.include.inc_footer')
@endsection

@section('MyJs')
    <!--请在下方写此页面业务相关的脚本-->
    <script type="text/javascript" src="{{asset('adminStatic/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/datatables/1.10.0/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/laypage/1.2/laypage.js')}}"></script>
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
                {"orderable":false,"aTargets":[0,7]}// 制定列不参与排序
            ],
            language: {
                sEmptyTable: "{{__('admin.dataTable.sEmptyTable')}}",
                sZeroRecords:"{{__('admin.dataTable.sZeroRecords')}}",
                search:"{{__('admin.dataTable.search')}}"
            }
        });

        /*日志批量删除*/
        function _del_admin_log(){
            var fd = new FormData();
                fd.append('_token','{{csrf_token()}}');
            $('.admin_log_table').find('.delete_input:checked').each(function(i){
                fd.append('delete_id[]',$(this).val());
            });

            layer.confirm('{{__('admin.deleteConfirm')}}',function(index){
                $.ajax({
                    type: 'POST',
                    url: '{{action('Admin\PrivilegeController@AdminLogBatchDelete')}}',
                    dataType: 'json',
                    data:fd,
                    processData : false,// 告诉jQuery不要去处理发送的数据
                    contentType : false,// 告诉jQuery不要去设置Content-Type请求头
                    beforeSend:function(){
                        layer_index = layer.loading();
                    },
                    success: function(data){
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
                    },
                    complete:function(){
                        layer.close(layer_index);
                    }
                });
            });
        }
    </script>

@endsection

