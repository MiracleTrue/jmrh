@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}

@section('MyCss')
    <link rel="stylesheet" href="{{URL::asset('adminStatic/lib/jquery.zTree/css/metroStyle/metroStyle.css')}}">
@endsection

@section('content')
    <div class="page-container">
        <div class="pd-5 mb-10 bg-1 bk-gray prompt">
            <div class="pl-30"><i class="Hui-iconfont c-primary">&#xe64b;</i><span class="pl-5 f-12 c-primary">{{__('admin.operationPrompt')}}</span></div>
            <div class="pl-30 pr-30 cl">
                <div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">选择分类中的 (数量) 代表分类下的商品属性数量</span></div>
                <div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">预设选择格式:输入框内填写单条或多条属性,按","逗号分隔如:(黑,白,红)</span></div>
                <div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">必须填写属性名称及属性录入方式 自定义:商品录入时手工输入 预设选择:商品录入时在列表选择</span></div>
            </div>
        </div>
        <form action="" method="post" class="form form-horizontal" id="form-category-add">
            {{csrf_field()}}
            <input type="hidden" name="attr_id" value="{{$attr_info['attr_id'] or 0}}">
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">ID：</label>
                <div class="formControls col-xs-6 col-sm-6">{{$attr_info['attr_id'] or '添加商品属性'}}</div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>商品分类：</label>
                <div class="formControls col-xs-5 col-sm-5">
                    <div>
                        <input class="input-text" id="parent_text"   name="category_id" type="text" readonly value="{{\App\Models\CommonModel::languageFormat($attr_info['goods_category']['category_name'] , $attr_info['goods_category']['category_en_name'])}}"/>
                        <input class="input-text" id="parent_hidden" name="category_id" type="hidden" value="{{$attr_info['category_id']}}"/>
                    </div>
                    <div id="goods_category_tree" class="ztree"></div>
                </div>
                <div class="col-xs-1 col-sm-1">
                    <button type="button" id="parent_btn" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe642;</i> {{__('admin.browse')}}</button>
                </div>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">
                    <span class="c-red">*</span>属性名称：</label>
                <div class="formControls col-xs-6 col-sm-6">
                    <input type="text" class="input-text" value="{{$attr_info['attr_name']}}" placeholder=""name="attr_name">
                </div>
            </div>

            {{--商品展示属性--}}
            <div class="row cl attr_container">
                <label class="form-label col-xs-2 col-sm-2"><b class="c-success">商品展示属性</b></label>
                <div class="formControls col-xs-9 col-sm-9 ">
                    <input type="hidden" name="show_attr" value="">
                    @if(is_array($attr_info['show_attr']))
                    @foreach($attr_info['show_attr'] as $item)
                    <div class="container show_attr">
                        <div class="col-sm-1"><i class="Hui-iconfont" onclick="container_remove(this)">&#xe6a1;</i></div>
                        <label class="col-sm-3">
                            <span>名称:</span>
                            <input class="input-text show_name" type="text" value="{{$item['name']}}"></label>
                        <label class="col-sm-8">
                            <span class="mr-10">录入方式:</span>
                            <select class="show_type" onchange="change_show_input(this)">
                                <option value="">请选择类型</option>
                                <option value="defined" @if($item['type'] == \App\Models\Attributes::DEFINED) selected="selected" @endif>自定义</option>
                                <option value="preset" @if($item['type'] == \App\Models\Attributes::PRESET) selected="selected" @endif >预设选择</option>
                            </select>
                            @if($item['type'] == \App\Models\Attributes::PRESET)
                                <input class="input-text show_preset" type="text" value="{{$item['data']}}">
                            @elseif($item['type'] == \App\Models\Attributes::DEFINED)
                                <input class="input-text show_preset" type="text" disabled="disabled">
                            @endif
                        </label>
                    </div>
                    @endforeach
                    @endif
                    <div class="container show_attr_add">
                        <div class="col-sm-1"><i class="Hui-iconfont" onclick="show_attr_add()">&#xe61f;</i></div>
                    </div>
                </div>
            </div>

            {{--商品购买属性--}}
            <div class="row cl attr_container">
                <label class="form-label col-xs-2 col-sm-2"><b class="c-warning">商品购买属性</b></label>
                <div class="formControls col-xs-9 col-sm-9 ">
                    <input type="hidden" name="select_attr" value="">
                    @if(is_array($attr_info['select_attr']))
                    @foreach($attr_info['select_attr'] as $item)
                    <div class="container select_attr">
                        <div class="col-sm-1"><i class="Hui-iconfont" onclick="container_remove(this)">&#xe6a1;</i></div>
                        <label class="col-sm-3">
                            <span>名称:</span>
                            <input class="input-text select_name" type="text" value="{{$item['name']}}"></label>
                        <label class="col-sm-8">
                            <span class="mr-10">录入方式:</span>
                            <select class="select_type" onchange="change_select_input(this)">
                                <option value="">请选择类型</option>
                                <option value="defined" @if($item['type'] == \App\Models\Attributes::DEFINED) selected="selected" @endif>自定义</option>
                                <option value="preset" @if($item['type'] == \App\Models\Attributes::PRESET) selected="selected" @endif >预设选择</option>
                            </select>
                            @if($item['type'] == \App\Models\Attributes::PRESET)
                                <input class="input-text select_preset" type="text" value="{{$item['data']}}">
                            @elseif($item['type'] == \App\Models\Attributes::DEFINED)
                                <input class="input-text select_preset" type="text" disabled="disabled">
                            @endif
                        </label>
                    </div>
                    @endforeach
                    @endif
                    <div class="container select_attr_add">
                        <div class="col-sm-1"><i class="Hui-iconfont" onclick="select_attr_add()">&#xe61f;</i></div>
                    </div>
                </div>
            </div>

            <div class="row cl ">
                <div class="col-9 col-offset-2">
                    <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
                </div>
            </div>
        </form>
    </div>
