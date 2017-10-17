@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}
@section('MyCss')
    <link rel="stylesheet" href="{{URL::asset('adminStatic/lib/jquery.zTree/css/metroStyle/metroStyle.css')}}">
@endsection
@section('content')
    <div class="page-container">
        <form action="" class="form form-horizontal" id="form-article-add" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" name="goods_id" value="{{$goods_info['goods_id'] or 0}}">
            <div id="tab-system" class="HuiTab">
                <div class="tabBar cl">
                    <span>商品基本信息</span>
                    <span>相册与详情</span>
                    <span>价格与运费</span>
                    <span>商品发布</span>
                </div>
                {{--商品基本信息--}}
                <div class="tabCon">
                    <div class="pd-5 mb-10 bg-1 bk-gray prompt">
                        <div class="pl-30"><i class="Hui-iconfont c-primary">&#xe64b;</i><span class="pl-5 f-12 c-primary">{{__('admin.operationPrompt')}}</span></div>
                        <div class="pl-30 pr-30 cl">
                            <div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">请先选择商品分类，再继续操作。</span></div>
                            <div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">商品分类中的 (数量) 代表分类下的商品数量</span></div>
                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2">ID：</label>
                        <div class="formControls col-xs-8 col-sm-9">{{$goods_info['goods_id'] or '添加商品'}}</div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>商品分类：</label>
                        <div class="formControls col-xs-7 col-sm-8">
                            <div>
                                <input class="input-text" id="parent_text" name="category_text" type="text" readonly value="{{\App\Models\CommonModel::languageFormat($goods_info['goods_category']['category_name'] , $goods_info['goods_category']['category_en_name'])}}"/>
                                <input id="parent_hidden" name="category_id" type="hidden" value="{{$goods_info['category_id'] or ''}}"/>
                            </div>
                            <div id="goods_category_tree" class="ztree"></div>
                        </div>
                        <div class="col-xs-1 col-sm-1">
                            <button type="button" id="parent_btn" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe642;</i> {{__('admin.browse')}}</button>
                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>商品名称：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{$goods_info['goods_name']}}" placeholder="" name="goods_name">
                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>商品品牌：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <span class="select-box" style="width:100%;">
                                <select class="select" id="brand_select" name="brand_id">
                                    <option value="">{{__('common.pleaseSelect')}}</option>
                                    @if(!empty($goods_info['goods_brand']))
                                        <option value="{{$goods_info['goods_brand']['brand_id']}}" selected="selected">{{$goods_info['goods_brand']['brand_name']}}</option>
                                    @endif
                                </select>
                            </span>
                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>缩略图：</label>
                        <span class="btn-upload formControls col-xs-8 col-sm-9">
                            <a href="javascript:void(0);" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe642;</i> {{__('admin.browse')}}&nbsp;</a>
                            <input type="file" name="goods_thumb" id="goods_thumb" class="input-file">
                            <span class="wk_img_preview">
                                <i class="Hui-iconfont">&#xe646;</i>
                                <img src="{{$goods_info['goods_thumb']}}"/>
                            </span>
                        </span>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2">商品描述：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <textarea name="goods_description" class="textarea" placeholder="说点什么...最少输入10个字符" onKeyUp="$.Huitextarealength(this,100)">{{$goods_info['goods_description']}}</textarea>
                            <p class="textarea-numberbar"><em class="textarea-length">0</em>/100</p>
                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>排序值：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="number" class="input-text" value="{{$goods_info['goods_sort'] or '1000'}}" name="goods_sort">
                        </div>
                    </div>

                </div>

                {{--相册与详情--}}
                <div class="tabCon">
                    <div class="pd-5 mb-10 bg-1 bk-gray prompt">
                        <div class="pl-30"><i class="Hui-iconfont c-primary">&#xe64b;</i><span class="pl-5 f-12 c-primary">{{__('admin.operationPrompt')}}</span></div>
                        <div class="pl-30 pr-30 cl">
                            <div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">商品相册内的上传图片不超过500KB.</span></div>
                        </div>
                    </div>

                    <div class="row cl attr_container">
                        <label class="form-label col-xs-4 col-sm-2">商品展示属性：</label>

                        <div class="formControls col-xs-8 col-sm-9 cl">
                            <input type="hidden" name="show_attr" value="">
                            <span class="select-box mb-10" style="width:100%;">
                                <select class="select" id="show_select" onchange="change_show_select(this)">
                                    <option value="0">自定义</option>
                                </select>
                            </span>
                            @if(is_array($goods_info['show_attr']))
                                @foreach($goods_info['show_attr'] as $item)
                                    <div class="show_attr product_show">
                                        <div class="col-sm-1"><i class="Hui-iconfont" onclick="container_remove(this)">&#xe6a1;</i></div>
                                        <label class="col-sm-4">
                                            <span>名称:</span>
                                            <input class="input-text show_name" type="text" value="{{$item['name']}}"></label>
                                        <label class="col-sm-7">
                                            <span class="mr-10">属性值:</span>
                                            <input class="input-text show_data" type="text" value="{{$item['data']}}">
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                            <div class="show_attr_add" >
                                <div class="col-sm-12"><i class="Hui-iconfont" onclick="show_attr_add()">&#xe61f;</i></div>
                            </div>
                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2">商品相册：</label>
                        <div class="formControls col-xs-8 col-sm-9 cl" id="goods_edit_photo_box">
                            @if(is_array($goods_info['goods_photo']))
                                @foreach($goods_info['goods_photo'] as $item)
                                <div class="goods_edit_photo">
                                    <span class="btn-upload active">
                                        <img src='{{\App\Models\MyFile::makeUrl($item)}}'>
                                        <input type="hidden" name="goods_photo[]" value="{{$item}}">
                                        <input type="file" class="input-file" onclick="return false;">
                                        <span class="hover_delete" onclick="goods_photo_del(this)"><i class="Hui-iconfont">&#xe6e2;</i></span>
                                    </span>
                                </div>
                                @endforeach
                            @endif
                            <div class="goods_edit_photo">
                                <span class="btn-upload">
                                    <img src='{{asset("adminStatic/images/goods_photo_add.png")}}'>
                                    <input type="hidden" name="goods_photo[]" value="">
                                    <input type="file" class="input-file" onchange="goods_photo_upload(this)">
                                    <span class="hover_delete" onclick="goods_photo_del(this)"><i class="Hui-iconfont">&#xe6e2;</i></span>
                                </span>
                            </div>


                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2">商品详情：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <script id="editor" name="goods_content" type="text/plain" style="width:100%;height:400px;">{!! $goods_info['goods_content'] !!}</script>
                        </div>
                    </div>

                </div>

                {{--价格与运费--}}
                <div class="tabCon">
                    <div class="pd-5 mb-10 bg-1 bk-gray prompt">
                        <div class="pl-30"><i class="Hui-iconfont c-primary">&#xe64b;</i><span class="pl-5 f-12 c-primary">{{__('admin.operationPrompt')}}</span></div>
                        <div class="pl-30 pr-30 cl">
                            <div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">属性小图上传不超过100KB</span></div>
                            <div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">属性价格不可低于商品价</span></div>
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>商品价格：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{$goods_info['goods_price']}}"  name="goods_price" onchange="this.value=toDecimal2(this.value)">
                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>商品库存：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="number" class="input-text" value="{{$goods_info['goods_number']}}" placeholder="" name="goods_number">
                        </div>
                    </div>

                    <div class="row cl attr_container">
                        <label class="form-label col-xs-4 col-sm-2">商品购买属性：</label>

                        <div class="formControls col-xs-8 col-sm-9 cl" id="product_select_box_parent">
                            <input type="hidden" name="select_attr" value="">
                            <span class="select-box mb-10" style="width:100%;">
                                <select class="select" id="select_select" onchange="change_select_select(this)">
                                    <option value="0">自定义</option>
                                </select>
                            </span>
                            <button class="btn btn-secondary radius mb-20" type="button" style="width: 100%" onclick="product_select_box()">自定义添加</button>
                            @if(is_array($goods_info['select_attr']))
                            @foreach($goods_info['select_attr'] as $item)
                                @if($item['type'] == \App\Models\Attributes::SELECT_TYPE_TEXT)
                                    <div class="product_select_box cl" data-type="text" data-name="{{$item['name']}}">
                                        <div class="product_select_name text-c mb-10">
                                            <a class="f-r f-12 lh-30" onclick="product_select_box_remove(this)"><i class="Hui-iconfont">&#xe6e2;</i></a>
                                            <span class="f-18 btn btn-default round">{{$item['name']}}</span>
                                        </div>
                                        @foreach($item['data'] as $row)
                                        <div class="select_attr product_select">
                                            <div class="col-sm-1"><i class="Hui-iconfont" onclick="container_remove(this)">&#xe6a1;</i></div>
                                            <label class="col-sm-3">
                                                <span>名称:</span>
                                                <input class="input-text select_name" type="text" value="{{$row['text']}}"></label>
                                            <label class="col-sm-2">
                                                <span class="mr-10">价格:</span>
                                                <input class="input-text select_price" type="text" value="{{$row['price']}}" onchange="this.value=change_price(this.value)">
                                            </label>
                                            <label class="col-sm-2">
                                                <div>库存:</div>
                                                <input class="input-text select_number" type="number" value="{{$row['number']}}" onchange="change_number()">
                                            </label>
                                        </div>
                                        @endforeach
                                        <div class="select_attr_add" >
                                            <div class="col-sm-12"><i class="Hui-iconfont" onclick="select_attr_add_t(this)">&#xe61f;</i></div>
                                        </div>
                                    </div>
                                @elseif($item['type'] == \App\Models\Attributes::SELECT_TYPE_PHOTO)
                                    <div class="product_select_box cl" data-type="photo" data-name="{{$item['name']}}">
                                        <div class="product_select_name text-c mb-10">
                                            <a class="f-r f-12 lh-30" onclick="product_select_box_remove(this)"><i class="Hui-iconfont">&#xe6e2;</i></a>
                                            <span class="f-18 btn btn-default round">{{$item['name']}}</span>
                                        </div>
                                        @foreach($item['data'] as $row)
                                        <div class="select_attr product_select">
                                            <div class="col-sm-1"><i class="Hui-iconfont" onclick="container_remove(this)">&#xe6a1;</i></div>
                                            <label class="col-sm-3">
                                                <div>名称:</div>
                                                <input class="input-text select_name" type="text" value="{{$row['text']}}"></label>
                                            <label class="col-sm-2">
                                                <div>价格:</div>
                                                <input class="input-text select_price" type="text" value="{{$row['price']}}" onchange="this.value=change_price(this.value)">
                                            </label>
                                            <label class="col-sm-2">
                                                <div>库存:</div>
                                                <input class="input-text select_number" type="number" value="{{$row['number']}}" onchange="change_number()">
                                            </label>
                                            <label class="col-sm-4"><div>图片:</div>
                                                <span class="btn-upload select_extra">
                                                    <a href="javascript:void(0);" class="btn btn-primary radius"><i class="Hui-iconfont Hui-iconfont-ignore">&#xe642;</i> {{__('admin.browse')}}</a>
                                                    <input type="file" class="input-file attr_photo_file" onchange="change_select_file(this)">
                                                    <span class="wk_img_preview">
                                                        <i class="Hui-iconfont Hui-iconfont-ignore">&#xe646;</i>
                                                        <img data-url="{{$row['extra']}}" src="{{\App\Models\MyFile::makeUrl($row['extra'])}}"/>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endforeach
                                        <div class="select_attr_add" >
                                            <div class="col-sm-12"><i class="Hui-iconfont" onclick="select_attr_add_p(this)">&#xe61f;</i></div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            @endif





                        </div>
                    </div>
                </div>

                {{--商品发布--}}
                <div class="tabCon">
                    <div class="pd-5 mb-10 bg-1 bk-gray prompt">
                        <div class="pl-30"><i class="Hui-iconfont c-primary">&#xe64b;</i><span class="pl-5 f-12 c-primary">{{__('admin.operationPrompt')}}</span></div>
                        <div class="pl-30 pr-30 cl">
                            <div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">请确认商品信息已填写完善!</span></div>
                        </div>
                    </div>
                    <div class="row cl">
                        <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                            <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存并提交审核</button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection

