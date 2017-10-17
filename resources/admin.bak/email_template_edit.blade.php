@extends('admin.layouts.master')

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
    <form action="" method="post" class="form form-horizontal" id="form-email-template-edit">
        {{csrf_field()}}
        <input type="hidden" name="merchant_id" value="0">
        <input type="hidden" name="template_code" value="{{isset($one_template)?$one_template->template_code:""}}">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.email.changeTemplate')}}：</label>
            <div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
				<select name="template" class="select select_mail" id="template">
                    <option>-{{__('admin.email.changeTemplate')}}-</option>
                    @foreach($mail_template as $template_item)
                    <option value="{{$template_item->template_id}}">{{$template_item->template_subject." [".$template_item->template_code."]"}}</option>
                    @endforeach
                </select>
				</span> </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.email.zh_subject')}}：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{isset($one_template)?$one_template->template_subject:""}}" name="subject" id="subject">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.email.zh_content')}}：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <script id="ueditorId_1" type="text/plain" style="width:100%;height:400px;" name="content" class="zh_content"><?php echo isset($one_template)?$one_template->template_content:""; ?></script>
            </div>
        </div>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> {{__('common.submit')}}</button>
                <button onClick="removeIframe();" class="btn btn-default radius" type="button">&nbsp;&nbsp;{{__('common.cancel')}}&nbsp;&nbsp;</button>
            </div>
        </div>
    </form>
    <form action="" method="post" class="form form-horizontal" id="form-sms-send">
        {{csrf_field()}}
        <input type="hidden" name="merchant_id" value="0">
        <input type="hidden" name="template_code" value="send_password">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.email.sendEmail')}}:</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" placeholder="{{__('admin.email.sendEmailNotes')}}" name="emailTo">
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

@endsection

@section('MyJs')
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/messages_zh.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/webuploader/0.1.5/webuploader.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/ueditor/1.4.3/ueditor.config.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/ueditor/1.4.3/ueditor.all.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js')}}"></script>

<script type="text/javascript">
    /*更换邮件模板，相应更换邮件主题和模板内容 */
    $(function(){
        UE.getEditor('ueditorId_1');
        UE.getEditor('ueditorId_2');
        var zh = true;
        $('.select_mail').change(function(){
            var checkValue=$("#template").val();
            window.location.href ="{{action('Admin\EmailController@GetOneTemplateById')}}"+"/"+checkValue;
        });

        $('.skin-minimal input').iCheck({
            checkboxClass: 'icheckbox-blue',
            radioClass: 'iradio-blue',
            increaseArea: '20%'
        });

/*
        $('.lang_slect').on('ifChecked', function(event){
            if(zh == true)
            {
                zh = false;
                $('#zh').hide();
                $('#en').show();
            }
            else {
                zh = true;
                $('#en').hide();
                $('#zh').show();
            }
        });
*/
        //模板表单验证
        $("#form-email-template-edit").validate({
            rules:{
                template:{
                    required:true,
                },
                subject:{
                    required:true,
                    minlength:2,
                    maxlength:16,
                },
                content:{
                    required:true,
                },
            },
            onkeyup:false,
            focusCleanup:true,
            success:"valid",
            submitHandler:function(form){
                //var index = parent.layer.getFrameIndex(window.name);
                $(form).ajaxSubmit({
                    url: '{{action('Admin\EmailController@TemplateEditSubmit')}}',
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
                //parent.$('.btn-refresh').click();
                //parent.layer.close(index);
            }
        });
        //发送邮件表单验证
        $("#form-sms-send").validate({
            rules:{
                emailTo:{
                    required:true,
                    email:true,
                },
            },
            onkeyup:false,
            focusCleanup:true,
            success:"valid",
            submitHandler:function(form){
                //var index = parent.layer.getFrameIndex(window.name);
                $(form).ajaxSubmit({
                    url: '{{action('Admin\EmailController@SendTest')}}',
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
                //parent.$('.btn-refresh').click();
                //parent.layer.close(index);
            }
        });

        $list = $("#fileList"),
                $btn = $("#btn-star"),
                state = "pending",
                uploader;

        var uploader = WebUploader.create({
            auto: true,
            swf: 'lib/webuploader/0.1.5/Uploader.swf',

            // 文件接收服务端。
            server: 'fileupload.php',

            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#filePicker',

            // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
            resize: false,
            // 只允许选择图片文件。
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/*'
            }
        });
        uploader.on( 'fileQueued', function( file ) {
            var $li = $(
                            '<div id="' + file.id + '" class="item">' +
                            '<div class="pic-box"><img></div>'+
                            '<div class="info">' + file.name + '</div>' +
                            '<p class="state">等待上传...</p>'+
                            '</div>'
                    ),
                    $img = $li.find('img');
            $list.append( $li );

            // 创建缩略图
            // 如果为非图片文件，可以不用调用此方法。
            // thumbnailWidth x thumbnailHeight 为 100 x 100
            uploader.makeThumb( file, function( error, src ) {
                if ( error ) {
                    $img.replaceWith('<span>不能预览</span>');
                    return;
                }

                $img.attr( 'src', src );
            }, thumbnailWidth, thumbnailHeight );
        });
        // 文件上传过程中创建进度条实时显示。
        uploader.on( 'uploadProgress', function( file, percentage ) {
            var $li = $( '#'+file.id ),
                    $percent = $li.find('.progress-box .sr-only');

            // 避免重复创建
            if ( !$percent.length ) {
                $percent = $('<div class="progress-box"><span class="progress-bar radius"><span class="sr-only" style="width:0%"></span></span></div>').appendTo( $li ).find('.sr-only');
            }
            $li.find(".state").text("上传中");
            $percent.css( 'width', percentage * 100 + '%' );
        });

        // 文件上传成功，给item添加成功class, 用样式标记上传成功。
        uploader.on( 'uploadSuccess', function( file ) {
            $( '#'+file.id ).addClass('upload-state-success').find(".state").text("已上传");
        });

        // 文件上传失败，显示上传出错。
        uploader.on( 'uploadError', function( file ) {
            $( '#'+file.id ).addClass('upload-state-error').find(".state").text("上传出错");
        });

        // 完成上传完了，成功或者失败，先删除进度条。
        uploader.on( 'uploadComplete', function( file ) {
            $( '#'+file.id ).find('.progress-box').fadeOut();
        });
        uploader.on('all', function (type) {
            if (type === 'startUpload') {
                state = 'uploading';
            } else if (type === 'stopUpload') {
                state = 'paused';
            } else if (type === 'uploadFinished') {
                state = 'done';
            }

            if (state === 'uploading') {
                $btn.text('暂停上传');
            } else {
                $btn.text('开始上传');
            }
        });

        $btn.on('click', function () {
            if (state === 'uploading') {
                uploader.stop();
            } else {
                uploader.upload();
            }
        });

        var ue = UE.getEditor('editor');

    });
</script>
@endsection