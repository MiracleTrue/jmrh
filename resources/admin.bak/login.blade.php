@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}

@section('MyCss')
  <link rel="stylesheet" type="text/css" href="{{URL::asset('adminStatic/static/h-ui.admin/css/H-ui.login.css')}}" />
@endsection
@section('content')
<div class="header">
  <div class="dropDown dropDown_hover login_lang">
    <a href="#" class="dropDown_A c-white">{{__('common.language')}}({{__('common.lang')}})<i class="Hui-iconfont">&nbsp;&#xe6d5;</i></a>
    <ul class="dropDown-menu menu radius box-shadow">
      <li><a href="{{action('Admin\IndexController@SetLanguage','zh')}}">中文</a></li>
      <li><a href="{{action('Admin\IndexController@SetLanguage','en')}}">English</a></li>
    </ul>
  </div>
</div>
<div class="loginWraper">
  <div id="loginform" class="loginBox">
    <form class="form form-horizontal" action="{{action('Admin\IndexController@LoginSubmit')}}" method="post">
      {{csrf_field()}}
      <div class="row cl">
        <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60d;</i></label>
        <div class="formControls col-xs-7">
          <input id="" name="admin_name" type="text" placeholder="{{__('admin.privilege.managerName')}}" class="input-text size-L">
        </div>
      </div>
      <div class="row cl">
        <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60e;</i></label>
        <div class="formControls col-xs-7">
          <input id="" name="password" type="password" placeholder="{{__('common.password')}}" class="input-text size-L">
        </div>
      </div>
      {{--<div class="row cl">--}}
        {{--<div class="formControls col-xs-8 col-xs-offset-3">--}}
          {{--<input class="input-text size-L" type="text" placeholder="验证码" onblur="if(this.value==''){this.value='验证码:'}" onclick="if(this.value=='验证码:'){this.value='';}" value="验证码:" style="width:150px;">--}}
          {{--<img src=""> <a id="kanbuq" href="javascript:;">看不清，换一张</a> </div>--}}
      {{--</div>--}}
      {{--<div class="row cl">--}}
        {{--<div class="formControls col-xs-7 col-xs-offset-3">--}}
          {{--<label for="online">--}}
            {{--<input type="checkbox" name="online" id="online" value="">--}}
            {{--使我保持登录状态--}}
          {{--</label>--}}
        {{--</div>--}}
      {{--</div>--}}
      <div class="row cl">
        <div class="formControls col-xs-8 col-xs-offset-3">
          <input name="" type="submit" class="btn btn-success radius size-L mr-50" value="&nbsp;{{__('common.submit')}}&nbsp;">
          <input name="" type="reset" class="btn btn-default radius size-L " value="&nbsp;{{__('common.cancel')}}&nbsp;">
        </div>
      </div>
    </form>
  </div>
</div>
@include('admin.include.inc_footer')
@endsection

@section('MyJs')
  <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
  <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
  <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/messages_'.session('AdminLanguage').'.js')}}"></script>
  <script type="text/javascript">
    $(document).ready(function()
    {
      /**
       * 登录表单验证与异步提交
       */
      $("#loginform form").validate({
        rules: {
          admin_name: {
            required: true,
            minlength: 4,
            maxlength: 16
          },
          password: {
            required: true,
            minlength: 6
          }
        },
        onkeyup: false,
        focusCleanup: true,
        success: "valid",
        submitHandler: function (form) {
          var index = parent.layer.getFrameIndex(window.name);
          $(form).ajaxSubmit({
            url: '{{action('Admin\IndexController@LoginSubmit')}}',
            type: 'POST',
            dataType: 'JSON',
            beforeSubmit: function () {
              if (!NetStatus) return false;
              NetStatus = false;
            },
            success: function (res) {
              if (res.code == 0) {
                NetStatus = true;
                window.location.replace("{{action('Admin\IndexController@Index')}}");
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