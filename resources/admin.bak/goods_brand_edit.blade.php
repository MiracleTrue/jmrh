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
                <div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">选择分类中的 (数量) 代表分类下的品牌数量</span></div>
            </div>
        </div>
        <form action="" method="post" class="form form-horizontal" id="form-category-add">
            {{csrf_field()}}
            <input type="hidden" name="brand_id" value="{{$brand_info['brand_id'] or 0}}">
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">ID：</label>
                <div class="formControls col-xs-6 col-sm-6">{{$brand_info['brand_id'] or '添加商品品牌'}}</div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>商品分类：</label>
                <div class="formControls col-xs-5 col-sm-5">
                    <div>
                        <input class="input-text" id="parent_text"   name="category_id" type="text" readonly value="{{\App\Models\CommonModel::languageFormat($brand_info['goods_category']['category_name'] , $brand_info['category']['category_en_name'])}}"/>
                        <input class="input-text" id="parent_hidden" name="category_id" type="hidden" value="{{$brand_info['category_id']}}"/>
                    </div>
                    <div id="goods_category_tree" class="ztree"></div>
                </div>
                <div class="col-xs-1 col-sm-1">
                    <button type="button" id="parent_btn" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe642;</i> {{__('admin.browse')}}</button>
                </div>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">
                    <span class="c-red">*</span>品牌Logo：</label>
                <span class="btn-upload formControls col-xs-6 col-sm-6">
                    <a href="javascript:void(0);" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe642;</i> {{__('admin.browse')}}&nbsp;</a>
                    <input type="file" name="brand_logo" id="brand_logo" class="input-file" >
                    <span class="wk_img_preview">
                        <i class="Hui-iconfont">&#xe646;</i>
                        <img src="{{$brand_info['brand_logo']}}"/>
                    </span>
                </span>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">
                    <span class="c-red">*</span>品牌名称：</label>
                <div class="formControls col-xs-6 col-sm-6">
                    <input type="text" class="input-text" value="{{$brand_info['brand_name']}}" placeholder=""name="brand_name">
                </div>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">
                    <span class="c-red">*</span>品牌描述：</label>
                <div class="formControls col-xs-6 col-sm-6">
                    <textarea name="brand_description" class="textarea" placeholder="说点什么...最少输入10个字符" onKeyUp="$.Huitextarealength(this,100)">{{$brand_info['brand_description']}}</textarea>
                    <p class="textarea-numberbar"><em class="textarea-length">0</em>/100</p>
                </div>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">
                    <span class="c-red">*</span>排序：</label>
                <div class="formControls col-xs-6 col-sm-6">
                    <input type="text" class="input-text" value="{{$brand_info['brand_sort'] or '1000'}}" placeholder="" name="brand_sort">
                </div>
            </div>

            <div class="row cl">
                <div class="col-9 col-offset-2">
                    <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
                </div>
            </div>
        </form>
    </div>
@endsection

@section('MyJs')
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('adminStatic/lib/jquery.validation/1.14.0/messages_'.session('AdminLanguage').'.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/jquery.zTree/js/jquery.ztree.all.min.js')}}"></script>
    <script type="text/javascript">
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

            /*点击上传后显示图片在预览图中*/
            $('#brand_logo').change(function()
            {
                var _this = $(this);
                _UploadImagePreviewOne(_this , function(path) {
                    $(_this).siblings('.wk_img_preview').find('img').attr("src" , path);
                });
            });

            $("#form-category-add").validate({
                rules:{
                    category_id:"required",
                    @if($brand_info['brand_id'] == 0)
                    brand_logo:"required",
                    @endif
                    brand_name:"required",
                    brand_description : {
                        required:true,
                        minlength:10
                    },
                    brand_sort:"required",
                },
                onkeyup:false,
                focusCleanup:true,
                success:"valid",
                submitHandler:function(form){
                    var index = parent.layer.getFrameIndex(window.name);
                    $(form).ajaxSubmit({
                        url: '{{action('Admin\GoodsController@BrandEditSubmit')}}',
                        type: 'POST',
                        dataType: 'JSON',
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