@endsection

@section('MyJs')
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.json.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/messages_'.session('AdminLanguage').'.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/jquery.zTree/js/jquery.ztree.all.min.js')}}"></script>

    <script type="text/javascript">
        /*商品购买属性的类型转换*/
        function change_select_input(obj)
        {
            if($(obj).val() == 'preset')
            {
                $(obj).siblings('.select_preset').removeAttr("disabled");
            }
            else
            {
                $(obj).siblings('.select_preset').attr('disabled','disabled')
                $(obj).siblings('.select_preset').val('');
            }
        }
        /*商品展示属性的类型转换*/
        function change_show_input(obj)
        {
            if($(obj).val() == 'preset')
            {
                $(obj).siblings('.show_preset').removeAttr("disabled");
            }
            else
            {
                $(obj).siblings('.show_preset').attr('disabled','disabled')
                $(obj).siblings('.show_preset').val('');
            }
        }
        /*容器删除*/
        function container_remove(obj)
        {
            $(obj).parent().parent().remove();
        }
        /*商品购买属性的容器添加*/
        function select_attr_add()
        {
            $(".select_attr_add").before('<div class="container select_attr">' +
                    '<div class="col-sm-1"><i class="Hui-iconfont" onclick="container_remove(this)">&#xe6a1;</i></div>' +
                    '<label class="col-sm-3">' +
                    '<span>名称:</span>' +
                    '<input class="input-text select_name" type="text"></label>' +
                    '<label class="col-sm-8">' +
                    '<span class="mr-10">录入方式:</span>' +
                    '<select class="select_type" onchange="change_select_input(this)">' +
                    '<option value="">请选择类型</option>' +
                    '<option value="defined">自定义</option>' +
                    '<option value="preset">预设选择</option>' +
                    '</select>' +
                    '<input class="input-text select_preset" type="text" disabled="disabled">' +
                    '</label></div>');
        }
        function show_attr_add()
        {
            $('.show_attr_add').before('<div class="container show_attr">' +
                    '<div class="col-sm-1"><i class="Hui-iconfont" onclick="container_remove(this)">&#xe6a1;</i></div> ' +
                    '<label class="col-sm-3"> ' +
                    '<span>名称:</span> ' +
                    '<input class="input-text show_name" type="text"></label> ' +
                    '<label class="col-sm-8"> ' +
                    '<span class="mr-10">录入方式:</span> ' +
                    '<select class="show_type" onchange="change_show_input(this)"> ' +
                    '<option value="">请选择类型</option> ' +
                    '<option value="defined">自定义</option> ' +
                    '<option value="preset">预设选择</option> ' +
                    '</select> ' +
                    '<input class="input-text show_preset" type="text" disabled="disabled"> ' +
                    '</label></div>');
        }
        var zTreeNodes =[
            { id:0, name:"{{__('admin.topCategory')}}", open:true},
            @foreach($category_tree as $item)
            { id:'{{$item['category_id']}}', pId:'{{$item['parent_id']}}', name:'{{$item['name']}}' + '({{$item['my_count']}})'},
            @endforeach
        ];

        var zTreeSetting = {
            view: {
                selectedMulti: false,
            },
            data: {
                simpleData: {
                    enable:true,
                    idKey: "id",
                    pIdKey: "pId"
                }
            },
            callback: {
                beforeClick: function(treeId, treeNode) {
                    var reg = new RegExp("\\(\\d+\\)$",["g"]);/*正则替换数量显示,页面美观*/
                    var name = treeNode.name.replace(reg,'');
                    $('#parent_text').val(name);
                    $('#parent_hidden').val(treeNode.id);
                    $('#goods_category_tree').hide();
                }
            }
        };

        $(document).ready(function(){
            var zTree = $.fn.zTree.init($('#goods_category_tree'), zTreeSetting, zTreeNodes);

            $('#parent_btn').click(function()
            {
                $('#goods_category_tree').toggle();
            });

            $("#form-category-add").validate({
                rules:{
                    category_id:"required",
                    attr_name:"required"
                },
                onkeyup:false,
                focusCleanup:true,
                success:"valid",
                submitHandler:function(form){
                    var index = parent.layer.getFrameIndex(window.name);
                    $(form).ajaxSubmit({
                        url: '{{action('Admin\GoodsController@AttributesEditSubmit')}}',
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSerialize:function(){
                            var attr_select = [];
                            var attr_show = [];
                            /*商品购买属性*/
                            $('.select_attr').each(function(index)
                            {
                                var obj = {};
                                obj.name = $(this).find('.select_name').val();
                                if($(this).find('.select_name').val() == '')
                                {
                                    attr_select = [];
                                    return false;
                                }
                                obj.type = $(this).find('.select_type').val();
                                if(obj.type == '')
                                {
                                    attr_select = [];
                                    return false;
                                }
                                else if(obj.type == 'preset')
                                {
                                    if($(this).find('.select_preset').val() == '')
                                    {
                                        attr_select = [];
                                        return false;
                                    }
                                    else
                                    {
                                        obj.data = $(this).find('.select_preset').val();
                                    }
                                }
                                attr_select.push(obj);
                            });

                            /*商品展示属性*/
                            $('.show_attr').each(function(index)
                            {
                                var obj = {};
                                obj.name = $(this).find('.show_name').val();
                                if($(this).find('.show_name').val() == '')
                                {
                                    attr_show = [];
                                    return false;
                                }
                                obj.type = $(this).find('.show_type').val();
                                if(obj.type == '')
                                {
                                    attr_select = [];
                                    return false;
                                }
                                else if(obj.type == 'preset')
                                {
                                    if($(this).find('.show_preset').val() == '')
                                    {
                                        attr_show = [];
                                        return false;
                                    }
                                    else
                                    {
                                        obj.data = $(this).find('.show_preset').val();
                                    }
                                }
                                attr_show.push(obj);

                            });
                            if($('.show_attr').size() > 0)
                            {
                                if($.isEmptyObject(attr_show))
                                {
                                    layer.msg('属性录入不完整');
                                    return false;
                                }
                                else
                                {
                                    $("input[name='show_attr']").val($.toJSON(attr_show));
                                }
                            }
                            else
                            {
                                $("input[name='show_attr']").val('');
                            }
                            if($('.select_attr').size() > 0)
                            {
                                if($.isEmptyObject(attr_select))
                                {
                                    layer.msg('属性录入不完整');
                                    return false;
                                }
                                else
                                {
                                    $("input[name='select_attr']").val($.toJSON(attr_select));
                                }
                            }
                            else
                            {
                                $("input[name='select_attr']").val('');
                            }

                        },
                        beforeSubmit:function(){

//                            if(!NetStatus) return false;
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
                }
            });
        });
    </script>
@endsection

