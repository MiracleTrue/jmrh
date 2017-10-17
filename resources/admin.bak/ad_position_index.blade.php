@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}
@section('content')
@include('admin.include.inc_nav')
<div class="page-container">
    <form action="{{action('Admin\AdvertController@PositionFuzzyQuery')}}"  name="picture_fuzzy_query" method="post">
        {{csrf_field()}}
        <div class="text-c">
            {{--日期范围：--}}
            {{--<input type="text" onfocus="WdatePicker({ maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}' })" id="datemin" class="input-text Wdate" style="width:120px;">--}}
            {{-----}}
            {{--<input type="text" onfocus="WdatePicker({ minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d' })" id="datemax" class="input-text Wdate" style="width:120px;">--}}
            <input type="text" class="input-text" style="width:250px" placeholder="输入广告名称" id="" name="position_name">
            <button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜图片位</button>
        </div>
    </form>
    <div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l">
            <a href="javascript:;" onclick="picturePosition_add('{{__('admin.picture.add_position')}}','{{action('Admin\AdvertController@PositionView')}}','','700')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> {{__('admin.picture.add_position')}}</a></span>
        <span class="r">{{__('admin.countData')}}：<strong>{{isset($count)?$count:0}}</strong> <i class="Hui-iconfont"> &#xe6c1;</i></span>
    </div>
    <div class="mt-20">
        <table class="table table-border table-bordered table-hover table-bg table-sort">
            <thead>
            <tr class="text-c">
                <th width="25"><input type="checkbox" name="" value=""></th>
                <th width="80">ID</th>
                <th width="100">{{__('admin.picture.position_name')}}</th>
                <th width="50">{{__('admin.picture.ad_width')}}</th>
                <th width="50">{{__('admin.picture.ad_height')}}</th>
                <th width="130">{{__('admin.picture.position_desc')}}</th>
                <th width="70">{{__('admin.picture.state')}}</th>
                <th width="100">{{__('admin.picture.operation')}}</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($picture_position))
                @foreach($picture_position as $item)
                    <tr class="text-c">
                        <td><input type="checkbox" value="{{$item->position_id}}" name="ad_position_check"></td>
                        <td>{{$item->position_id}}</td>
                        <td name="position_name"><u style="cursor:pointer" class="text-primary">{{$item->position_name}}</u></td>
                        <td>{{$item->ad_width}}</td>
                        <td>{{$item->ad_height}}</td>
                        <td>{{$item->position_desc}}</td>
                        <td class="td-status">
                            <span class='{{$item->status == 0?"label label-success radius":"label label-defaunt radius"}}'>{{$item->status == 0?"已启用":"已停用"}}</span>
                        </td>
                        <td class="td-manage">
                            @if($item->status == 0)
                               <a style="text-decoration:none" onClick="picturePosition_stop(this,'{{$item->position_id}}')" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>
                            @else
                               <a style="text-decoration:none" onClick="picturePosition_start(this,'{{$item->position_id}}')" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe6e1;</i></a>
                            @endif
                            <a title="编辑" href="javascript:;" onclick="picturePosition_show('编辑广告位','{{action('Admin\AdvertController@PositionView')}}/{{$item->position_id}}','4','','700')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                            <a title="删除" href="javascript:;" onclick="picturePosition_del(this,'{{$item->position_id}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                            <a title="查看" href="javascript:;" onclick="picture_show('查看广告','{{action('Admin\AdvertController@EntityView')}}/{{$item->position_id}}')" class="ml-5" style="text-decoration:none">{{__('admin.picture.view')}}</a>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
        @include('admin.include.inc_pagination',['pagination'=>$picture_position])
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
    /*图片位-添加*/
    function picturePosition_add(title,url,w,h){
        layer_show(title,url,w,h);
    }
    /*图片位-查看*/
    function picturePosition_show(title,url,id,w,h){
        layer_show(title,url,w,h);
    }
    function picture_show(title,url)
    {
        var index = layer.open({
            type: 2,
            title: title,
            content: url
        });
        layer.full(index);
    }
    /*图片位-停用*/
    function picturePosition_stop(obj,id){
        layer.confirm('确认要停用吗？',function(index){
            $.ajax({
                type: 'POST',
                url: '{{action('Admin\AdvertController@PositionQuickEdit')}}',
                dataType: 'json',
                data:{
                    "_token":"{{csrf_token()}}",
                    "position_id":id,
                    "status":1,
                },
                success: function(data){
                    if(data.code == 0)
                    {
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="picturePosition_start(this,'+data.data+')" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe6e1;</i></a>');
                        $(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已停用</span>');
                        $(obj).remove();
                        layer.msg(data.messages,{icon:1,time:1000});
                    }
                    else
                    {
                        $(obj).remove();
                        layer.msg(data.messages,{icon:2,time:1000});
                    }

                },
                error:function(data) {
                    console.log(data.msg);
                },
            });
        });
    }

    /*图片位-启用*/
    function picturePosition_start(obj,id){
        layer.confirm('确认要启用吗？',function(index){
            $.ajax({
                type: 'POST',
                url: '{{action('Admin\AdvertController@PositionQuickEdit')}}',
                dataType: 'json',
                data:{
                    "_token":"{{csrf_token()}}",
                    "position_id":id,
                    "status":0,
                },
                success: function(data){
                    if(data.code == 0)
                    {
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="picturePosition_stop(this,'+data.data+')" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>');
                        $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
                        $(obj).remove();
                        layer.msg(data.messages,{icon: 1,time:1000});
                    }
                    else
                    {
                        //$(obj).remove();
                        layer.msg(data.messages,{icon:2,time:1000});
                    }

                },
                error:function(data) {
                    console.log(data.msg);
                },
            });
        });
    }

    /*图片位-删除*/
    function picturePosition_del(obj,id){
        layer.confirm('确认要删除吗？',function(index){
            $.ajax({
                type: 'POST',
                url: '{{action('Admin\AdvertController@PositionDelOne')}}',
                dataType: 'json',
                data:{
                    "_token":"{{csrf_token()}}",
                    "position_id":id,
                },
                success: function(data){
                    if(data.code == 0){
                        $(obj).parents("tr").remove();
                        layer.msg(data.messages,{icon:1,time:1000});
                    }
                    else
                    {
                        layer.msg(data.messages,{icon:2,time:1000});
                    }
                },
                error:function(data) {
                    console.log(data.msg);
                },
            });
        });
    }

</script>
@endsection