@extends('layouts.master')

@section('MyCss')
    <link rel="stylesheet" href="{{asset('webStatic/css/provider.css')}}">
    	<link rel="stylesheet" href="{{asset('webStatic/css/page.css')}}">
    	<link rel="stylesheet" type='text/css' href="{{asset('webStatic/css/print2.css')}}">
    	<link rel="stylesheet" media='print' href="{{asset('webStatic/css/provider.css')}}">	
    	<link rel="stylesheet" type='text/css' media='print' href="{{asset('webStatic/css/print.css')}}">
    
    	<style type="text/css">
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
	.printdingdan,.printAll,.cancelprint{
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
		<div class="refresh" style="top: 155px;">
  			<img src="{{asset('webStatic/images/refresh.png')}}" />
  			<span style="color: #4eb4e5;">点击刷新</span>
  		</div>
			<div style="line-height: 36px;margin-bottom: 20px;">
				
				<div class="pvr-shaixuan">
					<select name="" class="pvr-state">
						<option value="null" >全部</option>
						<option value="待回复"   @if($page_search['status'] == '待回复') selected="selected" @endif>待回复</option>
						<option value="待确认" @if($page_search['status'] == '待确认') selected="selected" @endif>待确认</option>
						<option value="待发货" @if($page_search['status'] == '待发货') selected="selected" @endif>待发货</option>
						<option value="已发货" @if($page_search['status'] == '已发货') selected="selected" @endif>已发货</option>
						<option value="已收货" @if($page_search['status'] == '已收货') selected="selected" @endif>已收货</option>
						<option value="已拒绝" @if($page_search['status'] == '已拒绝') selected="selected" @endif>已拒绝</option>
						<option value="已过期" @if($page_search['status'] == '已过期') selected="selected" @endif>已过期</option>
						

					</select>
					
				 	<input  autocomplete="off" class="pvr-time laydate-icon" name="cre_time" id="cre_time" onClick="laydate({istime: true, format: 'YYYY-MM-DD'})"   name="army_receive_time" id="army_receive_time" value=@if($page_search['create_time']=="null") "" @else "{{$page_search['create_time']}}"@endif placeholder="请选择日期"/>
				</div>
				<a class="pvr-btn">搜索</a>
				
			</div>
			<div>
				<input style="width: 120px; margin-left: 10px;"  autocomplete="off" style="margin-left: 15px;" onClick="laydate({format: 'YYYY-MM-DD',elem:'#start_time'})" class="laydate-icon tre-time start_time"  name="army_receive_time" id="start_time"  placeholder="请选择日期"/>
				<span>-</span>
				<input style="width: 120px;margin-left: 0;"  autocomplete="off" style="margin-left: 15px;" onClick="laydate({format: 'YYYY-MM-DD',elem:'#end_time'})" class="laydate-icon tre-time end_time"  name="army_receive_time" id="end_time"  placeholder="请选择日期"/>
				<a class="daochubiaoge" onclick="biaoge(this)" style="margin-left: 10px;font-size: 14px;">导出表格到本地</a>	
			
			<a class="printdingdan" style="margin-left: 10px;font-size: 14px;" >打印</a>
				<a class="printAll" style="margin-left: 10px;font-size: 14px;background: #0e99dc;">确认打印</a>
				<a class="cancelprint" style="margin-left: 10px;font-size: 14px;">取消打印</a>
			
			</div>
			<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 9%;"><span>序号</span></th>
						<th style="width: 18%;"><span>订单号</span></th>
						<th style="width: 10%;"><span>品名</span></th>
						<th style="width: 7%;"><span>规格</span></th>
						<th style="width: 12%;"><span>到货时间</span></th>
						<th style="width: 14%;"><span style="">数量</span></th>
						<th><span style="">报价</span></th>

						<th style=""><span style="">操作</span></th>
					</tr>
					  @foreach($offer_list as $item)
					<tr>
						<td>{{$item->offer_id}}</td>
						<td>{{$item['order_info']['order_sn']}}</td>
						<td>{{$item['order_info']['product_name']}}</td>
						<td>{{$item['order_info']['spec_name']}}</td>
						<td>{{$item['platform_receive_date']}}</td>
						<td>{{$item['product_number']}}{{$item['order_info']['spec_unit']}}</td>
						<!--<td>@if($item->status_text=="等待通过"){{$item->total_price}}元 @else{{$item->status_text}} @endif </td>-->
						<td>{{$item['status_text']}}</td>
						<td class="blueWord">
							@if($item['status'] == '0')
							<a class="pvr-caozuo" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  @elseif($item['status'] == '1')
							  	<a class="pvr-caozuo" style="" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  	  @elseif($item['status'] == '2')
							  	  <a class="pvr-caozuo" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  	  	<a class="pvr-caozuo" style="color: #4284c5;"onclick="SendGoods(this,{{$item->offer_id}})" >准备配货</a>
							  	  	  @elseif($item['status'] == '3')
							  	  	  <a class="pvr-caozuo" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  	  	  <a class="pvr-caozuo" >已发货</a>
							  	  	  @elseif($item['status'] == '4')
							  	  	  <a class="pvr-caozuo" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  	  	  <a class="pvr-caozuo"style="color: #333;"></a>
							  	  	   @elseif($item['status'] == '-1')
							  	  	   <a class="pvr-caozuo" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  	  	  <a class="pvr-caozuo"style="color: #333;"></a>
							  	  	    @elseif($item['status'] == '10')
							  	  	    <a class="pvr-caozuo" onclick="supplierView(this,{{$item->offer_id}})">查看订单</a>
							  	  	  <a class="pvr-caozuo"style="color: #333;"></a>
							 	@endif
							 	
							 	
							    <a style="margin-left: 5px;" onclick="print(this,'{{$item->offer_id}}')">打印</a>

							 	 <a style="margin-left: 5px;float: right;margin-right: 5px;"><input type="checkbox" class="printcheck" name="" id="" value="" offer_id="{{$item['offer_id']}}"/></a>

						</td>
					</tr>
					
 @endforeach
				</tbody>
			</table>

			 @include('include.inc_pagination',['pagination'=>$offer_list])
	
		
		</section>
			<div id="myprint" ></div>
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
			location.href="{{url('supplier/output/excel')}}"+"/"+start_date+"/"+end_date
			
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
									printData += $(index).attr("offer_id")+",";
								}
						
							
						})
					
						if(printData.length>0){
						 	 allPrintData=printData.substr(0,printData.length-1);
						 }	
				
				
						$.ajax({
						type:"post",
						url:"{{url('supplier/output/print')}}",
						async:true,
						data:{
					    		offer_ids:allPrintData,
					    		_token:'{{csrf_token()}}'
					    	},
						success:function(resData){
							printData="";
							var data=JSON.parse(resData)
							//console.log(data)
							var mydata=data.data;
							$("#myprint").empty();
							for(var i in mydata){
								$("#myprint").append('<table style="width:100%;margin-left: 30px;" class="printone"><tbody><tr style="border: 1px solid #333333;width: 100%;"><td style="width: 10%;border-right:1px solid #333333 ;">序号</td><td style="width: 30%;border-right:1px solid #333333 ;">'+mydata[i].order_id+'</td><td style="border-right:1px solid #333333 ;width: 20%;">订单编号</td><td style="width: 40%;">'+mydata[i].order_info.order_sn+'</td></tr><tr style="border: 1px solid #000000;"><td>商品名称</td><td>'+mydata[i].order_info.product_name+'</td><td>规定到货时间</td><td>'+mydata[i].platform_receive_date+'</td></tr><tr style="border: 1px solid #000000;"><td>商品单价</td><td>'+mydata[i].price+'元</td><td>商品数量</td><td>'+mydata[i].product_number+''+mydata[i].order_info.spec_unit+'</td></tr><tr style="border: 1px solid #000000;"><td>商品规格</td><td>'+mydata[i].order_info.spec_name+'</td><td>商品总价</td><td>'+mydata[i].total_price+'元</td></tr><tr style="border: 1px solid #000000;"><td>订单状态</td><td  colspan="3">'+mydata[i].status_text+'</td></tr><tr style="border: 1px solid #000000;"><td style="border:1px solid #333333 ;"rowspan="2">备注</td><td style="border:1px solid #333333 ;" colspan="3"rowspan="2"></td></tr><tr></tr></tbody></table>')
							}
							
							allprint();	
						}
					});
			
			
			
		})
	
		function print(elm,offer_id){
			$.ajax({
						type:"post",
						url:"{{url('supplier/output/print')}}",
						async:true,
						data:{
					    		offer_ids:offer_id,
					    		_token:'{{csrf_token()}}'
					    	},
						success:function(resData){
							var data=JSON.parse(resData)
							//console.log(data)
							var mydata=data.data;
							$("#myprint").empty();
							for(var i in mydata){
								$("#myprint").append('<table style="width:100%;margin-left: 30px;" class="printone"><tbody><tr style="border: 1px solid #333333;width: 100%;"><td style="width: 10%;border-right:1px solid #333333 ;">序号</td><td style="width: 30%;border-right:1px solid #333333 ;">'+mydata[i].order_id+'</td><td style="border-right:1px solid #333333 ;width: 20%;">订单编号</td><td style="width: 40%;">'+mydata[i].order_info.order_sn+'</td></tr><tr style="border: 1px solid #000000;"><td>商品名称</td><td>'+mydata[i].order_info.product_name+'</td><td>规定到货时间</td><td>'+mydata[i].platform_receive_date+'</td></tr><tr style="border: 1px solid #000000;"><td>商品单价</td><td>'+mydata[i].price+'元</td><td>商品数量</td><td>'+mydata[i].product_number+''+mydata[i].order_info.spec_unit+'</td></tr><tr style="border: 1px solid #000000;"><td>商品规格</td><td>'+mydata[i].order_info.spec_name+'</td><td>商品总价</td><td>'+mydata[i].total_price+'元</td></tr><tr style="border: 1px solid #000000;"><td>订单状态</td><td  colspan="3">'+mydata[i].status_text+'</td></tr><tr style="border: 1px solid #000000;"><td style="border:1px solid #333333 ;"rowspan="2">备注</td><td style="border:1px solid #333333 ;" colspan="3"rowspan="2"></td></tr><tr></tr></tbody></table>')
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
	
	
	
	
	
	
	
	
	
	
	
	
	
	
  		!function(){

			laydate.skin('molv');//切换皮肤，请查看skins下面皮肤库
		
			laydate({elem: '#army_receive_time'});//绑定元素
		
		}();
	/*刷新*/
$(".refresh").on("click",function(){
	location.reload();
})	
		
		
		
  	function supplierView(elm,offer_id){
  		  layer.open({
		      type: 2,
		      title: false,
		      maxmin: false,
		      shadeClose: true, //点击遮罩关闭层
		      area : ['660px' , '700px'],
		      fixed :false,
		      content: '{{url('supplier/offer/view')}}'+"/"+offer_id
		    });
  	}
  	
  	
  	  function SendGoods(elm,offer_id){
		  	if (confirm("确认配货吗？")){
		  		$.ajax({
		  			type:'post',
		  			data:{
		  				offer_id:offer_id,
	  					_token:'{{csrf_token()}}'
		  			},
		  			url:'{{url('supplier/send/product')}}',
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
  
  
  	
  	//搜索
  	
  	  $(".pvr-btn").on("click",function(){
    	
    	 if($(".pvr-time").val()==""){
	    	var time=null;
	    }else{
	    	var time=$(".pvr-time").val();
	    }
    	
	   var staus_val = $('.pvr-state option:selected').val();
    
    	var url="{{url('supplier/need/list')}}"+"/"+staus_val+"/"+time;
    	
    	location.replace(url);
    });
  </script>
  @endsection
