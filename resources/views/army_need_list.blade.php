@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
  	<link rel="stylesheet" href="{{asset('webStatic/css/page.css')}}">
	<link rel="stylesheet" type='text/css' href="{{asset('webStatic/css/print2.css')}}">
	<link rel="stylesheet" media='print' href="{{asset('webStatic/css/military.css')}}">
  	<link rel="stylesheet" type='text/css' media='print' href="{{asset('webStatic/css/print.css')}}">
<style>

</style>

@endsection
@section('content')
  <section style="position: relative;">
  	<div class="refresh" style="top: 149px;">
  		<img src="{{asset('webStatic/images/refresh.png')}}" />
  	</div>
			<div style="margin-bottom: 30px;line-height: 36px;">
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
			<div style="margin-bottom: 70px;">
					<input style="width: 120px; margin-left: 10px;"  autocomplete="off" style="margin-left: 15px;" onClick="laydate({format: 'YYYY-MM-DD',elem:'#start_time'})" class="laydate-icon tre-time start_time"  name="army_receive_time" id="start_time"  placeholder="请选择日期"/>
					<span>-</span>
					<input style="width: 120px;margin-left: 0;"  autocomplete="off" style="margin-left: 15px;" onClick="laydate({format: 'YYYY-MM-DD',elem:'#end_time'})" class="laydate-icon tre-time end_time"  name="army_receive_time" id="end_time"  placeholder="请选择日期"/>
					<a onclick="biaoge(this)" style="margin-left: 10px;color: blue;font-size: 14px;">导出表格到本地</a>	
					<a class="printdingdan" style="margin-left: 10px;color: blue;font-size: 14px;" >打印</a>
					<a class="printAll" style="margin-left: 10px;color: blue;font-size: 14px;">确认打印</a>
					<a class="cancelprint" style="margin-left: 10px;color: blue;font-size: 14px;">取消打印</a>
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
            
            <tr class="orer_id" order_id="{{$item['order_id']}}">
                <td >{{$item['order_id']}}</td>
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
                <td>{{$item['quality_check_text']}}</td>
                <td class="blueWord">
                
                	@if($item['status'] == '0')
                               <a class="mly-caozuo" onclick="NeedEdit(this,'{{$item['order_id']}}')">修改</a>
                               <a style="margin-left: 8px;" class="mly-caozuo" onclick="NeedDelete(this,'{{$item['order_id']}}')">删除</a> 
	                @elseif($item['status'] == '1000')
	                   <a class="mly-caozuo" onclick="ConfirmReceive(this,'{{$item['order_id']}}')">已到货</a>
                	@endif
                		<a style="margin-left: 5px;" onclick="print(this,'{{$item['order_id']}}')">打印</a>
                		<a style="margin-left: 5px;float: right;margin-right: 5px;"><input type="checkbox" class="printcheck" name="" id="" value="" order_id="{{$item['order_id']}}"/></a>
                </td>
            </tr>
            @endforeach

				</tbody>
			</table>
 @include('include.inc_pagination',['pagination'=>$order_list])

		
		</section>
	<!--	<table style="width: 800px;" class="">
			<tbody >
				<tr style="border: 1px solid #333333;">
					<td style="width: 10%;border-right:1px solid #333333 ;">序号</td>
					<td style="width: 20%;border-right:1px solid #333333 ;">      </td>
					<td style="border-right:1px solid #333333 ;width: 20%;"cellspacing="20px">订单编号</td>
					<td></td>
				</tr>
				<tr style="border: 1px solid #000000;">
					<td>商品名称</td>
					<td>      </td>
					<td>下单时间</td>
					<td></td>
				</tr>
				<tr style="border: 1px solid #000000;">
					<td>到货时间</td>
					<td>      </td>
					<td>商品数量</td>
					<td></td>
					
				</tr>
			<tr style="border: 1px solid #000000;">
					<td>商品规格</td>
					<td>      </td>
					<td>商品 单价</td>
					<td></td>
					
				</tr>
				<tr style="border: 1px solid #000000;">
					<td style="border-right:1px solid #333333;">商品总价</td>
					<td colspan="3">      </td>
					
					
				</tr>
				<tr style="border: 1px solid #000000;">
					<td>订单状态</td>
					<td  colspan="3">      </td>
					
					
				</tr>
				<tr style="border: 1px solid #000000;">
					<td >商品质检状态</td>
					<td  colspan="3">      </td>
					
					
				</tr>
				<tr style="border: 1px solid #000000;" >
					<td style="border:1px solid #333333 ;"	rowspan="2">备注</td>
					<td style="border:1px solid #333333 ;" colspan="3"rowspan="2">      </td>
					
					
				</tr>
				<tr style="border: 1px solid #000000;" >
					
					
				</tr>
				
			</tbody>
		</table>
		-->
		<div id="myprint">
			
		</div>
		
@endsection

@section('MyJs')
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery-calendar/js/laydate.js')}}"></script>
	<script src="http://www.jq22.com/jquery/jquery-migrate-1.2.1.min.js"></script>
 <script type="text/javascript" src="{{asset('/webStatic/library/jquery.jqprint/jquery.jqprint-0.3.js')}}"></script>
