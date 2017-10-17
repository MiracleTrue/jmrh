@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}


@section('content')
    @include('admin.include.inc_nav')
    <div class="page-container">
        <div class="text-c">
            <input type="text" name="" id="" placeholder="{{__('admin.menu.searchHint')}}" style="width:250px" class="input-text">
            <button name="" id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i> {{__('common.search')}}</button>
        </div>
        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">
                <a class="btn btn-primary radius" onclick="system_category_add('{{__('admin.menu.add')}}','{{action('Admin\MenuController@MenusView')}}')" href="javascript:;"><i class="Hui-iconfont">&#xe600;</i> {{__('admin.menu.add')}}</a>
            </span>
            <span class="r">{{__('admin.countData')}}：<strong>{{$count}}</strong><i class="Hui-iconfont"> &#xe6c1;</i></span>
        </div>
        <div class="mt-20">
            <table class="table table-border table-bordered table-hover table-bg">
                <thead>
                <tr class="text-c">
                    <th width="25"><input type="checkbox" name="" value=""></th>
                    <th width="60">ID</th>
                    <th width="60">{{__('common.sort')}}</th>
                    <th width="32">{{__('admin.menu.icon')}}</th>
                    <th>{{__('admin.menu.name')}}</th>
                    <th>{{__('admin.menu.enName')}}</th>
                    <th>{{__('admin.menu.url')}}</th>
                    <th>{{__('admin.menu.controller')}}</th>
                    <th width="100">{{__('common.manage')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $value)
                    <tr class="text-c">
                        <td><input type="checkbox" name="" value=""></td>
                        <td>{{$value['menu_id']}}</td>
                        <td>{{$value['menu_sort']}}</td>
                        <td><i style="font-size: 24px" class="Hui-iconfont">{{$value['menu_icon']}}</i></td>
                        <td class="text-l">{{$value['menu_name']}}</td>
                        <td class="text-l">{{$value['menu_en_name']}}</td>
                        <td class="text-l">{{$value['menu_url']}}</td>
                        <td class="text-l">{{$value['menu_controller']}}</td>
                        <td class="f-14">
                            <a title="{{__('admin.menu.edit')}}" href="javascript:;" onclick="system_category_edit('{{__("admin.menu.edit")}}','{{action('Admin\MenuController@MenusView')}}/{{$value['menu_id']}}')" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                            <a href="javascript:;" onclick="system_category_del(this,'{{$value['menu_id']}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                        </td>
                    </tr>
                    @if($value['child'])
                        @foreach($value['child'] as $value)
                            <tr class="text-c">
                                <td><input type="checkbox" name="" value=""></td>
                                <td>{{$value['menu_id']}}</td>
                                <td>{{$value['menu_sort']}}</td>
                                <td><i style="font-size: 24px" class="Hui-iconfont">{{$value['menu_icon']}}</i></td>
                                <td class="text-l">&nbsp;&nbsp;├&nbsp;{{$value['menu_name']}}</td>
                                <td class="text-l">&nbsp;&nbsp;├&nbsp;{{$value['menu_en_name']}}</td>
                                <td class="text-l">{{$value['menu_url']}}</td>
                                <td class="text-l">{{$value['menu_controller']}}</td>
                                <td class="f-14">
                                    <a title="{{__('admin.menu.edit')}}" href="javascript:;" onclick="system_category_edit('{{__("admin.menu.edit")}}','{{action('Admin\MenuController@MenusView')}}/{{$value['menu_id']}}')" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                                    <a href="javascript:;" onclick="system_category_del(this,'{{$value['menu_id']}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
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
            "aaSorting": [[ 1, "desc" ]],//默认第几个排序
            "bStateSave": true,//状态保存
            "aoColumnDefs": [
                //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
                {"orderable":false,"aTargets":[0,4]}// 制定列不参与排序
            ]
        });
        /*系统-栏目-添加*/
        function system_category_add(title,url,w,h){
            layer_show(title,url,w,h);
        }
        /*系统-栏目-编辑*/
        function system_category_edit(title,url,w,h){
            layer_show(title,url,w,h);
        }
        /*系统-栏目-删除*/
        function system_category_del(obj,id){
            layer.confirm('{{__('admin.menu.deleteConfirm')}}',function(index){
                $.ajax({
                    type: 'POST',
                    url: '{{action('Admin\MenuController@MenusDeleteOne')}}',
                    data:{
                        "_token":"{{csrf_token()}}",
                        "menu_id":id
                    },
                    dataType: 'json',
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

                    }
                });
            });
        }
    </script>
@endsection

