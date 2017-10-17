@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}


@section('content')
    <article class="page-container">
        <form class="form form-horizontal" id="form-admin-add">
            {{csrf_field()}}
            <input type="hidden" name="admin_id" value="{{$manager_info['admin_id'] or 0}}">
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">ID：</label>
                <div class="formControls col-xs-8 col-sm-9">{{$manager_info['admin_id'] or __('admin.privilege.managerAdd')}}</div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>{{__('admin.privilege.managerName')}}：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{$manager_info['admin_name']}}" placeholder="" id="adminName" name="admin_name">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>{{__('common.password')}}：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="password" class="input-text" autocomplete="off" value="" placeholder="" id="password" name="password">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>{{__('common.confirmPassword')}}：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="password" class="input-text" autocomplete="off"  placeholder="" id="password2" name="password_confirmation">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>{{__('common.mobilePhone')}}：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{$manager_info['phone']}}" placeholder="" id="phone" name="phone">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>{{__('common.email')}}：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="{{$manager_info['email']}}" placeholder="" name="email" id="email">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>{{__('admin.privilege.roleName')}}：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <span class="select-box" style="width:150px;">
                        <select class="select Hui-iconfont" name="role_id" size="1">
                            <option class="Hui-iconfont" value="">{{__('common.pleaseSelect')}}</option>
                            @foreach($role_list as $item)
                            @if($manager_info['role_id'] === $item['role_id'])
                                    <option title="{{$item['role_description']}}" class="Hui-iconfont" value="{{$item['role_id']}}" selected="selected" >{{$item['role_name']}} @if($item['is_super_management_group'] == \App\Models\Rbac::IS_SUPER_MANAGEMENT_GROUP) &#xe62d; @endif</option>
                                @else
                                    <option title="{{$item['role_description']}}" class="Hui-iconfont" value="{{$item['role_id']}}">{{$item['role_name']}} @if($item['is_super_management_group'] == \App\Models\Rbac::IS_SUPER_MANAGEMENT_GROUP) &#xe62d; @endif</option>
                                @endif
                            @endforeach
                        </select>
                    </span>
                </div>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>{{__('admin.isEnable')}}：</label>
                <div class="formControls col-xs-8 col-sm-9 skin-minimal " >
                    <div class="radio-box pr-30" >
                        <input class="lang_slect" value="{{\App\Models\Rbac::MANAGER_IS_ENABLE}}" id="langue-1" type="radio" name="is_enable" @if($manager_info['is_enable'] === \App\Models\Rbac::MANAGER_IS_ENABLE) checked @else checked @endif>
                        <label for="langue-1" >{{__('admin.enable')}}</label>
                    </div>
                    <div class="radio-box">
                        <input class="lang_slect" value="{{\App\Models\Rbac::MANAGER_NO_ENABLE}}" id="langue-1" type="radio" name="is_enable" @if($manager_info['is_enable'] === \App\Models\Rbac::MANAGER_NO_ENABLE) checked  @else @endif>
                        <label for="langue-2">{{__('admin.disable')}}</label>
                    </div>
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
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/messages_'.session('AdminLanguage').'.js')}}"></script>
    <script type="text/javascript">
        $(function() {
            $('.skin-minimal input').iCheck({
                checkboxClass: 'icheckbox-blue',
                radioClass: 'iradio-blue',
                increaseArea: '20%'
            });

            /**
             * 表单验证与异步提交
             */
            $("#form-admin-add").validate({
                rules: {
                    admin_id : {
                        required: true,
                    },
                    admin_name: {
                        required: true,
                        minlength: 4,
                        maxlength: 16
                    },
                    password: {
                        required: true,
                        minlength: 6,
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: "#password"
                    },
                    phone: {
                        required: true,
                        isPhone: true,
                    },
                    email: {
                        required: true,
                        email: true,
                    },
                    role_id: {
                        required: true,
                    },
                },
                onkeyup: false,
                focusCleanup: true,
                success: "valid",
                submitHandler: function (form) {
                    var index = parent.layer.getFrameIndex(window.name);
                    $(form).ajaxSubmit({
                        url: '{{action('Admin\PrivilegeController@ManagerEditSubmit')}}',
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSubmit: function () {
                            if (!NetStatus) return false;
                            NetStatus = false;
                        },
                        success: function (res) {
                            if (res.code == 0) {
                                layer.msg(res.messages, {icon: 1, time: 1000}, function () {
                                    NetStatus = true;
                                    parent.location.replace(parent.location.href);
                                    parent.layer.close(index);
                                });
                            }
                            else {
                                NetStatus = true;
                                layer.msg(res.messages, {icon: 2, time: 1000});
                            }
                        }
                    });
                }
            });

        });
    </script>
@endsection

