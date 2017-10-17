@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}
@section('content')
    @include('admin.include.inc_nav')
    <div class="page-container">
        <div class="pd-5 mb-10 bg-1 bk-gray prompt">
            <div class="pl-30"><i class="Hui-iconfont c-primary">&#xe64b;</i><span class="pl-5 f-12 c-primary">{{__('admin.operationPrompt')}}</span></div>
            <div class="pl-30 pr-30 cl">
                @if(isset($note_array)&& $note_array!=null )
                    @foreach($note_array as $k=>$v)
                        @if($k != 'validate')
                            <div class="col-sm-4 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">{{$k}}      {{$v['zh_desc']}}</span></div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
        <form action="" method="post" class="form form-horizontal" id="form-template-view">
            {{csrf_field()}}
            <input type="hidden" name="merchant_id" value="0">
            <input type="hidden" name="template_id" value="{{$sms_template->template_id or '0'}}">
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.sms.temName')}}：</label>
                <div class="formControls col-xs-6 col-sm-9">
                    <input type="text" class="input-text" value="{{$sms_template->template_name or ''}}" placeholder="" id="template-name" name="template_name">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.sms.templateCode')}}：</label>
                <div class="formControls col-xs-6 col-sm-9">
                    <input type="text" class="input-text" value="{{$sms_template->template_code or ''}}" placeholder="" {{isset($sms_template->template_code)?'readonly="readonly"':''}} id="user-name" name="template_code">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.sms.aliYuCode')}}：</label>
                <div class="formControls col-xs-6 col-sm-9">
                    <input type="text" class="input-text" value="{{$sms_template->aliyu_code or ''}}" placeholder="{{__('admin.sms.aliYuCodeNotes')}}" id="user-name" name="aliyu_code">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.sms.templateContent')}}：</label>
                <div class="formControls col-xs-6 col-sm-9">
                    <textarea name="template_content" cols="" rows="" class="textarea"  placeholder="{{__('admin.sms.templateContentNotes')}}" onKeyUp="$.Huitextarealength(this,100)">{{$sms_template->template_content or ''}}</textarea>
                    <p class="textarea-numberbar"><em class="textarea-length">0</em>/100</p>
                </div>
            </div>
            <div class="row cl">
                <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                    <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;{{__('common.submit')}}&nbsp;&nbsp;">
                </div>
            </div>
        </form>
        @if(isset($sms_template))
            <form action="" method="post" class="form form-horizontal" id="form-sms-send">
                {{csrf_field()}}
                <input type="hidden" name="aliyu_code" value="SMS_71585007">
                <input type="hidden" name="template_code" value="send_password">
                <div class="row cl" >
                    <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.sms.smsReceiver')}}：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <input type="text" class="input-text" value="" placeholder="{{__('admin.sms.smsReceiverNotes')}}" id="user-name" name="recNum">
                    </div>
                </div>
                <div class="row cl">
                    <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                        <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;{{__('admin.sms.sendSmsTest')}}&nbsp;&nbsp;">
                    </div>
                </div>
            </form>
        @endif
    </div>
    @endsection

    @section('MyJs')
            <!--请在下方写此页面业务相关的脚本-->
    <script type="text/javascript" src="{{asset('adminStatic/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/messages_zh.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/messages_'.session('AdminLanguage').'.js')}}"></script>

    <script type="text/javascript">
        $(function(){
            $('.skin-minimal input').iCheck({
                checkboxClass: 'icheckbox-blue',
                radioClass: 'iradio-blue',
                increaseArea: '20%'
            });

            $("#form-template-view").validate({
                rules:{
                    template_name:{
                        required:true,
                        minlength:2,
                        maxlength:16,
                    },
                    template_code:{
                        required:true,
                        minlength:2,
                        maxlength:16,
                    },
                    aliyu_code:{
                        required:true,
                        minlength:2,
                        maxlength:16,
                    },
                    template_content:{
                        required:true,
                    },
                },
                onkeyup:false,
                focusCleanup:true,
                success:"valid",
                submitHandler:function(form){
                    $(form).ajaxSubmit({
                        url:'{{action('Admin\SmsController@TemplateEditSubmit')}}',
                        type:'POST',
                        dataType:'JSON',
                        success:function(res)
                        {
                            if(res.code == 0)
                            {
                                layer.msg(res.messages,{icon:1,time:1000},function()
                                {
                                    NetStatus = true;
                                    parent.location.replace(parent.location.href);
                                    //parent.layer.close(index);
                                });
                            }
                            else
                            {
                                layer.msg(res.messages,{icon:2,time:1000});
                            }
                        }

                    });
                    return false;
                }
            });
            $("#form-sms-send").validate({
                rules:{
                    recNum:{
                        required:true,
                        isMobile:true,
                    },
                },
                onkeyup:false,
                focusCleanup:true,
                success:"valid",
                submitHandler:function(form){
                    $(form).ajaxSubmit({
                        url:'{{action('Admin\SmsController@TemplateSendSms')}}',
                        type:'POST',
                        dataType:'JSON',
                        success:function(res)
                        {
                            if(res.code == 0)
                            {
                                layer.msg(res.messages,{icon:1,time:1000},function()
                                {
                                    NetStatus = true;
                                    parent.location.replace(parent.location.href);
                                    //parent.layer.close(index);
                                });
                            }
                            else
                            {
                                layer.msg(res.messages,{icon:2,time:1000});
                            }
                        }

                    });
                    return false;
                }
            });
        });
    </script>
@endsection