<script>
	function biaoge(){
		var start_date=$(".start_time").val();
		var end_date=$(".end_time").val();
		if(start_date=="" || end_date==""){
			alert("时间选择不能为空")
			
		}else{
			location.href="{{url('army/output/excel')}}"+"/"+start_date+"/"+end_date
			
		}
		
	
		
	}
		var printData="";
		var allPrintData="";
		$(".printcheck").hide();
		$(".printAll").hide();
		$(".cancelprint").hide();
		
		
		$(".cancelprint").click(function(){
			$(".printAll").hide();
			$(".printdingdan").show();
			$(this).hide();
			$(".printcheck").hide();
			
		})
		
		$(".printdingdan").click(function(){
			$(".printcheck").show();
			$(this).hide();
			$(".cancelprint").show();
			
			$(".printAll").show();
		})
		
		$(".printAll").click(function(){
			
					$(".printcheck").each(function(i,index){
								if($(index).is(":checked")){
									printData += $(index).attr("order_id")+",";
								}
						
							
						})
					
						if(printData.length>0){
						 	 allPrintData=printData.substr(0,printData.length-1);
						 }	
				
				
						$.ajax({
						type:"post",
						url:"{{url('army/output/print')}}",
						async:true,
						data:{
					    		order_ids:allPrintData,
					    		_token:'{{csrf_token()}}'
					    	},
						success:function(resData){
							printData="";
							var data=JSON.parse(resData)
							console.log(data)
							var mydata=data.data;
							$("#myprint").empty();
							for(var i in mydata){
								$("#myprint").append('<table style="width: 800px;" class="printone"><tbody><tr style="border: 1px solid #333333;"><td style="width: 10%;border-right:1px solid #333333 ;">序号</td><td style="width: 20%;border-right:1px solid #333333 ;">'+mydata[i].order_id+'</td><td style="border-right:1px solid #333333 ;width: 20%;"cellspacing="20px">订单编号</td><td>'+mydata[i].order_sn+'</td></tr><tr style="border: 1px solid #000000;"><td>商品名称</td><td>'+mydata[i].product_name+'</td><td>下单时间</td><td>'+mydata[i].create_date+'</td></tr><tr style="border: 1px solid #000000;"><td>到货时间</td><td>'+mydata[i].army_receive_date+'</td><td>商品数量</td><td>'+mydata[i].product_number+''+mydata[i].spec_unit+'</td></tr><tr style="border: 1px solid #000000;"><td>商品规格</td><td>'+mydata[i].spec_name+'</td><td>商品单价</td><td>'+mydata[i].price+'元</td></tr><tr style="border: 1px solid #000000;"><td style="border-right:1px solid #333333;">商品总价</td><td colspan="3">'+mydata[i].total_price+'元</td></tr><tr style="border: 1px solid #000000;"><td>订单状态</td><td  colspan="3">'+mydata[i].status_text+'</td></tr><tr style="border: 1px solid #000000;"><td >商品质检状态</td><td  colspan="3">'+mydata[i].quality_check_text+'</td></tr><tr style="border: 1px solid #000000;"><td style="border:1px solid #333333 ;"rowspan="2">备注</td><td style="border:1px solid #333333 ;" colspan="3"rowspan="2"></td></tr><tr></tr></tbody></table>')
							}
							
							allprint();	
						}
					});
			
			
			
		})
	
		function print(elm,orer_id){
			$.ajax({
						type:"post",
						url:"{{url('army/output/print')}}",
						async:true,
						data:{
					    		order_ids:orer_id,
					    		_token:'{{csrf_token()}}'
					    	},
						success:function(resData){
							var data=JSON.parse(resData)
							console.log(data)
							var mydata=data.data;
							$("#myprint").empty();
							for(var i in mydata){
								$("#myprint").append('<table style="width: 800px;" class="printone"><tbody><tr style="border: 1px solid #333333;"><td style="width: 10%;border-right:1px solid #333333 ;">序号</td><td style="width: 20%;border-right:1px solid #333333 ;">'+mydata[i].order_id+'</td><td style="border-right:1px solid #333333 ;width: 20%;"cellspacing="20px">订单编号</td><td>'+mydata[i].order_sn+'</td></tr><tr style="border: 1px solid #000000;"><td>商品名称</td><td>'+mydata[i].product_name+'</td><td>下单时间</td><td>'+mydata[i].create_date+'</td></tr><tr style="border: 1px solid #000000;"><td>到货时间</td><td>'+mydata[i].army_receive_date+'</td><td>商品数量</td><td>'+mydata[i].product_number+''+mydata[i].spec_unit+'</td></tr><tr style="border: 1px solid #000000;"><td>商品规格</td><td>'+mydata[i].spec_name+'</td><td>商品单价</td><td>'+mydata[i].price+'元</td></tr><tr style="border: 1px solid #000000;"><td style="border-right:1px solid #333333;">商品总价</td><td colspan="3">'+mydata[i].total_price+'元</td></tr><tr style="border: 1px solid #000000;"><td>订单状态</td><td  colspan="3">'+mydata[i].status_text+'</td></tr><tr style="border: 1px solid #000000;"><td >商品质检状态</td><td  colspan="3">'+mydata[i].quality_check_text+'</td></tr><tr style="border: 1px solid #000000;"><td style="border:1px solid #333333 ;"rowspan="2">备注</td><td style="border:1px solid #333333 ;" colspan="3"rowspan="2"></td></tr><tr></tr></tbody></table>')
							}
							
							allprint();	
						}
					});
		}	
				
	
	

	
	function allprint(elm){
		$("#myprint").jqprint({
		     debug: false, //如果是true则可以显示iframe查看效果（iframe默认高和宽都很小，可以再源码中调大），默认是false
		     importCSS: true, //true表示引进原来的页面的css，默认是true。（如果是true，先会找$("link[media=print]")，若没有会去找$("link")中的css文件）
		     printContainer: true, //表示如果原来选择的对象必须被纳入打印（注意：设置为false可能会打破你的CSS规则）。
		     operaSupport: true//表示如果插件也必须支持歌opera浏览器，在这种情况下，它提供了建立一个临时的打印选项卡。默认是true
		});
		
	}
	
	
	
	
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