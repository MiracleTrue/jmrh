@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}

@section('content')
	@include('admin.include.inc_nav')
	<div class="page-container">
		<form action="" class="form form-horizontal" id="form-article-add" method="post" enctype="multipart/form-data">
			<div id="tab-system" class="HuiTab">
				<div class="tabBar cl">
					@foreach($config_list as $base)
						<span>{{__('admin.config.'.$base['name_code'])}}</span>
					@endforeach
				</div>
				@foreach($config_list as $base)
					<div class="tabCon">
						@if($base['child'])
							@foreach($base['child'] as $value)

								@if($value['type'] == 'text')
									<div class="row cl">
										<label class="form-label col-xs-4 col-sm-2">
											{{__('admin.config.'.$value['name_code'])}}：</label>
										<div class="formControls col-xs-8 col-sm-9">
											<input type="text" id="website-{{$value['name_code']}}" name="{{$value['name_code']}}" placeholder="{{__('admin.config.'.$value['name_code'].'_placeholder')}}" value="{{$value['value']}}" class="input-text">
										</div>
									</div>
								@endif

								@if($value['type'] == 'radio')
									<div class="row cl">
										<label class="form-label col-xs-4 col-sm-2">
											{{__('admin.config.'.$value['name_code'])}}：</label>
										<div class="formControls col-xs-8 col-sm-9 skin-minimal">
											@foreach($value['html_range'] as $row_key => $row_range)
											<div class="radio-box">
												<input name="{{$value['name_code']}}" value="{{$row_range['value']}}" id="website-{{$value['name_code']}}{{$row_key}}"  type="radio" @if($row_range['value'] == $value['value'] ) checked @endif >
												<label for="website-{{$value['name_code']}}{{$row_key}}}">{{$row_range['name']}}</label>
											</div>
											@endforeach
										</div>
									</div>
								@endif

								@if($value['type'] == 'file')
									<div class="row cl">
										<label class="form-label col-xs-4 col-sm-2">
											{{__('admin.config.'.$value['name_code'])}}：</label>
										<span class="btn-upload formControls col-xs-8 col-sm-9">
											<a href="javascript:void(0);" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe642;</i> {{__('admin.browse')}}&nbsp;</a>
											@if($value['name_code'] == 'shop_logo' || $value['name_code'] == 'shop_default_picture')
												<input type="file" name="{{$value['name_code']}}" id="website-{{$value['name_code']}}" class="input-file config_input_file" accept="image/png">
											@else
												<input type="file" name="{{$value['name_code']}}" id="website-{{$value['name_code']}}" class="input-file config_input_file">
											@endif
											<span class="wk_img_preview">
												<i class="Hui-iconfont">&#xe646;</i>
												<img src="{{\App\Models\MyFile::makeUrl($value['value'])}}?{{\Carbon\Carbon::now()}}" alt="" />
											</span>
										</span>
									</div>
								@endif

								@if($value['type'] == 'select')
									<div class="row cl">
										<label class="form-label col-xs-4 col-sm-2">
											{{__('admin.config.'.$value['name_code'])}}：</label>
										<div class="formControls col-xs-8 col-sm-9">
											<select class="select" size="1" name="{{$value['name_code']}}" style="width: auto">
												<option value="" selected>默认selasdasdasdasd1231ect</option>
												<option value="1">菜单一</option>
												<option value="2">菜单二</option>
												<option value="3">菜单三</option>
											</select>
										</div>
									</div>
								@endif

								@if($value['type'] == 'checkbox')
									<div class="row cl">
										<label class="form-label col-xs-4 col-sm-2">
											{{__('admin.config.'.$value['name_code'])}}：</label>
										<div class="formControls col-xs-8 col-sm-9 skin-minimal">
											<div class="check-box">
												<input type="checkbox" name="{{$value['name_code']}}" id="website-{{$value['name_code']}}" checked>
												<label for="website-{{$value['name_code']}}">复选框 checked状态</label>
											</div>
										</div>
									</div>
								@endif

							@endforeach
						@endif
					</div>
				@endforeach
				<div class="row cl">
					<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
						<button onClick="_config_submit(this.form);" class="btn btn-primary radius" type="button"><i class="Hui-iconfont">&#xe632;</i> {{__('common.submit')}}</button>
						<button onClick="location.replace(location.href);" class="btn btn-default radius" type="button">&nbsp;&nbsp;{{__('common.cancel')}}&nbsp;&nbsp;</button>
						{{csrf_field()}}
					</div>
				</div>
			</div>
		</form>
	</div>
	@include('admin.include.inc_footer')
@endsection

@section('MyJs')
	<!--请在下方写此页面业务相关的脚本-->
	<script type="text/javascript" src="{{asset('adminStatic/lib/My97DatePicker/4.8/WdatePicker.js')}}"></script>
	<script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/jquery.validate.js')}}"></script>
	<script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/validate-methods.js')}}"></script>
	<script type="text/javascript" src="{{asset('adminStatic/lib/jquery.validation/1.14.0/messages_zh.js')}}"></script>

	<script type="text/javascript">

		/*表单提交函数*/
		function _config_submit (form)
		{
			$(form).ajaxSubmit({
				url: '{{action('Admin\ConfigController@ConfigSubmit')}}',
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
							location.replace(location.href);
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

		$(function(){
			/*H-ui文件上传控件优化,点击上传后显示图片在预览图中*/
			$('.config_input_file').change(function()
			{
				var _this = $(this);
				_UploadImagePreviewOne(_this , function(path) {
					$(_this).siblings('.wk_img_preview').find('img').attr("src" , path);
				});
			});


			$('.skin-minimal input').iCheck({
				checkboxClass: 'icheckbox-blue',
				radioClass: 'iradio-blue',
				increaseArea: '20%'
			});
			$.Huitab("#tab-system .tabBar span","#tab-system .tabCon","current","click","0");
		});


	</script>
@endsection