@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}

@section('content')
    @include('admin.include.inc_nav')
    <div class="page-container">
        <div class="pd-5 mb-10 bg-1 bk-gray prompt">
            <div class="pl-30"><i class="Hui-iconfont c-primary">&#xe64b;</i><span class="pl-5 f-12 c-primary">{{__('admin.operationPrompt')}}</span></div>
        </div>

        <form method="post" enctype="multipart/form-data" class="form form-horizontal" id="form-setting-view" >
            {{csrf_field()}}
            <div id="tab-system" class="HuiTab">
                <div class="tabBar cl">
                    <span>{{__('admin.sms.smsSettings')}}</span>
                </div>
                <input type="hidden" name="merchant_id" value="0">
                <input type="hidden" name="sms_id" value="{{isset($sms_config->sms_id)?$sms_config->sms_id :'0'}}">
                <div class="tabCon">
                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.sms.smsType')}}：</label>
                        <div class="formControls col-xs-8 col-sm-9 skin-minimal " >
                            <div class="radio-box" >
                                <input class="lang_slect" name="sms_type" value="0" type="radio" id="sms_type-1"  checked >
                                <label for="sms_type-1" >{{__('admin.sms.alidaYu')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.sms.appKey')}}：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" id="appkey" value="{{isset($sms_config->appkey)?$sms_config->appkey:"" }}" class="input-text" name="appkey">
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.sms.secretKey')}}：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{isset($sms_config->secretKey)?$sms_config->secretKey:"" }}" id="secretKey" name="secretKey" >
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.sms.smsSignature')}}：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{isset($sms_config->signName)?$sms_config->signName:"" }}" id="signName" name="signName" >
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
            $("#form-setting-view").validate({
                rules:{
                    sms_type:{
                        required:true,
                        number:true,
                    },
                    appkey:{
                        required:true,
                    },
                    secretKey:{
                        required:true,
                    },
                    signName:{
                        required:true,
                        minlength:2,
                        maxlength:16,
                    },
                },
                onkeyup:false,
                focusCleanup:true,
                success:"valid",
                submitHandler:function(form){
                    $(form).ajaxSubmit({
                        url:"{{action('Admin\SmsController@SettingEditSubmit')}}",
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






