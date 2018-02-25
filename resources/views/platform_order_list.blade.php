@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
   
    <link rel="stylesheet" href="{{asset('webStatic/css/terrace.css')}}">
    	<link rel="stylesheet" href="{{asset('webStatic/css/page.css')}}">
    	<link rel="stylesheet" media='print' href="{{asset('webStatic/css/military.css')}}">
  		<link rel="stylesheet" type='text/css' media='print' href="{{asset('webStatic/css/print.css')}}">
  		<link rel="stylesheet" type='text/css' href="{{asset('webStatic/css/print2.css')}}">
<style>
	

.plat_shanixuan{
	width: 85px;height: 36px;line-height: 36px;color: #fff;font-size: 16px;text-indent: 18px;background-color: #fe8d01;float: left;
	border-radius:20px ;
}
.plat_stauschoose{
	float: left;
	background: #DDDDDD;
	border-radius:20px ;
	margin-left: -28px;
	padding-right: 20px;
	
}
.platshaixuan{
	width: auto;
}
.daochubiaoge{
	width: 160px;
	height: 38px;
	text-align: center;
	line-height: 38px;
	background: #feb501;
	border-radius: 20px;
	display: inline-block;
	color: #FFFFFF;
}
.printdingdan,.printAll,.cancelprint,.tongji{
	width: 90px;
	height: 38px;
	text-align: center;
	line-height: 38px;
	background: #F3570D;
	border-radius: 20px;
	color: #FFFFFF;	
	display: inline-block;
}
</style>
@endsection
@section('content')

      <section>
      		<div class="refresh" style="top: 159px;">
		  		<img src="{{asset('webStatic/images/refresh.png')}}" />
		  		  		<span style="color: #4eb4e5;">点击刷新</span>

		  	</div>
			<div style="line-height: 36px;margin-bottom: 30px;">
				<a href="#" class="tre-tianjia"></a>
				<div class="tre-shaixuan platshaixuan" style="background: none;">
					<div class="plat_shanixuan" >筛选</div>
					<div class="plat_stauschoose" >
						<span style="margin-left: 24px;font-size: 16px;">类型</span>
					<select name="" class="tre-state palt type_val" style="margin-left: 15px;">
					 <option value="0">全部</option>
                      <option value="2" @if($page_search['type'] == '2') selected="selected" @endif>平台</option>
                        <option value="1" @if($page_search['type'] == '1') selected="selected" @endif>军方</option>
						
					</select>
					<span style="margin-left: 24px;font-size: 16px;">状态</span>
					<select name="" class="tre-state staus_val" style="margin-left: 15px;">
						<option value="null"  @if($page_search['status'] == 'null') selected="selected" @endif>全部</option>
						<option value="待分配"  @if($page_search['status'] == '待分配') selected="selected" @endif>待分配</option>
						<option value="已分配"  @if($page_search['status'] == '已分配') selected="selected" @endif>已分配</option>
						<option value="库存供应"  @if($page_search['status'] == '已发货') selected="selected" @endif>已发货</option>
						<option value="交易成功"  @if($page_search['status'] == '交易成功') selected="selected" @endif>交易成功</option>
					</select>
					<span style="margin-left: 24px;font-size: 16px;">分配时间</span>
					<input  autocomplete="off" style="margin-left: 15px;"value= @if($page_search['create_time']=="null") "" @else "{{$page_search['create_time']}}"@endif onClick="laydate({istime: true, format: 'YYYY-MM-DD' })" class="laydate-icon tre-time"  name="army_receive_time" id="army_receive_time"  placeholder="请选择日期"/>
					
					
					
					</div>
				</div>
				<a class="tre-btn">搜索</a>
				
			</div>
			<div>
				<input style="width: 120px; margin-left: 10px;"  autocomplete="off" style="margin-left: 15px;" onClick="laydate({format: 'YYYY-MM-DD',elem:'#start_time' })" class="laydate-icon tre-time start_time"  name="army_receive_time" id="start_time"  placeholder="请选择日期"/>
				<span>-</span>
				<input style="width: 120px;margin-left: 0;"  autocomplete="off" style="margin-left: 15px;" onClick="laydate({format: 'YYYY-MM-DD',elem:'#end_time' })" class="laydate-icon tre-time end_time"  name="army_receive_time" id="end_time"  placeholder="请选择日期"/>
				<a class="daochubiaoge" onclick="biaoge(this)" style="margin-left: 10px;font-size: 14px;">导出表格到本地</a>	

				<a class="tongji" onclick="tongji(this)" style="margin-left: 10px;font-size: 14px;">统计</a>						
				
				<a class="printdingdan" style="margin-left: 10px;font-size: 14px;" >打印</a>
				<a class="printAll" style="margin-left: 10px;font-size: 14px;background: #0e99dc;">确认打印</a>
				<a class="cancelprint" style="margin-left: 10px;font-size: 14px;">取消打印</a>
				</div>
				<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 6%;"><span>序号</span></th>		
						<th style="width: 15%;"><span>订单号</span></th>
						<th style="width: 7%;"><span>品名</span></th>
						<th style="width: 7%;"><span>规格</span></th>
						<th style="width: 12%;"><span>到货时间</span></th>
						<th style="width: 12%;"><span style="">数量</span></th>
						<th style="width: 12%;"><span style="">负责人</span></th>
						<th style="width: 12%;"><span style="">状态</span></th>
						<th style=""><span style="">操作</span></th>
					</tr>
					  @foreach($order_list as $item)
					<tr>
						<td>{{$item->order_id}}</td>
						<td>{{$item->order_sn}}</td>
						<td>{{$item->product_name}}</td>
						<td>{{$item->spec_name}}</td>
						<td>{{$item->army_receive_time}}</td>
						<td>{{$item->product_number}}{{$item->spec_unit}}</td>
						
						@if($item->manage_user)
							<td>{{$item->manage_user['nick_name']}}</td>
						@else
							<td></td>
						@endif
					
					
						<td>{{$item->status_text}}</td>
						<td class="blueWord">
							@if($item['status'] == '0')
							<a class="tre-caozuo platfenpei" onclick="fenpei(this,'{{$item->order_id}}')">分配</a>
							@if($item['type']=='1')
								<a style="margin-left: 5%;" onclick="InventorySupply(this,'{{$item->order_id}}')">库存供应</a>
							@endif
							@elseif($item['status'] == '1')
							<a class="tre-caozuo platfenpei" onclick="fenpei2(this,'{{$item->order_id}}')">重新分配</a>
						 	 @elseif($item['status'] == '100')
								<a class="tre-caozuo" onclick="chakanbaojia(this,'{{$item->order_id}}')" >订单确认</a>
							 @elseif($item['status'] == '110')
								<a class="tre-caozuo" onclick="confirmshouhuo(this,'{{$item->order_id}}')" >确认收货</a>
						   @elseif($item['status'] == '120' || $item['status'] == '200' )
						   			<a class="tre-caozuo" onclick="ConfirmReceive(this,'{{$item->order_id}}')">发货到军方</a>
						   	@endif 	
						 
						 	<a onclick="xiangxiinfo(this,'{{$item->order_id}}')">详细信息</a>
						   	<a style="margin-left: 5px;" onclick="print(this,'{{$item['order_id']}}')">打印</a>
                		<a style="margin-left: 5px;float: right;margin-right: 5px;"><input type="checkbox" class="printcheck" name="" id="" value="" order_id="{{$item['order_id']}}"/></a>

						</td>
					</tr>
					  @endforeach

				</tbody>
			</table>
			 @include('include.inc_pagination',['pagination'=>$order_list])
		</section>
	<div id="myprint">
			
		</div>
