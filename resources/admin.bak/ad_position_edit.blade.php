@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}
@section('content')
<article class="page-container">
    <form action="" method="post" class="form form-horizontal" id="form-ad-view">
        {{csrf_field()}}
        <input type="hidden" name="position_id" value="{{$picturePosition->position_id or '0'}}">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>{{__('admin.picture.position_name')}}：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{$picturePosition->position_name or ''}}" placeholder="" id="position_name" name="position_name">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">{{__('admin.picture.en_name')}}：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{$picturePosition->en_position_name or ''}}" placeholder="" id="en_position_name" name="en_position_name">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>{{__('admin.picture.ad_width')}}：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{$picturePosition->ad_width or ''}}" placeholder="" id="ad_width" name="ad_width">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>{{__('admin.picture.ad_height')}}：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" placeholder="" name="ad_height" id="ad_height" value="{{$picturePosition->ad_height or ''}}">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>{{__('admin.isEnable')}}：</label>
            <div class="formControls col-xs-8 col-sm-9 skin-minimal">
                <div class="radio-box">
                    <input name="status" type="radio" id="status-1"  value="0" {{isset($picturePosition)? $picturePosition->status==0?'checked':'':'checked'}}>
                    <label for="status-1">{{__('admin.enable')}}</label>
                </div>
                <div class="radio-box">
                    <input type="radio" id="status-2" name="status" value="1" {{isset($picturePosition)? $picturePosition->status==1?'checked':'':''}}>
                    <label for="status-2">{{__('admin.disable')}}</label>
                </div>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">{{__('admin.picture.position_desc')}}：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <textarea name="position_desc" cols="" rows="" class="textarea"  placeholder="说点什么...最少输入10个字符" onKeyUp="$.Huitextarealength(this,100)">{{$picturePosition->position_desc or ''}}</textarea>
                <p class="textarea-numberbar"><em class="textarea-length">0</em>/100</p>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">{{__('admin.picture.en_desc')}}：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <textarea name="en_position_desc" cols="" rows="" class="textarea"  placeholder="说点什么...最少输入10个字符" onKeyUp="$.Huitextarealength(this,100)">{{$picturePosition->en_position_desc or ''}}</textarea>
                <p class="textarea-numberbar"><em class="textarea-length">0</em>/100</p>
            </div>
        </div>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;{{__('common.submit')}}&nbsp;&nbsp;">
            </div>
        </div>
    </form>
</article>
@endsection

@section('MyJs')
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="{{asset('adminStatic/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
<script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
<script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
<script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/messages_zh.js')}}"></script>
<script type="text/javascript">
    $(function(){
        $('.skin-minimal input').iCheck({
            checkboxClass: 'icheckbox-blue',
            radioClass: 'iradio-blue',
            increaseArea: '20%'
        });

        $("#form-ad-view").validate({
            rules:{
                position_name:{
                    required:true,
                    minlength:4,
                    maxlength:20,
                },
                en_position_name:{
                    minlength:4,
                    maxlength:100,
                },
                ad_width:{
                    required:true,
                    number:true,
                },
                ad_height:{
                    required:true,
                    number:true,
                },
                status:{
                    required:true,
                },
            },
            onkeyup:false,
            focusCleanup:true,
            success:"valid",
            submitHandler:function(form){
                $(form).ajaxSubmit({
                    url:'{{action('Admin\AdvertController@PositionSubmit')}}',
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