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
  		<div class="refresh">
	  		<img src="{{asset('webStatic/images/refresh.png')}}" />
	  	</div>
			<div style="margin-bottom: 78px;">
				<a href="#" class="gmt-add"></a>
				<div class="tre-shaixuan platshaixuan" style="background: none;">
					<div class="plat_shanixuan">筛选</div>
					<div class="plat_stauschoose">
						<span style="margin-left: 24px;font-size: 16px;">类型</span>
					<select name="" class="tre-state palt type_val" style="margin-left: 15px;">
						 <option value="0">全部</option>
						 @foreach($category_list as $item)
				 		<option value="{{$item['category_id']}}" @if($page_search['category_id'] ==$item['category_id']) selected="selected" @endif>{{$item['category_name']}}</option>
				 		@endforeach
					</select>
					
					</div>
				</div>
			<a class="tre-btn">筛选</a>
			</div>

			<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 20%;"><span>商品名称</span></th>
						<th style="width: 17%;"><span>商品图片</span></th>
						<th style="width: 16%;"><span>所属分类</span></th>
					<!--	<th style="width: 14%;"><span>价格</span></th>
						<th style="width: 8%;"><span>单位</span></th>-->
						<th style="width: 13%;"><span>排序</span></th>

						<th><span style="">操作</span></th>
					</tr>
					
					 @foreach($product_list as $item)
					<tr>
						<td>{{$item['product_name']}}</td>
						<td><img src="{{\App\Models\MyFile::makeUrl($item['product_thumb'])}}" onerror="this.src='{{asset('webStatic/images/noimg.png')}}'"/></td>
						<td>{{$item['product_category']['category_name']}}</td>
						<!--<td>{{$item['product_price']}}元</td>
						<td>{{$item['product_unit']}}</td>-->
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
	//筛选
	
	  $(".tre-btn").on("click",function(){
    	
    	
	   var staus_val = $('.type_val option:selected').val();
    	var url="{{url('product/list')}}"+"/"+staus_val;
    	
    	location.replace(url);
    });
	
/*刷新*/
$(".refresh").on("click",function(){
	location.reload();
})	
	
	
	
	function ProductEdit(elm,product_id){
		
		layer.open({
						type: 2,
						title: false,
						maxmin: true,
						fixed :false,
						shadeClose: true, //点击遮罩关闭层
						area: ['920px', '700px'],
						content: '{{url('product/view')}}'+'/'+product_id
					});
		
		
		
		
	};
	//删除商品
    	function ProductDelete(elm,product_id){
    		 	if (confirm("确认删除吗？")){
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
		    				if(resData.code==0){
		    					 layer.msg(resData.messages, {icon: 1, time: 1000},function(){
		    					 });
		    					 
			    				 setTimeout(function(){
			    				 	if(!resData.code){
			    						$(elm).parent().parent().hide();
			    					}
			    				 },1200)
		    				}else{
		    					 layer.msg(resData.messages, {icon: 2, time: 1000});
		    				}
		    			}
		    		});
    		 		
    		 	}
    		
    	}
	;
		! function() {

			//页面一打开就执行，放入ready是为了layer所需配件（css、扩展模块）加载完毕
			layer.ready(function() {
				$('.gmt-add').on('click', function() {
					layer.open({
						type: 2,
						title: false,
						maxmin: true,
						fixed :false,
						shadeClose: true, //点击遮罩关闭层
						area: ['920px', '98%'],
						content: '{{url('product/view')}}'
					});
				});
			});

		}();
		
		
		
		
		
</script>
@endsection