@endsection
 
@section('MyJs')
  <script type="text/javascript" src="{{asset('/webStatic/library/jquery-calendar/js/laydate.js')}}"></script>
<script src="http://www.jq22.com/jquery/jquery-migrate-1.2.1.min.js"></script>
 <script type="text/javascript" src="{{asset('/webStatic/library/jquery.jqprint/jquery.jqprint-0.3.js')}}"></script>
<script>
	/*确认收货页面*/
	function confirmshouhuo(elm,order_id){
		 layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		       fixed :false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['50%' , '50%'],
		      content: '{{url('platform/confirm/receive/view')}}'+"/"+order_id
		    });
	}
	
	
	function biaoge(){
		var start_date=$(".start_time").val();
		var end_date=$(".end_time").val();
		if(start_date=="" || end_date==""){
			alert("时间选择不能为空")
			
		}else{
			location.href="{{url('platform/output/excel')}}"+"/"+start_date+"/"+end_date
			
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
						url:"{{url('platform/output/print')}}",
						async:true,
						data:{
					    		order_ids:allPrintData,
					    		_token:'{{csrf_token()}}'
					    	},
						success:function(resData){
							printData="";
							var data=JSON.parse(resData)
							//console.log(data)
							var mydata=data.data;
							$("#myprint").empty();
							for(var i in mydata){
								$("#myprint").append('<table style="width: 800px;" class="printone"><tbody><tr style="border: 1px solid #333333;"><td style="width: 10%;border-right:1px solid #333333 ;">序号</td><td style="width: 20%;border-right:1px solid #333333 ;">'+mydata[i].order_id+'</td><td style="border-right:1px solid #333333 ;width: 20%;" cellspacing="20px">订单编号</td><td>'+mydata[i].order_sn+'</td></tr><tr style="border: 1px solid #000000;"><td>商品名称</td><td>'+mydata[i].product_name+'</td><td>商品数量</td><td>'+mydata[i].product_number+'</td></tr><tr style="border: 1px solid #000000;"><td>军方下单时间</td><td>'+mydata[i].create_date+'</td><td>军方规定到货时间</td><td>'+mydata[i].army_receive_date+'</td></tr><tr style="border: 1px solid #000000;"><td>商品规格</td><td>'+mydata[i].spec_name+'</td><td>商品单价</td><td>'+mydata[i].price+'元</td></tr><tr style="border: 1px solid #000000;"><td>商品总价</td><td>'+mydata[i].total_price+'元</td><td>军方联系人</td><td>'+mydata[i].army_contact_person+'</td></tr><tr style="border: 1px solid #000000;"><td>联系电话</td><td colspan="3">'+mydata[i].army_contact_tel+'</td></tr><tr style="border: 1px solid #000000;"><td>订单状态</td><td colspan="3">'+mydata[i].status_text+'</td></tr><tr style="border: 1px solid #000000;"><td>商品质检状态</td><td colspan="3">'+mydata[i].quality_check_text+'</td></tr><tr style="border: 1px solid #000000;"><td style="border:1px solid #333333 ;" rowspan="2">备注</td><td style="border:1px solid #333333 ;" colspan="3" rowspan="2"></td></tr><tr></tr></tbody></table>')
							}
							
							allprint();	
						}
					});
			
			
			
		})
	
		function print(elm,orer_id){
			$.ajax({
						type:"post",
						url:"{{url('platform/output/print')}}",
						async:true,
						data:{
					    		order_ids:orer_id,
					    		_token:'{{csrf_token()}}'
					    	},
						success:function(resData){
							var data=JSON.parse(resData)
						//	console.log(data)
							var mydata=data.data;
							$("#myprint").empty();
							for(var i in mydata){
								$("#myprint").append('<table style="width: 800px;" class="printone"><tbody><tr style="border: 1px solid #333333;"><td style="width: 10%;border-right:1px solid #333333 ;">序号</td><td style="width: 20%;border-right:1px solid #333333 ;">'+mydata[i].order_id+'</td><td style="border-right:1px solid #333333 ;width: 20%;" cellspacing="20px">订单编号</td><td>'+mydata[i].order_sn+'</td></tr><tr style="border: 1px solid #000000;"><td>商品名称</td><td>'+mydata[i].product_name+'</td><td>商品数量</td><td>'+mydata[i].product_number+'</td></tr><tr style="border: 1px solid #000000;"><td>军方下单时间</td><td>'+mydata[i].create_date+'</td><td>军方规定到货时间</td><td>'+mydata[i].army_receive_date+'</td></tr><tr style="border: 1px solid #000000;"><td>商品规格</td><td>'+mydata[i].spec_name+'</td><td>商品单价</td><td>'+mydata[i].price+'元</td></tr><tr style="border: 1px solid #000000;"><td>商品总价</td><td>'+mydata[i].total_price+'元</td><td>军方联系人</td><td>'+mydata[i].army_contact_person+'</td></tr><tr style="border: 1px solid #000000;"><td>联系电话</td><td colspan="3">'+mydata[i].army_contact_tel+'</td></tr><tr style="border: 1px solid #000000;"><td>订单状态</td><td colspan="3">'+mydata[i].status_text+'</td></tr><tr style="border: 1px solid #000000;"><td>商品质检状态</td><td colspan="3">'+mydata[i].quality_check_text+'</td></tr><tr style="border: 1px solid #000000;"><td style="border:1px solid #333333 ;" rowspan="2">备注</td><td style="border:1px solid #333333 ;" colspan="3" rowspan="2"></td></tr><tr></tr></tbody></table>')
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
	
	
	
	
	
	
	
	
	
	
	
	/*统计*/
	function tongji(){
		var start_date=$(".start_time").val();
		var end_date=$(".end_time").val();
		
		/* layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		       fixed :false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['80%' , '70%'],
		      content: '{{url('platform/statistics/list')}}'+"/"+start_date+"/"+end_date
		    });*/
		   var url="{{url('platform/statistics/list')}}"+"/"+start_date+"/"+end_date
		location.replace(url);
	}
	
	
		function xiangxiinfo(elm,order_id){
			 layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		       fixed :false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['900px' , '500px'],
		      content: '{{url('platform/order/detail/view')}}'+"/"+order_id
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
	//搜索
	
	  $(".tre-btn").on("click",function(){
    	
    	 if($("#army_receive_time").val()==""){
	    	var time=null;
	    }else{
	    	var time=$("#army_receive_time").val();
	    }
    	
	   var staus_val = $('.type_val option:selected').val();
    	var status_val=$(".staus_val option:selected").val();	
    	var url="{{url('platform/order/list')}}"+"/"+staus_val+"/"+status_val+"/"+time;
    	
    	location.replace(url);
    });
	
	



	 $('.tre-tianjia').on('click', function(){
		    layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		       fixed :false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['965px' , '600px'],
		      content: '{{url('platform/need/view/release')}}'
		    });
		  });
		  //分配
		  
		  function fenpei(elm,order_id){
		  
		    layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		       fixed :false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['940px' , '81%'],
		      content: '{{url('platform/allocation/view')}}'+'/'+order_id
		    });
		  }
		  
		  function fenpei2(elm,order_id){
		  	 layer.open({
			      type: 2,
			      title: false,
			      maxmin: false,
			       fixed :false,
			      shadeClose: true, //点击遮罩关闭层
			      area : ['900px' , '600px'],
			      content: '{{url('platform/re/allocation/view')}}'+'/'+order_id
		    });
		  }
		  
		  
		    function chakanbaojia(elm,order_id){
			    layer.open({
			      type: 2,
			      title: false,
			      maxmin: false,
			       fixed :false,
			      shadeClose: true, //点击遮罩关闭层
			      area : ['900px' , '500px'],
			      content: '{{url('platform/order/confirm/view')}}'+'/'+order_id
			    });
		  }
		  
		  function sendArmy(elm,order_id){
		  	if (confirm("确认发货到军方吗？")){
		  		$.ajax({
		  			type:'post',
		  			data:{
		  				order_id:order_id,
	  					_token:'{{csrf_token()}}'
		  			},
		  			url:'{{url('platform/send/army')}}',
		  			async:true,
		  			success: function (resData) {
		  				var res=JSON.parse(resData)
		  				
		            if(res.code==0){
		             	   layer.msg(res.messages, {icon: 1, time: 1000},function(){
		             	  
		             	   	location.reload();
		             	   });
		             	
			        var index=parent.layer.getFrameIndex(window.name);
					setTimeout(function(){
						parent.layer.close(index);
		             	layer.closeAll('')
					},1200)
						
		             }else{
		             	   layer.msg(res.messages, {icon: 2, time: 1000});
		             }
		            }
		  		});
		  	}
		 	
		  }
		  
		   function ConfirmReceive(elm,order_id){
		  	if (confirm("确认发货到军方吗？")){
		  		$.ajax({
		  			type:'post',
		  			data:{
		  				order_id:order_id,
	  					_token:'{{csrf_token()}}'
		  			},
		  			url:'{{url('platform/send/army')}}',
		  			async:true,
		  			success: function (resData) {
		  				var res=JSON.parse(resData)
		            if(res.code==0){
		             	   layer.msg(res.messages, {icon: 1, time: 1000},function(){
		             	   	location.reload();
		             	   });
		             	
			        var index=parent.layer.getFrameIndex(window.name);
					setTimeout(function(){
						parent.layer.close(index);
		             	layer.closeAll('')
					},1200)
						
		             }else{
		             	   layer.msg(res.messages, {icon: 2, time: 1000});
		             }
		            }
		  		});
		  	}
		 	
		  }
		  
		  
		     function InventorySupply(elm,order_id){
		  	if (confirm("确认库存供应吗？")){
		  		$.ajax({
		  			type:'post',
		  			data:{
		  				order_id:order_id,
	  					_token:'{{csrf_token()}}'
		  			},
		  			url:'{{url('platform/inventory/supply')}}',
		  			async:true,
		  			
		  			success: function (resData) {
		  				var res=JSON.parse(resData)
		            if(res.code==0){
		             	   layer.msg(res.messages, {icon: 1, time: 1000},function(){
		             	   	
		             	   	location.reload();
		             	   });
		             	
			        var index=parent.layer.getFrameIndex(window.name);
					setTimeout(function(){
						parent.layer.close(index);
		             	layer.closeAll('')
					},1200)
						
		             }else{
		             	   layer.msg(res.messages, {icon: 2, time: 1000});
		             }
		            }
		  		});
		  	}
		 	
		  }
		  
		  

	
</script>
@endsection

