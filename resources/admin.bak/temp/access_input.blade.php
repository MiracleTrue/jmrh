@extends('admin.layouts.master')
@section('content')
    <div class="page-container">

    </div>
@endsection

@section('MyJs')
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/messages_'.session('AdminLanguage').'.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            layer.prompt({title: '{{$layer_title or '默认标题'}}',value:'{{$layer_value or ''}}', formType: '{{$layer_formType or 0}}'}, function(value, index) {

                var load_index;
                $.ajax({
                    url:"{{$ajax_url or ''}}",    //请求的url地址
                    dataType:"json",   //返回格式为json
                    data:{
                        "_token":"{{csrf_token()}}",
                        "input_value":value
                    },    //参数值
                    type:"POST",   //请求方式
                    beforeSend:function(xhr, settings){
                        layer.close(index);
                        load_index = layer.load(2);
                    },
                    success:function(res){
                        layer.close(load_index);
                        layer.msg(res.messages);
                        location.replace('{{$ajax_back}}');
                    }
                });

            });
        });
    </script>
@endsection