@section('MyJs')
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.json.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/messages_'.session('AdminLanguage').'.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/jquery.zTree/js/jquery.ztree.all.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/ueditor/1.4.3/ueditor.config.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/ueditor/1.4.3/ueditor.all.min.js')}}"> </script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js')}}"></script>
    <script type="text/javascript">
        /*改变商品分类时触发函数*/
        function change_goods_category(category_id)
        {
            /*获取分类下的商品属性*/
            $.ajax({
                url: '{{action('Admin\GoodsController@CategoryGetRelevance')}}',
                dataType:"json",   //返回格式为json
                data:{"type":'attr,brand' , "category_id":category_id , "_token":"{{csrf_token()}}"},
                type:"POST",   //请求方式
                success:function(res){
                    if(res.code == 0)
                    {
                        $('#show_select option.append').remove();
                        $('#brand_select option.append').remove();
                        $('#select_select option.append').remove();
                        if(!$.isEmptyObject(res.data.goods_brand))/*商品品牌*/
                        {
                            $(res.data.goods_brand).each(function()
                            {
                                $('#brand_select').append('<option class="append" value="'+ this.brand_id +'">' + this.brand_name + '</option>');
                            });
                        }
                        if(!$.isEmptyObject(res.data.goods_attributes))/*商品属性*/
                        {

                            $(res.data.goods_attributes).each(function()
                            {
                                $('#show_select').append("<option class='append' value='"+ this.attr_id +"' data-json='"+ this.show_attr + "'>" + this.attr_name + "</option>");
                                $('#select_select').append("<option class='append' value='"+ this.attr_id +"' data-json='"+ this.select_attr + "'>" + this.attr_name + "</option>");
                            });
                        }
                    }
                    else
                    {
                        layer.msg(res.messages);
                    }
                }
            });
        }
        /*分类树*/
        var zTreeNodes =[
            @foreach($category_tree as $item)
            { id:'{{$item['category_id']}}', pId:'{{$item['parent_id']}}', name:'{{$item['name']}}' + '{{$item['count_desc']}}'},
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
                    $('#parent_text').val(treeNode.name);
                    $('#parent_hidden').val(treeNode.id);
                    $('#goods_category_tree').hide();
                    change_goods_category(treeNode.id);
                }

            }
        };
        /*价格*/
        function change_price(price)
        {
            var goods_price = $('.input-text[name="goods_price"]').val() == '' ?  '0.00' : $('.input-text[name="goods_price"]').val();
            var goods_price = parseFloat(goods_price);
            var price       = parseFloat(price);
            if(price < goods_price || goods_price == 0)
            {
                layer.msg('属性价格不可小于商品价格!');
                return toDecimal2(goods_price);
            }
            else
            {
                return toDecimal2(price);
            }
        }
        /*库存*/
        function change_number()
        {
            var arr = $('.input-text.select_number');
            var all_number = 0;
            $(arr).each(function()
            {
                var nb = $(this).val() == '' ? 0 : parseInt($(this).val());
                if($.isNumeric(nb)) all_number += nb;
            });
            $('.input-text[name="goods_number"]').val(all_number);
        }
        /*容器删除*/
        function container_remove(obj)
        {
            $(obj).parent().parent().remove();
        }
        function product_select_box_remove(obj)
        {
            $(obj).parent().parent().remove();
        }
        /*商品购买属性*/
        function select_box_add_t(text)
        {
            $('#product_select_box_parent').append('<div class="product_select_box cl" data-type="text" data-name="'+ text +'">' +
                    '<div class="product_select_name text-c mb-10"><a class="f-r f-12 lh-30" onclick="product_select_box_remove(this)"><i class="Hui-iconfont">&#xe6e2;</i></a><span class="f-18 btn btn-default round">' + text + '</span></div>' +
                    '<div class="select_attr_add" >' +
                    '<div class="col-sm-12"><i class="Hui-iconfont" onclick="select_attr_add_t(this)">&#xe61f;</i></div>' +
                    '</div></div>');
        }
        function select_box_add_p(text)
        {
            $('#product_select_box_parent').append('<div class="product_select_box cl" data-type="photo" data-name="'+ text +'">' +
                    '<div class="product_select_name text-c mb-10"><a class="f-r f-12 lh-30" onclick="product_select_box_remove(this)"><i class="Hui-iconfont">&#xe6e2;</i></a><span class="f-18 btn btn-default round">' + text + '</span></div>' +
                    '<div class="select_attr_add" >' +
                    '<div class="col-sm-12"><i class="Hui-iconfont" onclick="select_attr_add_p(this)">&#xe61f;</i></div>' +
                    '</div></div>');
        }
        function product_select_box()
        {
            layer.confirm('请选择属性类型。', {
                btn: ['文本格式','相册格式']
            },
            function()/*文本格式*/
            {
                layer.prompt({title: '请填写属性名称。', formType:0}, function(val, index){
                    select_box_add_t(val);
                    layer.close(index);
                });
            },
            function()/*相册格式*/
            {
                layer.prompt({title: '请填写属性名称。', formType:0}, function(val, index){
                    select_box_add_p(val);
                    layer.close(index);
                });
            });
        }
        function change_select_file(obj)
        {
            var _this = $(obj);
            _UploadAttrSaveOne(_this , function(data) {
                $(_this).siblings('.wk_img_preview').find('img').attr("src" , data.url);
                $(_this).siblings('.wk_img_preview').find('img').attr("data-url" , data.base);
            });
        }
        function select_attr_add_p(obj,attr_name,attr_price)
        {
            var attr_name = arguments[1] ? arguments[1] : '';
            var attr_price = arguments[2] ? arguments[2] : $('.input-text[name="goods_price"]').val() == '' ?  '0.00' : $('.input-text[name="goods_price"]').val();
            $(obj).parent().parent().before('<div class="select_attr product_select">' +
                    '<div class="col-sm-1"><i class="Hui-iconfont" onclick="container_remove(this)">&#xe6a1;</i></div>' +
                    '<label class="col-sm-3">' +
                    '<div>名称:</div>' +
                    '<input class="input-text select_name" type="text" value="'+ attr_name +'"></label>' +
                    '<label class="col-sm-2"><div>价格:</div><input class="input-text select_price" type="text" value="'+ attr_price +'" onchange="this.value=change_price(this.value)"></label>' +
                    '<label class="col-sm-2"><div>库存:</div><input class="input-text select_number" type="number" value="" onchange="change_number()"></label>' +
                    '<label class="col-sm-4"><div>图片:</div>' +
                    '<span class="btn-upload select_extra"><a href="javascript:void(0);" class="btn btn-primary radius"><i class="Hui-iconfont Hui-iconfont-ignore">&#xe642;</i>' +
                    ' {{__("admin.browse")}}'+
                    '&nbsp;</a><input type="file" class="input-file attr_photo_file" onchange="change_select_file(this)">' +
                    '<span class="wk_img_preview"><i class="Hui-iconfont Hui-iconfont-ignore">&#xe646;</i>' +
                    '<img data-url="" src=""/></span></span></label></div>');
            __wk_img_preview();
        }
        function select_attr_add_t(obj,attr_name,attr_price)
        {
            var attr_name = arguments[1] ? arguments[1] : '';
            var attr_price = arguments[2] ? arguments[2] : $('.input-text[name="goods_price"]').val() == '' ?  '0.00' : $('.input-text[name="goods_price"]').val();;
            $(obj).parent().parent().before('<div class="select_attr product_select"> ' +
                    '<div class="col-sm-1"><i class="Hui-iconfont" onclick="container_remove(this)">&#xe6a1;</i></div>' +
                    '<label class="col-sm-3"><span>名称:</span>' +
                    '<input class="input-text select_name" type="text" value="'+ attr_name +'"></label>' +
                    '<label class="col-sm-2"> <span class="mr-10">价格:</span> ' +
                    '<input class="input-text select_price" type="text" value="'+ attr_price +'" onchange="this.value=change_price(this.value)"></label>' +
                    '<label class="col-sm-2"><div>库存:</div><input class="input-text select_number" type="number" value="" onchange="change_number()"></label></div>');
        }
        function change_select_select(obj)
        {
            var attr_obj = $(obj).find('option:selected').val() > 0  ?  JSON.parse($(obj).find('option:selected').attr('data-json')) : '';
            if(!$.isEmptyObject(attr_obj))
            {
                $('.product_select_box').remove();
                $(attr_obj).each(function()
                {
                    if(this.type == 'defined')
                    {
                        select_box_add_t(this.name);
                    }
                    else if (this.type == 'preset')
                    {
                        select_box_add_t(this.name);
                        var button_obj = $('.product_select_box[data-name="'+ this.name +'"]').find('.select_attr_add i.Hui-iconfont');
                        var arr = this.data.split(',');
                        if($.type(arr) == 'array')
                        {
                            var price = $('.input-text[name="goods_price"]').val() == '' ?  '0.00' : $('.input-text[name="goods_price"]').val();
                            for (var i=0; i < arr.length;i++)
                            {
                                select_attr_add_t(button_obj,arr[i],price);
                            }

                        }
                    }
                });
            }
        }
        /*---------------------------------------------------------------------------------------------------------------------------------------*/
        /*商品展示属性*/
        function change_show_select(obj)
        {
            var attr_obj = $(obj).find('option:selected').val() > 0  ?  JSON.parse($(obj).find('option:selected').attr('data-json')) : '';
            if(!$.isEmptyObject(attr_obj))
            {
                $('.show_attr').remove();
                $(attr_obj).each(function()
                {
                    if(this.type == 'defined')
                    {
                        show_attr_add(this.name);
                    }
                    else if (this.type == 'preset')
                    {
                        show_attr_add(this.name,this.data);
                    }
                });
            }
        }
        function show_attr_add(attr_name,attr_value)
        {
            var attr_name = arguments[0] ? arguments[0] : '';
            var attr_value = arguments[1] ? arguments[1] : '';
            $('.show_attr_add').before('<div class="show_attr product_show">' +
                    '<div class="col-sm-1"><i class="Hui-iconfont" onclick="container_remove(this)">&#xe6a1;</i></div>' +
                    '<label class="col-sm-4">' +
                    '<span>名称:</span>' +
                    '<input class="input-text show_name" type="text" value="'+ attr_name +'"></label>' +
                    '<label class="col-sm-7">' +
                    '<span class="mr-10">属性值:</span>' +
                    '<input class="input-text show_data" type="text" value="' + attr_value + '">' +
                    '</label></div>');
        }

        /*相册预览图与数据对接*/
        function goods_photo_upload(obj)
        {
            var _this = $(obj);
            _UploadImageSaveOne(_this , function(data) {
                $(_this).parent().addClass('active');
                $(_this).siblings('img').attr("src" , data.url);
                $(_this).siblings('input[type="hidden"]').val(data.base);
                goods_photo_add();
            });
        }
        /*相册增加图片*/
        function goods_photo_add()
        {
            $('#goods_edit_photo_box').append('<div class="goods_edit_photo"> ' +
                    '<span class="btn-upload"><img src='+
                    '{{asset("adminStatic/images/goods_photo_add.png")}}' +
                    '><input type="hidden" name="goods_photo[]" value="">' +
                    '<input type="file" class="input-file" onchange="goods_photo_upload(this)"> ' +
                    '<span class="hover_delete" onclick="goods_photo_del(this)"><i class="Hui-iconfont">&#xe6e2;</i></span> ' +
                    '</span></div>')
        }
        /*相册删除图片*/
        function goods_photo_del(obj)
        {
            $(obj).parent().parent().remove();
        }

        /*序列化商品表单*/
        function serialize_goods_form()
        {
            var attr_select = [];
            var attr_show = [];
            var check_status = true;
            /*商品购买属性*/
            $('.product_select_box').each(function(index) {
                var _this = this;
                var obj = {};
                if(obj.name == '') check_status = false;
                if(obj.type == '') check_status = false;
                if($(_this).find('.product_select').length <= 0) check_status = false;

                obj.name = $(_this).attr('data-name');
                obj.type = $(_this).attr('data-type');
                obj.data = [];
                if(obj.type == 'text')/*文本*/
                {
                    $(_this).find('.product_select').each(function()
                    {
                        var temp_obj = {};
                        temp_obj.text    = $(this).find('.select_name').val();
                        temp_obj.price   = $(this).find('.select_price').val();
                        temp_obj.number  = $(this).find('.select_number').val();
                        if(temp_obj.text == '') check_status = false;
                        if(temp_obj.price == '') check_status = false;
                        if(temp_obj.number == '' || parseInt(temp_obj.number) < 0) check_status = false;

                        obj.data.push(temp_obj);
                    });
                }
                else if(obj.type == 'photo')/*图片*/
                {
                    $(_this).find('.product_select').each(function()
                    {
                        var temp_obj = {};
                        temp_obj.text    = $(this).find('.select_name').val();
                        temp_obj.price   = $(this).find('.select_price').val();
                        temp_obj.number  = $(this).find('.select_number').val();
                        temp_obj.extra  = $(this).find('.select_extra img').attr('data-url');
                        if(temp_obj.text == '') check_status = false;
                        if(temp_obj.price == '') check_status = false;
                        if(temp_obj.number == '' || parseInt(temp_obj.number) < 0) check_status = false;
                        if(temp_obj.extra == '') check_status = false;
                        obj.data.push(temp_obj);
                    });
                }

                attr_select.push(obj);
            });
            /*商品展示属性*/
            $('.product_show').each(function(index) {
                var obj = {};
                obj.name = $(this).find('.show_name').val();
                obj.data = $(this).find('.show_data').val();
                if(obj.name == '') check_status = false;
                if(obj.data == '') check_status = false;

                attr_show.push(obj);
            });

            if(check_status === true)
            {
                if(!$.isEmptyObject(attr_select)) {
                    $('input[name="select_attr"]').val($.toJSON(attr_select));
                }else {
                    $('input[name="select_attr"]').val('');
                }

                if(!$.isEmptyObject(attr_show)){
                    $('input[name="show_attr"]').val($.toJSON(attr_show));
                }else {
                    $('input[name="show_attr"]').val('');
                }
            }
            else
            {
                $('input[name="select_attr"]').val('');
                $('input[name="show_attr"]').val('');
                return false;
            }

        }

        $(document).ready(function(){
            var zTree = $.fn.zTree.init($('#goods_category_tree'), zTreeSetting, zTreeNodes);

            $('#parent_btn').click(function()
            {
                $('#goods_category_tree').toggle();
            });

            /*Tab标签*/
            $.Huitab("#tab-system .tabBar span","#tab-system .tabCon","current","click","0");

            /*UEditor编辑器*/
            var ue = UE.getEditor('editor');

            /*点击上传后显示图片在预览图中*/
            $('#goods_thumb').change(function()
            {
                var _this = $(this);
                _UploadImagePreviewOne(_this , function(path) {
                    $(_this).siblings('.wk_img_preview').find('img').attr("src" , path);
                });
            });

            //表单验证
            $("#form-article-add").validate({
                rules:{
                    goods_id:"required",
                    brand_id:"required",
                    goods_name:"required",
                    goods_number:"required",
                    category_text : "required",
                    goods_sort:"required",
                    goods_price: {
                        required:true,
                        number:true,
                        min:0
                    }
                },
                ignore:"",
                onkeyup:false,
                success:"valid",
                submitHandler:function(form){
                    var index = parent.layer.getFrameIndex(window.name);
                    $(form).ajaxSubmit({
                        url: '@if(empty($goods_info['goods_id'])){{action('Admin\GoodsController@GoodsAdd')}}@else {{action('Admin\GoodsController@GoodsEdit')}} @endif',
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSerialize:function(){
                            if(serialize_goods_form() === false){
                                layer.msg('属性录入不完整');
                                return false;
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

