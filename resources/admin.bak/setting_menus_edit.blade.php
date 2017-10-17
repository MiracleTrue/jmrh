@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}


@section('content')
    <div class="page-container">
        <form action="#" method="post" class="form form-horizontal" id="form-category-add">
            {{csrf_field()}}
            <input type="hidden" name="menu_id" value="{{$menu_info['menu_id'] or 0}}">
            <div id="tab-category" class="HuiTab">
                <div class="tabBar cl">
                    <span>{{__('admin.basicSetting')}}</span>
                </div>
                <div class="tabCon">
                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-3">ID：</label>
                        <div class="formControls col-xs-8 col-sm-9">{{$menu_info['menu_id'] or __('admin.menu.add')}}</div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-3">
                            <span class="c-red">*</span>
                            {{__('admin.parentCategory')}}：</label>
                        <div class="formControls col-xs-8 col-sm-9">
						<span class="select-box">
						<select class="select" id="sel_Sub" name="parent_id">
                            <option value="0">{{__('admin.topCategory')}}</option>
                            @foreach($menu_parent as $value)
                                <option @if($menu_info['parent_id'] == $value['menu_id'])  selected="selected" @endif value="{{$value['menu_id']}}">├ {{\App\Models\CommonModel::languageFormat($value['menu_name'],$value['menu_en_name'])}}</option>
                            @endforeach
                        </select>
						</span>
                        </div>
                        <div class="col-3">
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-3">
                            <span class="c-red">*</span>
                            {{__('admin.menu.name')}}：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{$menu_info['menu_name'] or ''}}" placeholder="例如：栏目管理" id="" name="menu_name">
                        </div>
                        <div class="col-3">
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-3">
                            <span class="c-red">*</span>
                            {{__('admin.menu.enName')}}：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{$menu_info['menu_en_name'] or ''}}" placeholder="例如：栏目管理" id="" name="menu_en_name">
                        </div>
                        <div class="col-3">
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-3">
                            <span class="c-red"></span>
                            {{__('admin.menu.icon')}}：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text menu_icon_input" value="" placeholder="例如：请访问http://www.h-ui.net/Hui-3.7-Hui-iconfont.shtml 查看图标代码填入" id="" name="menu_icon">
                            <i class="Hui-iconfont" style="font-size: 24px;margin-right: 20px">{{$menu_info['menu_icon'] or ''}}</i><a target="_blank" href="http://www.h-ui.net/Hui-3.7-Hui-iconfont.shtml">查看</a>
                        </div>
                        <div class="col-3">
                        </div>
                    </div>
                    <div class="row cl sub_show" @if($menu_info['parent_id'] == 0) style="display: none;"  @endif >
                        <label class="form-label col-xs-4 col-sm-3">
                            <span class="c-red">*</span>
                            {{__('admin.menu.url')}}：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{$menu_info['menu_url'] or ''}}" placeholder="例如：admin/setting/menus/add" id="" name="menu_url">
                        </div>
                        <div class="col-3">
                        </div>
                    </div>
                    <div class="row cl sub_show" @if($menu_info['parent_id'] == 0) style="display: none;"  @endif>
                        <label class="form-label col-xs-4 col-sm-3">
                            <span class="c-red">*</span>
                            {{__('admin.menu.controller')}}：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{$menu_info['menu_controller']}}" placeholder="例如：Admin\MenuController@MenusAdd" id="" name="menu_controller">
                        </div>
                        <div class="col-3">
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-3">
                            <span class="c-red"></span>
                            {{__('common.sort')}}：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{$menu_info['menu_sort'] or '100'}}" placeholder="" id="" name="menu_sort">
                        </div>
                        <div class="col-3">
                        </div>
                    </div>

                </div>
            </div>
            <div class="row cl">
                <div class="col-9 col-offset-3">
                    <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;{{__('common.submit')}}&nbsp;&nbsp;">
                </div>
            </div>
        </form>
    </div>
@endsection

@section('MyJs')
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/messages_'.session('AdminLanguage').'.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            /*获取栏目icon 填入input*/
            @if($menu_info['menu_id'])
             $.ajax({
                url:"{{action('Admin\MenuController@MenusGetOne')}}",    //请求的url地址
                dataType:"json",   //返回格式为json
                data:{
                    "_token":"{{csrf_token()}}",
                    "menu_id":"{{$menu_info['menu_id']}}"
                },
                type:"POST",   //请求方式
                success:function(response){
                    $('.menu_icon_input').val(response.data.menu_icon);
                }
             });
            @endif

            $('#sel_Sub').change(function()
            {
                if( $('#sel_Sub option:selected').val() == 0)
                {
                    $('.sub_show').hide();
                }
                else
                {
                    $('.sub_show').show();
                }
            });


            $('.skin-minimal input').iCheck({
                checkboxClass: 'icheckbox-blue',
                radioClass: 'iradio-blue',
                increaseArea: '20%'
            });

            $.Huitab("#tab-category .tabBar span","#tab-category .tabCon","current","click","0");

            $("#form-category-add").validate({
                rules:{
                    menu_name:"required",
                    menu_en_name:"required",
                },
                onkeyup:false,
                focusCleanup:true,
                success:"valid",
                submitHandler:function(form){
                    var index = parent.layer.getFrameIndex(window.name);
                    $(form).ajaxSubmit({
                        url: '{{$form_url}}',
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSubmit:function(){
                            if(!NetStatus) return false;
                            NetStatus = false;
                        },
                        success:function(res){
                            if(res.code == 0)
                            {
                                layer.msg(res.messages,{icon:1,time:1000},function()
                                {
                                    NetStatus = true;
                                    parent.location.replace(parent.location.href);
                                    parent.layer.close(index);
                                });
                            }
                            else
                            {
                                NetStatus = true;
                                layer.msg(res.messages,{icon:2,time:1000});
                            }
                        }
                    });
                    //parent.$('.btn-refresh').click();
                    //parent.layer.close(index);
                }
            });
        });
    </script>
@endsection

