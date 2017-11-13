@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/css/goods-management.css')}}">
 	<link rel="stylesheet" href="{{asset('webStatic/css/page.css')}}">
<style>
	
</style>
@endsection
@section('content')
  <section>
			<div>
				<a href="#" class="gmt-add"></a>

			</div>

			<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 20%;"><span>商品名称</span></th>
						<th style="width: 17%;"><span>商品图片</span></th>
						<th style="width: 16%;"><span>所属分类</span></th>
						<th style="width: 13%;"><span>排序</span></th>

						<th><span style="">操作</span></th>
					</tr>
					
					 @foreach($product_list as $item)
					<tr>
						<td>{{$item['product_name']}}</td>
						<td><img src="{{\App\Models\MyFile::makeUrl($item['product_thumb'])}}" /></td>
						<td>{{$item['product_category']['category_name']}}</td>
						<td>{{$item['sort']}}</td>

						<td class="blueWord">
							<a class="mly-caozuo" onclick="ProductEdit(this,'{{$item['product_id']}}')">编辑</a>
							<a style="margin-left: 5%;" onclick="ProductDelete(this,'{{$item['product_id']}}')">删除</a>
						</td>
					</tr>
					 @endforeach
				</tbody>
			</table>
        @include('include.inc_pagination',['pagination'=>$product_list])

		</section>

@endsection
@section('MyJs')
<script>
	function ProductEdit(elm,product_id){
		
		layer.open({
						type: 2,
						title: false,
						maxmin: true,
						shadeClose: true, //点击遮罩关闭层
						area: ['920px', '650px'],
						content: '{{url('product/view')}}'+'/'+product_id
					});
		
		
		
		
	};
	//删除商品
    	function ProductDelete(elm,product_id){
    		$.ajax({
    			type:"post",
    			url:"{{url('product/delete')}}",
    			async:true,
    			data:{
    				product_id:product_id,
    				_token:'{{csrf_token()}}'
    			},
    			success:function(res){
    				
    				var resData=JSON.parse(res);
    				alert(resData.messages);
    				if(!resData.code){
    					$(elm).parent().parent().hide();
    				}
    			}
    		});
    	}
	;
		! function() {

			//页面一打开就执行，放入ready是为了layer所需配件（css、扩展模块）加载完毕
			layer.ready(function() {
				$('.gmt-add').on('click', function() {
					
					layer.open({
						type: 2,
						title: false,
						maxmin: false,
						shadeClose: true, //点击遮罩关闭层
						area: ['920px', '913px'],
						content: '{{url('product/view')}}'
					});
				});
			});

		}();
		
		
		
		
		
</script>
@endsection