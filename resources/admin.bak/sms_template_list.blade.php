@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}
@section('content')
    @include('admin.include.inc_nav')
    <div class="page-container">
        <form action="{{action('Admin\SmsController@TemplateFuzzyQuery')}}" method="post" name="sms_fuzzy_query">
            {{csrf_field()}}
            <div class="text-c">
                <input type="text" class="input-text" style="width:250px" placeholder="输入模板名称" id="" name="fuzzy_query">
                <button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜模板</button>
            </div>
        </form>
        <div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l">
            <a href="javascript:;" onclick="sms_template_view('添加短信模板','{{action('Admin\SmsController@TemplateView')}}','','700')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加短信模板</a></span>
            <span class="r">{{__('admin.countData')}}：<strong>{{isset($count)?$count:0}}</strong> <i class="Hui-iconfont"> &#xe6c1;</i></span>
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-hover table-bg table-sort">
                <thead>
                <tr class="text-c">
                    <th width="25"><input type="checkbox" name="" value=""></th>
                    <th width="60">ID</th>
                    <th width="80">{{__('admin.sms.templateName')}}</th>
                    <th width="80">{{__('admin.sms.smsTemplate')}}</th>
                    <th width="80">{{__('admin.sms.aliTemplate')}}</th>
                    <th width="100">{{__('admin.sms.templateContent')}}</th>
                    <th width="80">{{__('admin.sms.operation')}}</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($sms_template))
                    @foreach($sms_template as $item)
                        <tr class="text-c">
                            <td><input type="checkbox" value="" name="ad_position_check"></td>
                            <td>{{$item->template_id}}</td>
                            <td>{{$item->template_name}}</td>
                            <td>{{$item->template_code}}</td>
                            <td>{{$item->aliyu_code}}</td>
                            <td>{{$item->template_content}}</td>
                            <td class="td-manage">
                                <a title="编辑" href="javascript:;" onclick="sms_template_view('{{__('common.editor')}}','{{action('Admin\SmsController@TemplateView')}}/{{$item->template_id}}','','700')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                                <a title="删除" href="javascript:;" onclick="sms_template_del(this,'{{$item->template_id}}')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            @include('admin.include.inc_pagination',['pagination'=>$sms_template])
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
        /*模板-添加*/
        function sms_template_view(title,url,w,h){
            layer_show(title,url,w,h);
        }

        /*广告位-删除*/
        function sms_template_del(obj,id){
            layer.confirm('确认要删除吗？',function(index){
                $.ajax({
                    type: 'POST',
                    url: '{{action('Admin\SmsController@TemplateDeleteOne')}}',
                    dataType: 'json',
                    data:{
                        "_token":"{{csrf_token()}}",
                        "template_id":id,
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