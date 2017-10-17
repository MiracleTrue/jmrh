@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}
<link rel="stylesheet" type="text/css" href="{{URL::asset('adminStatic/lib/webuploader/0.1.5/webuploader.css')}}" />
@section('content')
<div class="page-container">
    <input class="btn btn-primary size-L radius" type="button" value="+ {{__('admin.picture.add_adver')}}" onclick="show_addPicture()">
    <div id="add_adver">
        <form action="" method="post" class="form form-horizontal" id="form-ad-add"  enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" name="ad_id" value="0">
            <input type="hidden" name="position_id" value="{{$position_id}}">
            <input type="reset" name="reset" style="display: none;" />
            <table>
                <tr>
                    <td width="5%" align="center">
                    </td>
                    <td width="35%" align="center">
                        <div class="div-0 picture_entity_add_file_div">
                            <input type="file" class="picture_entity_add_file" name="advert_file" onchange="selectedfile(0,0)" id="myfile-0">
                        </div>
                    </td>
                    <td width="60%">
                        <div class="row cl">
                            <label class="form-label col-xs-4 col-sm-2">{{__('admin.picture.ad_link')}}：</label>
                            <div class="formControls col-xs-8 col-sm-9">
                                <input type="text" name="ad_link" id="" placeholder=""  class="input-text"  style="width:330px" >
                            </div>
                        </div>
                        <div class="row cl">
                            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.picture.effective_time')}}：</label>
                            <div class="formControls col-xs-8 col-sm-9">

                                <input type="text"  name="start_time" onfocus="WdatePicker({ dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}' })" id="datemin" class="input-text Wdate" style="width:160px;">
                                -
                                <input type="text"  name="end_time" onfocus="WdatePicker({ dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'datemin\')}' })" id="datemax" class="input-text Wdate" style="width:160px;">
                            </div>
                        </div>
                        <div class="row cl">
                            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                                <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> {{__('common.submit')}}</button>
                                <button onClick="addAdver_hide();" class="btn btn-default radius" type="button">&nbsp;&nbsp;{{__('common.delete')}}&nbsp;&nbsp;</button>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    @foreach($position as $item)
        <form action="" method="post" class="form form-horizontal form-ad-view"  enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" name="ad_id" value="{{$item->ad_id or '0'}}">
            <input type="hidden" name="position_id" value="{{$position_id}}">
            <div class="mt-20">
                <table class="table table-border table-bordered table-hover table-bg table-sort">
                    <thead>
                    <tr class="text-c">
                        <th width="100">ID</th>
                        <th width="100">{{__('admin.picture.ad_display')}}</th>
                        <th width="130">{{__('admin.picture.ad_operat')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr class="text-c">
                            <td>
                                {{$item->ad_id or '0'}}
                            </td>
                            <td>
                                <div>
                                    <img src="{{URL::asset('uploads/'.$item->ad_path.'')}}"  class="picture_change_img img-{{$item->ad_id or '0'}}" alt="请重新上传图片">
                                </div>
                            </td>
                            <td>
                                <div class="row cl">
                                    <label class="form-label col-xs-4 col-sm-2">{{__('admin.picture.ad_link')}}：</label>
                                    <div class="formControls col-xs-8 col-sm-9">
                                        <input type="text" name="ad_link" id="" placeholder=""  class="input-text"  style="width:370px" value="{{$item->ad_link or ''}}">
                                    </div>
                                </div>
                                <div class="row cl">
                                    <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>{{__('admin.picture.effective_time')}}：</label>
                                    <div class="formControls col-xs-8 col-sm-9">
                                        <input type="text" value="{{isset($item->start_time)?date("Y-m-d H:i:s",$item->start_time):''}}" name="start_time" onfocus="WdatePicker({ dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}' })" id="datemin" class="input-text Wdate" style="width:180px;">
                                        -
                                        <input type="text" value="{{isset($item->end_time)?date("Y-m-d H:i:s",$item->end_time):''}}" name="end_time" onfocus="WdatePicker({ dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'datemin\')}' })" id="datemax" class="input-text Wdate" style="width:180px;">
                                    </div>
                                </div>
                                <div class="row cl">
                                    <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                                        <label for="myfile-{{$item->ad_id or '0'}}" class="btn btn-primary radius">
                                            <input id="myfile-{{$item->ad_id or '0'}}" type="file" onchange="selectedfile('{{$item->ad_id or '0'}}',1)" style="display: none" name="advert_file"/>
                                            <i class="Hui-iconfont">&#xe642;</i> {{__('admin.browse')}}&nbsp;
                                        </label>
                                        <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> {{__('common.submit')}}</button>
                                        <a title="删除" href="javascript:;" onclick="position_del(this,'{{$item->ad_id}}')" class="ml-5" style="text-decoration:none">
                                            <button class="btn btn-default radius" type="button">&nbsp;&nbsp;{{__('common.delete')}}&nbsp;&nbsp;</button>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    @endforeach
</div>
@endsection

@section('MyJs')
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/messages_zh.js')}}"></script>

<script type="text/javascript">
    /*显示添加图片*/
    function show_addPicture()
    {
        $('#add_adver').show();
    }
    function addAdver_hide()
    {
        $('#add_adver').hide();
        $("input[type=reset]").trigger("click");
        $("label.error").hide();
        $(".error").removeClass("error")
    }
    /*图片-上传临时保存*/
    function selectedfile($ad_id,issAdd)
    {
        _UploadImagePreviewOne($("#myfile-"+$ad_id+"") , function(path) {
            if(issAdd == 0)
            {
                $('.div-'+$ad_id).css("background-image","url("+path+")");
            }
            else
            {
                $('.img-'+$ad_id).attr('src',path);
            }

        });
    }
    /*图片-删除*/
    function position_del(obj,id){
        layer.confirm('确认要删除吗？',function(index){
            $.ajax({
                type: 'POST',
                url: '{{action('Admin\AdvertController@EntityDelOne')}}',
                dataType: 'json',
                data:{
                    "_token":"{{csrf_token()}}",
                    "ad_id":id,
                },
                success: function(data){
                    if(data.code == 0){
                        $(obj).parents("table").remove();
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
    $(function(){
        $('#add_adver').hide();
        $('.skin-minimal input').iCheck({
            checkboxClass: 'icheckbox-blue',
            radioClass: 'iradio-blue',
            increaseArea: '20%'
        });
        $(".form-ad-view").validate({
            rules:{
                ad_link:{
                    minlength:2,
                    maxlength:200,
                },
                start_time:{
                    required:true,
                    date:true,
                },
                end_time:{
                    required:true,
                    date:true,
                },
            },
            onkeyup:false,
            focusCleanup:true,
            success:"valid",
            submitHandler:function(form){
                $(form).ajaxSubmit({
                    url:'{{action('Admin\AdvertController@EntitySubmit')}}',
                    type:'POST',
                    dataType:'JSON',
                    success:function(res)
                    {
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
                return false;
            }
        });

        $("#form-ad-add").validate({
            rules:{
//                advert_file: {
//                    required:true,
//                },
                ad_link:{
                    minlength:2,
                    maxlength:200,
                },
                start_time:{
                    required:true,
                    date:true,
                },
                end_time:{
                    required:true,
                    date:true,
                },
            },
            onkeyup:false,
            focusCleanup:true,
            success:"valid",
            submitHandler:function(form){
                $(form).ajaxSubmit({
                    url:'{{action('Admin\AdvertController@EntitySubmit')}}',
                    type:'POST',
                    dataType:'JSON',
                    success:function(res)
                    {
                        if(res.code == 0)
                        {
                            layer.msg(res.messages,{icon:1,time:1000});
                            window.location.reload();
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