@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
  
<style>
	/*分页样式*/
.userlist_pag{
	height: 45px;
	text-align: center;
	margin-top: 57px;
}
.userlist_pag ul{
	overflow: hidden;
	height: 43px;
	text-align: center;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    width: 85%;
  -webkit-box-pack: center;
    -webkit-justify-content: center;
    -ms-flex-pack: center;
    justify-content: center;
  
}
.userlist_pag ul li{
	
	height: 41px;
	line-height: 41px;
	text-align: center;
	color: #0e99dc;
	font-size: 15px;
	border-left:1px solid #dddddd ;
	border-top:1px solid #dddddd ;
	border-bottom:1px solid #dddddd ;
	width: 46px;
   
	
	
}
.userlist_pag ul li a, .userlist_pag ul li span{
	display: inline-block;
	width: 100%;
	height: 100%;
}
.userlist_pag ul li a{
	color:#0e99dc ;
}
.userlist_pag ul li:nth-child(1),.userlist_pag ul li:last-child{
	width: 89px;
}
.userlist_pag .active{
	background-color: #FE8D01;
	color: #FFFFFF;
}
.userlist_pag ul li:last-child{
	border-right:1px solid #dddddd;
}
</style>

@endsection
@section('content')
  <section>
			<div>
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
					<input  onClick="laydate({istime: true, format: 'YYYY-MM-DD' })" class="laydate-icon mly-time"  name="army_receive_time" id="army_receive_time" value=@if($page_search['create_time'] != 'null') "{{$page_search['create_time']}}" @else "" @endif placeholder="请选择日期"/>
				</div>
				<a class="mly-btn">搜索</a>
			</div>

			<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 9%;"><span>序号</span></th>
						<th style="width: 18%;"><span>订单号</span></th>
						<th style="width: 10%;"><span>品名</span></th>
						<th style="width: 8%;"><span>下单时间</span></th>
						<th style="width: 8%;"><span>到货时间</span></th>
						<th style="width: 14%;"><span style="">数量</span></th>
						<th style="width: 14%;"><span style="">状态</span></th>
						<th style="width: 15%"><span style="">操作</span></th>
					</tr>
					
				 @foreach($order_list as $item)
            
            <tr>
                <td>{{$item['order_id']}}</td>
                <td>{{$item['order_sn']}}</td>
                <td>{{$item['product_name']}}</td>
                <td>{{$item['create_time']}}</td>
                <td>{{$item['army_receive_time']}}</td>
                <td>{{$item['product_number']}}{{$item['product_unit']}}</td>
                <td>{{$item['status_text']}}</td>
                <td class="blueWord">
                	@if($item['status'] == '0')
                               <a class="mly-caozuo" onclick="NeedEdit(this,'{{$item['order_id']}}')">修改</a>
                               <a class="mly-caozuo" onclick="NeedDelete(this,'{{$item['order_id']}}')">删除</a> 
	                @elseif($item['status'] == '1000')
	                   <a class="mly-caozuo"  >已到货</a>
                	@endif
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
		!function(){

	laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库

	laydate({elem: '#army_receive_time'});//绑定元素

}();

/*搜索*/
	
	$(".mly-btn").on("click",function(){
		
		var mlystate_val=$(".mly-state option:selected").val();
		
		 if($(".laydate-icon").val()==""){
	    	var cre_time=null;
	    }else{
	    	var cre_time=$(".laydate-icon").val();
	    }
	    
	var url="{{url('army/need/list')}}"+"/"+mlystate_val+"/"+cre_time;
	console.log(url)
	location.replace(url);
	})

	
	
	//需求修改
	function NeedEdit(elm,order_id){
		layer.open({
            type: 2,
            title: false,
            maxmin: false,
            shadeClose: true, //点击遮罩关闭层
            area: ['900px' , '500px'],
            content: '{{url('army/need/view')}}'+'/'+order_id
        });
		
	}
	//删除需求
	function NeedDelete(elm,order_id){
    		$.ajax({
    			type:"post",
    			url:"{{url('army/need/delete')}}",
    			async:true,
    			data:{
    				order_id:order_id,
    				_token:'{{csrf_token()}}'
    			},
    			success:function(res){
    				console.log(res);
    				
    				var resData=JSON.parse(res);
    				 layer.msg(resData.messages, {icon: 1, time: 1000});
    				 setTimeout(function(){
    				 	if(!resData.code){
    					$(elm).parent().parent().hide();
    				}
    				 },1200)
    				
    			}
    		});
    	}
	
	$(function(){
		layer.ready(function(){ 
		  $('.mly-tianjia').on('click', function(){
		    layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['900px' , '500px'],
		      content: '{{url('army/need/view')}}'
		    });
		  });
		});
	})



</script>
@endsection