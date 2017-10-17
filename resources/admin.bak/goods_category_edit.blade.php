@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}

@section('MyCss')
    <link rel="stylesheet" href="{{URL::asset('adminStatic/lib/jquery.zTree/css/metroStyle/metroStyle.css')}}">
@endsection

@section('content')
    <div class="page-container">
        <form action="" method="post" class="form form-horizontal" id="form-category-add">
            {{csrf_field()}}
            <input type="hidden" name="category_id" value="{{$category_info['category_id'] or 0}}">
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">ID：</label>
                <div class="formControls col-xs-6 col-sm-6">{{$category_info['category_id'] or '添加商品分类'}}</div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">
                    <span class="c-red">*</span>
                    {{__('admin.parentCategory')}}：
                </label>
                <div class="formControls col-xs-5 col-sm-5">
                    <div>
                        <input class="input-text" id="parent_text"   name="parent_id" type="text" readonly value="{{\App\Models\CommonModel::languageFormat($category_info['parent_info']['category_name'] , $category_info['parent_info']['category_en_name'])}}"/>
                        <input class="input-text" id="parent_hidden" name="parent_id" type="hidden" value="{{$category_info['parent_id']}}"/>
                    </div>
                    <div id="goods_category_tree" class="ztree"></div>
                </div>
                <div class="col-xs-1 col-sm-1">
                    <button type="button" id="parent_btn" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe642;</i> {{__('admin.browse')}}</button>
                </div>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">
                    <span class="c-red">*</span>
                    分类名称：</label>
                <div class="formControls col-xs-6 col-sm-6">
                    <input type="text" class="input-text" value="{{$category_info['category_name']}}" placeholder=""name="category_name">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">
                    <span class="c-red"></span>
                    分类英文名：</label>
                <div class="formControls col-xs-6 col-sm-6">
                    <input type="text" class="input-text" value="{{$category_info['category_en_name']}}" placeholder="" name="category_en_name">
                </div>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">
                    <span class="c-red">*</span>
                    分类别名(短名称)：</label>
                <div class="formControls col-xs-6 col-sm-6">
                    <input type="text" class="input-text" value="{{$category_info['alias_name']}}" placeholder=""name="alias_name">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">
                    <span class="c-red"></span>
                    分类英文别名(短名称)：</label>
                <div class="formControls col-xs-6 col-sm-6">
                    <input type="text" class="input-text" value="{{$category_info['alias_en_name']}}" placeholder="" name="alias_en_name">
                </div>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">
                    <span class="c-red">*</span>
                    排序：</label>
                <div class="formControls col-xs-6 col-sm-6">
                    <input type="text" class="input-text" value="{{$category_info['category_sort'] or '1000'}}" placeholder="" name="category_sort">
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
            { id:'{{$item['category_id']}}', pId:'{{$item['parent_id']}}', name:'{{$item['name']}}' + '({{$item['article_count']}})'},
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
                    parent_id:"required",
                    category_name:"required",
                    alias_name : "required",
                    category_sort:"required",
                },
                onkeyup:false,
                focusCleanup:true,
                success:"valid",
                submitHandler:function(form){
                    var index = parent.layer.getFrameIndex(window.name);
                    $(form).ajaxSubmit({
                        url: '{{action('Admin\GoodsController@CategoryEditSubmit')}}',
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
                }
            });
        });
    </script>
@endsection

