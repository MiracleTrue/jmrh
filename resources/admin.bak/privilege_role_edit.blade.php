@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}


@section('content')
    <article class="page-container">
        <form action="" method="post" class="form form-horizontal" id="form-admin-role-add">
            {{csrf_field()}}
            <input type="hidden" name="role_id" value="{{$role_info['role_id'] or 0}}">
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">ID：</label>
                <div class="formControls col-xs-8 col-sm-9">{{$role_info['role_id'] or __('admin.privilege.roleAdd')}}</div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>{{__('admin.privilege.roleName')}}：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{$role_info['role_name'] or ''}}" placeholder=""name="role_name">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">{{__('admin.privilege.roleDescription')}}：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{$role_info['role_description'] or ''}}" placeholder=""  name="role_description">
                </div>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>{{__('admin.privilege.roleIsSuper')}}：</label>
                <div class="formControls col-xs-8 col-sm-9 skin-minimal " >
                    <div class="radio-box pr-30" >
                        <input class="lang_slect" value="{{\App\Models\Rbac::NO_SUPER_MANAGEMENT_GROUP}}" id="langue-1" type="radio" name="is_super_management_group" @if($role_info['is_super_management_group'] == \App\Models\Rbac::NO_SUPER_MANAGEMENT_GROUP) checked @endif>
                        <label for="langue-1" >{{__('common.no')}}</label>
                    </div>
                    <div class="radio-box">
                        <input class="lang_slect" value="{{\App\Models\Rbac::IS_SUPER_MANAGEMENT_GROUP}}" id="langue-1" type="radio" name="is_super_management_group" @if($role_info['is_super_management_group'] == \App\Models\Rbac::IS_SUPER_MANAGEMENT_GROUP) checked @endif>
                        <label for="langue-2">{{__('common.yes')}}</label>
                    </div>
                </div>
            </div>

            <div class="row cl prv_list" @if($role_info['is_super_management_group'] == \App\Models\Rbac::IS_SUPER_MANAGEMENT_GROUP) style="display: none" @endif >
                <label class="form-label col-xs-4 col-sm-3">{{__('admin.privilege.roleAssignment')}}：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    @foreach($privilege_list as $group)
                        <dl class="permission-list">
                            <dt>
                                <label><input class="mr-5" type="checkbox" value="" name=""  >{{$group['name']}}</label>
                            </dt>
                            <dd>
                                @foreach($group['action_group'] as $action_group)
                                    <dl class="cl permission-list2">
                                        <dt style="width: 140px;overflow: hidden">
                                            <label><input class="mr-5" type="checkbox" value="" name="" >{{$action_group['name']}}</label>
                                        </dt>
                                        <dd style="margin-left: 140px">
                                            @foreach($action_group['action'] as $action)
                                                <label><input class="mr-5" type="checkbox" value="{{$action['privilege_url']}}" name="checked_action[]" {{$action['checked'] or ''}} >{{$action['name']}}</label>
                                            @endforeach
                                        </dd>
                                    </dl>
                                @endforeach
                            </dd>
                        </dl>
                    @endforeach
                </div>
            </div>

            <div class="row cl">
                <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                    <button type="submit" class="btn btn-success radius" id="admin-role-save"><i class="icon-ok"></i> {{__('common.submit')}}</button>
                </div>
            </div>
        </form>
    </article>
@endsection

@section('MyJs')
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/messages_'.session('AdminLanguage').'.js')}}"></script>
    <script type="text/javascript">
        $(function(){

            $('.skin-minimal input').iCheck({
                checkboxClass: 'icheckbox-blue',
                radioClass: 'iradio-blue',
                increaseArea: '20%'
            });

            $('.lang_slect').on('ifChecked', function(event){
                if($(this).val() == '{{\App\Models\Rbac::IS_SUPER_MANAGEMENT_GROUP}}')
                {
                    $('.prv_list').hide();
                }
                else
                {
                    $('.prv_list').show();
                }
            });

            $(".permission-list dt input:checkbox").click(function(){
                $(this).closest("dl").find("dd input:checkbox").prop("checked",$(this).prop("checked"));
            });
            $(".permission-list2 dd input:checkbox").click(function(){
                var l =$(this).parent().parent().find("input:checked").length;
                var l2=$(this).parents(".permission-list").find(".permission-list2 dd").find("input:checked").length;
                if($(this).prop("checked")){
                    $(this).closest("dl").find("dt input:checkbox").prop("checked",true);
                    $(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",true);
                }
                else{
                    if(l==0){
                        $(this).closest("dl").find("dt input:checkbox").prop("checked",false);
                    }
                    if(l2==0){
                        $(this).parents(".permission-list").find("dt").first().find("input:checkbox").prop("checked",false);
                    }
                }
            });

            $("#form-admin-role-add").validate({
                rules:{
                    role_name:"required",
                    is_super_management_group : "required"
                },
                onkeyup:false,
                focusCleanup:true,
                success:"valid",
                submitHandler:function(form){
                    var index = parent.layer.getFrameIndex(window.name);
                    $(form).ajaxSubmit({
                        url: '{{action('Admin\PrivilegeController@RoleEditSubmit')}}',
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

