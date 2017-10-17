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
                <div class="col-sm-12 l"><i class="Hui-iconfont c-primary pr-5">&#xe677;</i><span class="c-primary f-12">选择分类中的 (数量) 代表分类下的文章数量</span></div>
            </div>
        </div>
        <form action="" class="form form-horizontal" id="form-article-add" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" name="article_id" value="{{$article_info['article_id'] or 0}}">
            <div id="tab-system" class="HuiTab">
                <div class="tabBar cl">
                    <span>中文版</span>
                    <span>英文版</span>
                </div>
                {{--中文版--}}
                <div class="tabCon">
                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2">ID：</label>
                        <div class="formControls col-xs-8 col-sm-9">{{$article_info['article_id'] or '添加文章'}}</div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red"></span>文章标题：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{$article_info['article_title']}}" placeholder="" name="article_title">
                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>文章分类：</label>
                        <div class="formControls col-xs-7 col-sm-8">
                            <div>
                                <input class="input-text" id="parent_text" name="category_text" type="text" readonly value="{{\App\Models\CommonModel::languageFormat($article_info['article_category']['category_name'] , $article_info['article_category']['category_en_name'])}}"/>
                                <input id="parent_hidden" name="category_id" type="hidden" value="{{$article_info['category_id']}}"/>
                            </div>
                            <div id="select_article_tree" class="ztree"></div>
                        </div>
                        <div class="col-xs-1 col-sm-1">
                            <button type="button" id="parent_btn" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe642;</i> {{__('admin.browse')}}</button>
                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2">关键词：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{$article_info['article_keywords']}}" placeholder=""name="article_keywords">
                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2">文章作者：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{$article_info['article_author']}}" placeholder=""name="article_author">
                        </div>
                    </div>


                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2">缩略图：</label>
                        <span class="btn-upload formControls col-xs-8 col-sm-9">
                            <a href="javascript:void(0);" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe642;</i> {{__('admin.browse')}}&nbsp;</a>
                            <input type="file" name="article_thumb" id="article_thumb" class="input-file">
                            <span class="wk_img_preview">
                                <i class="Hui-iconfont">&#xe646;</i>
                                <img src="{{$article_info['article_thumb']}}"/>
                            </span>
                        </span>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2">浏览次数：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{$article_info['browse_count'] or '0'}}" placeholder="" name="browse_count">
                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2">排序值：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="number" class="input-text" value="{{$article_info['article_sort'] or '1000'}}" placeholder="" name="article_sort">
                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2">文章内容：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <script id="zh_editor" name="article_content" type="text/plain" style="width:100%;height:400px;">{!! $article_info['article_content'] !!}</script>
                        </div>
                    </div>
                </div>

                {{--英文版--}}
                <div class="tabCon">
                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2"><span class="c-red"></span>文章英文标题：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <input type="text" class="input-text" value="{{$article_info['article_en_title']}}" placeholder="" name="article_en_title">
                        </div>
                    </div>

                    <div class="row cl">
                        <label class="form-label col-xs-4 col-sm-2">文章英文内容：</label>
                        <div class="formControls col-xs-8 col-sm-9">
                            <script id="en_editor" name="article_en_content" type="text/plain" style="width:100%;height:400px;">{!! $article_info['article_en_content'] !!}</script>
                        </div>
                    </div>

                </div>

                <div class="row cl">
                    <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                        <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存并提交审核</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('MyJs')
    <script type="text/javascript" src="{{asset('adminStatic/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/messages_'.session('AdminLanguage').'.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/jquery.zTree/js/jquery.ztree.all.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/ueditor/1.4.3/ueditor.config.js')}}"></script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/ueditor/1.4.3/ueditor.all.min.js')}}"> </script>
    <script type="text/javascript" src="{{asset('adminStatic/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js')}}"></script>
    <script type="text/javascript">
        /*分类树*/
        var zTreeNodes =[
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
                    $('#select_article_tree').hide();
                }

            }
        };

        $(function(){
            var zTree = $.fn.zTree.init($('#select_article_tree'), zTreeSetting, zTreeNodes);

            $('#parent_btn').click(function()
            {
                $('#select_article_tree').toggle();
            });

            /*Tab标签*/
            $.Huitab("#tab-system .tabBar span","#tab-system .tabCon","current","click","0");

            /*UEditor编辑器*/
            var zh_ue = UE.getEditor('zh_editor');
            var en_ue = UE.getEditor('en_editor');

            /*点击上传后显示图片在预览图中*/
            $('#article_thumb').change(function()
            {
                var _this = $(this);
                _UploadImagePreviewOne(_this , function(path) {
                    $(_this).siblings('.wk_img_preview').find('img').attr("src" , path);
                });
            });

            //表单验证
            $("#form-article-add").validate({
                rules:{
                    article_id:"required",
                    category_text : "required",
                    article_sort:"digits",
                    browse_count:"digits"
                },
                onkeyup:false,
                focusCleanup:true,
                success:"valid",
                submitHandler:function(form){
                    var index = parent.layer.getFrameIndex(window.name);
                    $(form).ajaxSubmit({
                        url: '{{action('Admin\ArticleController@InfoEditSubmit')}}',
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

