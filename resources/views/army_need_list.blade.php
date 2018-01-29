@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
  <link rel="stylesheet" href="{{asset('webStatic/css/page.css')}}">
<style>

</style>

@endsection
@section('content')
  <section style="position: relative;">
  	<div class="refresh">
  		<img src="{{asset('webStatic/images/refresh.png')}}" />
  	</div>
			<div style="margin-bottom: 78px;">
				<a href="#" class="mly-tianjia"></a>
				<div class="mly-shaixuan">
					<select name="status" class="mly-state">
						<option value="null">全部</option>
						<option value="待确认" @if($page_search['status'] == '待确认') selected="selected" @endif>待确认</option>
						<option value="已确认" @if($page_search['status'] == '已确认') selected="selected" @endif>已确认</option>
						<option value="已发货" @if($page_search['status'] == '已发货') selected="selected" @endif>已发货</option>
						<option value="已到货" @if($page_search['status'] == '已到货') selected="selected" @endif>已到货</option>
					</select>
					<!--<select name="" class="mly-time">
						<option value="">全部</option>
					</select>-->
					<input  autocomplete="off" onClick="laydate({istime: true, format: 'YYYY-MM-DD' })" class="laydate-icon mly-time"  name="army_receive_time" id="army_receive_time" value=@if($page_search['create_time'] != 'null') "{{$page_search['create_time']}}" @else "" @endif placeholder="请选择日期"/>
				</div>
				<a class="mly-btn">搜索</a>
			</div>

			<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 5%;"><span>序号</span></th>
						<!--	@if($manage_user['identity'] == '1')
							<th style="width: 8%;"><span>军方名称</span></th>
							@endif-->
						<th style="width: 10%;"><span>订单号</span></th>
						<th style="width: 10%;"><span>品名</span></th>
						<th style="width: 8%;"><span>规格</span></th>
						<th style="width: 8%;"><span>到货时间</span></th>
						<!--<th style="width: 8%;"><span style="">价格</span></th>-->
						<th style="width: 9%;"><span style="">数量</span></th>
						<th style="width: 8%;"><span style="">联系人</span></th>
						<th style="width: 8%;"><span style="">电话</span></th>
						<th style="width: 6%;"><span style="">备注</span></th>
						<th style="width: 7%;"><span style="">状态</span></th>
						<th style="width: 7%;"><span style="">质检状态</span></th>
						<th ><span style="">操作</span></th>
					</tr>
					
				 @foreach($order_list as $item)
            
            <tr>
                <td>{{$item['order_id']}}</td>
                <!--@if($manage_user['identity'] == '1')
						<td>{{$item['army_info']['nick_name']}}</td>
				@endif-->
                <td>{{$item['order_sn']}}</td>
                <td>{{$item['product_name']}}</td>
                <td>{{$item['spec_name']}}</td>
                <td>{{$item['army_receive_time']}}</td>
                <!-- <td>{{$item['product_price']}}元</td>-->
                <td>{{$item['product_number']}}{{$item['spec_unit']}}</td>
                <td>{{$item['army_contact_person']}}</td>
                <td>{{$item['army_contact_tel']}}</td> 
                <td><a noteData="{{$item['army_note']}}" onclick="noteShow(this,'{{$item['army_note']}}')">点击查看</a></td>
                <td>{{$item['status_text']}}</td>
                <td>{{$item['status_text']}}</td>
                <td class="blueWord">
                
                	@if($item['status'] == '0')
                               <a class="mly-caozuo" onclick="NeedEdit(this,'{{$item['order_id']}}')">修改</a>
                               <a style="margin-left: 8px;" class="mly-caozuo" onclick="NeedDelete(this,'{{$item['order_id']}}')">删除</a> 
	                @elseif($item['status'] == '1000')
	                   <a class="mly-caozuo" onclick="ConfirmReceive(this,'{{$item['order_id']}}')">已到货</a>
                	@endif
                		<a style="margin-left: 5px;">打印</a>
                </td>
            </tr>
            @endforeach

				</tbody>
			</table>
 @include('include.inc_pagination',['pagination'=>$order_list])

		
		</section>
@endsection

@section('MyJs')
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery-calendar/js/laydate.js')}}"></script>

<script>
	
	/*点击查看*/
	function noteShow(elm,noteData){
		layer.open({
            type: 1, 
            title: false,
            maxmin: false,
            fixed :false,
            shadeClose: true, //点击遮罩关闭层
            area: ['400px' , '250px'],
            content: '<div style="font-size:16px;padding-left:20px;padding-right:20px;padding-top:10px;">'+noteData+'</div>'
        });
		
	}
	
	
		!function(){

	laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库

	laydate({elem: '#army_receive_time'});//绑定元素

}();
/*刷新*/
$(".refresh").on("click",function(){
	location.reload();
})

/*搜索*/
	
	$(".mly-btn").on("click",function(){
		
		var mlystate_val=$(".mly-state option:selected").val();
		
		 if($(".laydate-icon").val()==""){
	    	var cre_time=null;
	    }else{
	    	var cre_time=$(".laydate-icon").val();
	    }
	    
	var url="{{url('army/need/list')}}"+"/"+mlystate_val+"/"+cre_time;
	
	location.replace(url);
	})

	
	
	//需求修改
	function NeedEdit(elm,order_id){
		layer.open({
            type: 2, 
            title: false,
            maxmin: false,
            fixed :false,
            shadeClose: true, //点击遮罩关闭层
            area: ['965px' , '550px'],
            content: '{{url('army/need/view/edit')}}'+'/'+order_id
        });
		
	}
	//删除需求
	function NeedDelete(elm,order_id){
		 	if (confirm("确认删除吗？")){
	    		$.ajax({
	    			type:"post",
	    			url:"{{url('army/need/delete')}}",
	    			async:true,
	    			data:{
	    				order_id:order_id,
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
    //确认收货	
    	function ConfirmReceive(elm,order_id){
    	  if (confirm("确认收货吗？")){
	    		$.ajax({
	    			type:"post",
	    			url:"{{url('army/confirm/receive')}}",
	    			async:true,
	    			data:{
	    				order_id:order_id,
	    				_token:'{{csrf_token()}}'
	    			},
	    			success:function(res){
	    				
	    				var resData=JSON.parse(res);
	    				if(resData.code==0){
	    					 layer.msg(resData.messages, {icon: 1, time: 1000},function(){
	    					 		location.reload();
	    					 });
	    				}else{
	    					 layer.msg(resData.messages, {icon: 2, time: 1000});
	    				}
	    				
	    			}
	    		}); 
    		}
    	}    	
	$(function(){
		layer.ready(function(){ 
		  $('.mly-tianjia').on('click', function(){
		    layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		      fixed :false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['965px' , '850px'],
		      content: '{{url('army/need/view/release')}}'
		    });
		  });
		});
	})



</script>
@endsection