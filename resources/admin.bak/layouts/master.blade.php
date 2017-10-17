<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta name="keywords" content="{{$shop_config['shop_keywords']}}">
    <meta name="description" content="{{$shop_config['shop_description']}}">
    <link rel="shortcut icon" href="{{URL::asset('/favicon.png')}}" >
    <title>@yield('title',\App\Models\CommonModel::languageFormat($shop_config['shop_name'],$shop_config['shop_en_name']).'-'.__('admin.backSystem'))</title>
    <!-- Styles And JavaScript Library-->

    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/html5shiv.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/respond.min.js')}}"></script>
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="{{URL::asset('adminStatic/static/h-ui/css/H-ui.min.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{URL::asset('adminStatic/static/h-ui.admin/css/H-ui.admin.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{URL::asset('adminStatic/lib/Hui-iconfont/1.0.8/iconfont.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{URL::asset('adminStatic/static/h-ui.admin/skin/default/skin.css')}}" id="skin" />
    <link rel="stylesheet" type="text/css" href="{{URL::asset('adminStatic/css/style.css')}}" />

    <!--[if IE 6]>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/DD_belatedPNG_0.0.8a-min.js')}}" ></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->

    @section('MyCss')
    @show
</head>
<body>
        @yield('content')
</body>

<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery/1.9.1/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('adminStatic/lib/layer/2.4/layer.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('adminStatic/static/h-ui/js/H-ui.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('adminStatic/static/h-ui.admin/js/H-ui.admin.js')}}"></script>
<script type="text/javascript">
    /**后台 js 全局变量与函数*/
    window.NetStatus = true;/**全局网络状态,用于异步请求控制*/

    /**
     * 为方便页面loading调用,二次封装网站loading展示函数,返回layer的index
     * layer_index = layer.loading();
     * layer.close(layer_index);
     */
    layer.loading = function()
    {
        return layer.msg('{{__('common.loading')}}', {
            icon: 16
        });
    };

    /** _UploadImagePreviewOne
     *  预览图函数,用于展示图片或其他数据,数据在服务器上临时存放.
     *  file_obj 表单input file对象 仅限单一对象,不可传入多个file
     *  success_function(param) 成功的回调函数 param返回的图片全路径
    */
    function _UploadImagePreviewOne (file_obj,success_function)
    {
        /*判断传来的对象是否是file对象*/
        if(arguments[0].length == 0 || $(file_obj) == null || $(file_obj) == undefined || $(file_obj)[0].files[0] == undefined) return false;
        /*拼成表单对象*/
        var formData = new FormData();
        formData.append("image",$(file_obj)[0].files[0]);
        formData.append("_token","{{csrf_token()}}");
        /*请求服务器,返回图片链接*/
        $.ajax({
            url:"{{action('Admin\ToolsController@ImagePreview')}}",//请求的url地址
            dataType:"JSON",   //返回格式为json
            data:formData,    //参数值
            type:"POST",   //请求方式
            processData : false,// 告诉jQuery不要去处理发送的数据
            contentType : false,// 告诉jQuery不要去设置Content-Type请求头
            success:function(res){
                if(res.code == 0)
                {
                    layer.msg(res.messages);
                    success_function(res.data);
                }
                else
                {
                    layer.msg(res.messages);
                }
            }
        });
    }

    /** _UploadImageSaveOne
     *  上传永久图片函数,用于多图提交表单保存图片,数据在服务器上永久存放.
     *  file_obj 表单input file对象 仅限单一对象,不可传入多个file
     *  success_function(param) 成功的回调函数 param返回 图片全路径 与 可存储至数据库的图片路径
     */
    function _UploadImageSaveOne (file_obj,success_function)
    {
        /*判断传来的对象是否是file对象*/
        if(arguments[0].length == 0 || $(file_obj) == null || $(file_obj) == undefined || $(file_obj)[0].files[0] == undefined) return false;
        /*拼成表单对象*/
        var formData = new FormData();
        formData.append("image",$(file_obj)[0].files[0]);
        formData.append("_token","{{csrf_token()}}");
        /*请求服务器,返回 图片链接 与 可存储至数据库的图片路径*/
        $.ajax({
            url:"{{action('Admin\ToolsController@ImageSave')}}",//请求的url地址
            dataType:"JSON",   //返回格式为json
            data:formData,    //参数值
            type:"POST",   //请求方式
            processData : false,// 告诉jQuery不要去处理发送的数据
            contentType : false,// 告诉jQuery不要去设置Content-Type请求头
            success:function(res){
                if(res.code == 0)
                {
                    layer.msg(res.messages);
                    success_function(res.data);
                }
                else
                {
                    layer.msg(res.messages);
                }
            }
        });
    }

    /** _UploadAttrSaveOne
     *  上传永久属性小图函数,用于多图提交表单保存图片,数据在服务器上永久存放.
     *  file_obj 表单input file对象 仅限单一对象,不可传入多个file
     *  success_function(param) 成功的回调函数 param返回 图片全路径 与 可存储至数据库的图片路径
     */
    function _UploadAttrSaveOne (file_obj,success_function)
    {
        /*判断传来的对象是否是file对象*/
        if(arguments[0].length == 0 || $(file_obj) == null || $(file_obj) == undefined || $(file_obj)[0].files[0] == undefined) return false;
        /*拼成表单对象*/
        var formData = new FormData();
        formData.append("image",$(file_obj)[0].files[0]);
        formData.append("_token","{{csrf_token()}}");
        /*请求服务器,返回 图片链接 与 可存储至数据库的图片路径*/
        $.ajax({
            url:"{{action('Admin\ToolsController@ImageAttrSave')}}",//请求的url地址
            dataType:"JSON",   //返回格式为json
            data:formData,    //参数值
            type:"POST",   //请求方式
            processData : false,// 告诉jQuery不要去处理发送的数据
            contentType : false,// 告诉jQuery不要去设置Content-Type请求头
            success:function(res){
                if(res.code == 0)
                {
                    layer.msg(res.messages);
                    success_function(res.data);
                }
                else
                {
                    layer.msg(res.messages);
                }
            }
        });
    }
    /**
     * H-ui文件上传控件优化,解决css Bug
     */
    function __wk_img_preview()
    {
        $('.wk_img_preview').hover(
                function(){
                    var zIndex = $(this).css('z-index');
                    zIndex++;
                    $(this).parent().find('.input-file').css('font-size','1em');
                    $(this).parent().css('overflow','visible');
                    $(this).css('z-index',zIndex);
                },
                function(){
                    var zIndex = $(this).css('z-index');
                    zIndex--;
                    $(this).parent().find('.input-file').css('font-size','30em');
                    $(this).parent().css('overflow','hidden');
                    $(this).css('z-index',zIndex);
                }
        );
    }

    /**需要加载完成后的全局处理*/
    $(document).ready(function()
    {
        __wk_img_preview();
    });

</script>
@section('MyJs')
@show
</html>
