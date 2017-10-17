@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}

@section('content')
@include('admin.include.inc_nav')
<div class="page-container">
    <div class="pd-5 mb-10 bg-1 bk-gray prompt">
        <div class="pl-30"><i class="Hui-iconfont c-primary">&#xe64b;</i><span class="pl-5 f-12 c-primary">{{__('admin.operationPrompt')}}</span></div>
    </div>
    
    <form method="post" enctype="multipart/form-data" class="form form-horizontal" id="form-server-index" >
        {{csrf_field()}}
        <div id="tab-system" class="HuiTab">
            <div class="tabBar cl">
                <span>{{__('admin.email.mailSetting')}}</span>
            </div>
            <input name="identity" value="0" type="hidden">
            <div class="tabCon">
                <input type="hidden" name="emailConfig_id" value="{{isset($emailConfig)?$emailConfig->id:""}}">
                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.email.server')}}：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <input type="text" id="" value="{{isset($emailConfig)?$emailConfig->smtp_server:"" }}" class="input-text" name="smtpServer">
                    </div>
                </div>
                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.email.port')}}：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <input type="text" class="input-text" value="{{isset($emailConfig)?$emailConfig->port:"" }}" id="" name="port" >
                    </div>
                </div>
                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.email.account')}}：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <input type="text" class="input-text" value="{{isset($emailConfig)?$emailConfig->email_from:"" }}" id="emailName" name="emailFrom" >
                    </div>
                </div>
                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.email.password')}}：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <input type="password" id="email-password" value="{{isset($emailConfig)?$emailConfig->password:"" }}" class="input-text" name="emailPossword">
                    </div>
                </div>
            </div>
            <div class="tabCon">
            </div>
        </div>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> {{__('common.submit')}}</button>
                <button onClick="removeIframe();" class="btn btn-default radius" type="button">&nbsp;&nbsp;{{__('common.cancel')}}&nbsp;&nbsp;</button>
            </div>
        </div>
    </form>
</div>

@include('admin.include.inc_footer')
@endsection

@section('MyJs')
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/messages_zh.js')}}"></script>
    <script type="text/javascript">
    $(function(){
        $('.skin-minimal input').iCheck({
            checkboxClass: 'icheckbox-blue',
            radioClass: 'iradio-blue',
            increaseArea: '20%'
        });
        $.Huitab("#tab-system .tabBar span","#tab-system .tabCon","current","click","0");
        //表单验证
        $("#form-server-index").validate({
            rules:{
                smtpServer:{
                    required:true,
                },
                port:{
                    required:true,
                    number:true,
                },
                emailFrom:{
                    required:true,
                    email:true,
                },
                emailPossword:{
                    required:true,
                },
            },
            onkeyup:false,
            focusCleanup:true,
            success:"valid",
            submitHandler:function(form){
                $(form).ajaxSubmit({
                    url:"{{action('Admin\EmailController@ServerEditSubmit')}}",
                    type: 'POST',
                    dataType: 'JSON',
                    success:function(res){
                        if(res.code == 0)
                        {
                            layer.msg(res.messages,{icon:1,time:1000});
                        }
                        else
                        {
                            layer.msg(res.messages,{icon:2,time:1000});
                        }
                    }
                });
            }
        });
    });

</script>
@endsection






