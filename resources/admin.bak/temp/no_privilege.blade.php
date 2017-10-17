@extends('admin.layouts.master')
@section('content')
    <div class="page-container">

    </div>
@endsection

@section('MyJs')
    <script type="text/javascript">
        $(function()
        {
            layer.alert('{{__('admin.noPrivilege')}}', {
                skin: 'layui-layer-molv', //样式类名
                closeBtn: 0,
                title:'{{__('common.information')}}',
                shift: 6, //动画类型
                btn: '{{__('admin.goBack')}}'
            },
            function () {
                self.location=document.referrer;
            });
        });
    </script>
@endsection

