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
						<th style="width: 20%;"><span>分类名称</span></th>
						<th style="width: 17%;"><span>商品数量</span></th>
						<th style="width: 16%;"><span>数量单位</span></th>
						<th style="width: 13%;"><span>排序</span></th>
						<th style="width: 13%;"><span>是否首页显示</span></th>


						<th><span style="">操作</span></th>
					</tr>
					
					  @foreach($category_list as $item)
					<tr>
						<td>{{$item['category_name']}}</td>
						<td>{{$item['product_count']}}</td>
						<td>{{$item['unit']}}</td>
						<td>{{$item['sort']}}</td>
						<td class="checkbox_td">
							<label>是</label> <input @if($item['is_index']==1) checked="checked"@endif class="chinput" type="checkbox" name="Fruit" id="" value="" onclick="CategoryIndex(this,'{{$item['category_id']}}')"/>

						</td>
						<td class="blueWord">
							<a class="mly-caozuo" onclick="CategoryEdit(this,'{{$item['category_id']}}')">编辑</a>
							<a style="margin-left: 5%;" onclick="cateDelete(this,'{{$item['category_id']}}')">删除</a>
						</td>
					</tr>
					 @endforeach					
				</tbody>
			</table>
        @include('include.inc_pagination',['pagination'=>$category_list])

		</section>
@endsection

@section('MyJs')
<script>
/*	$(".chinput").on("click",function(){
	
		$(this).attr("checked",true);
		$(this).siblings().attr("checked",false);
		
		
		
	})*/
	/*是否首页显示*/
	function CategoryIndex(elm,category_id){
		
		if($(elm).is(':checked')){
			$.ajax({
				type:"post",
				url:"{{url('category/is/index')}}",
				async:true,
				data:{
					category_id:category_id,
    				_token:'{{csrf_token()}}'
				},
				success:function(res){
					console.log(res)
				}
			});
		}else{
			$.ajax({
				type:"post",
				url:"{{url('category/no/index')}}",
				async:true,
				data:{
					category_id:category_id,
    				_token:'{{csrf_token()}}'
				},
				success:function(res){
					console.log(res)
				}
			});
		}
		
		
	}
	
	
	
		//删除分类
    	function cateDelete(elm,category_id){
    		if (confirm("确认删除吗？")){
	    		$.ajax({
	    			type:"post",
	    			url:"{{url('category/delete')}}",
	    			async:true,
	    			data:{
	    				category_id:category_id,
	    				_token:'{{csrf_token()}}'
	    			},
	    			success:function(res){
	    				console.log(res);
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
	
	
	
	function CategoryEdit(elm,category_id){
		
		layer.open({
						type: 2,
						title: false,
						maxmin: false,
						shadeClose: true, //点击遮罩关闭层
						area: ['920px', '413px'],
						content: '{{url('category/view')}}'+'/'+category_id
					});
		
		
		
		
	}
		! function() {

			//页面一打开就执行，放入ready是为了layer所需配件（css、扩展模块）加载完毕
			layer.ready(function() {
				$('.gmt-add').on('click', function() {
					
					layer.open({
						type: 2,
						title: false,
						maxmin: false,
						shadeClose: true, //点击遮罩关闭层
						area: ['920px', '413px'],
						content: '{{url('category/view')}}'
					});
				});
			});

		}();
		
	/*首页显示分类*/	
		
		
</script>
@endsection