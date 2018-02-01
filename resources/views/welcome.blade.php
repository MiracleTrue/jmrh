@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    {{--<link rel="stylesheet" type="text/css" href="css/iframe.css" />--}}
    <link rel="stylesheet" type="text/css" href="{{asset('webStatic/css/iframe.css')}}" />
 	<style type="text/css">
 		.fnumber{
 			font-weight: bold;
 			color: #FFFFFF;
 			height:50px;
 			width:50px;
 			display:inline-block;
 			line-height:50px;
 			text-align:center;
 			font-size:30px;
 			border-radius:50%;
 			margin-right: 5px;
 		}
 	</style>
 	
@endsection
@section('content')
    <section style="position: relative;">
        <div class="floor" style="position: fixed;right: 6%;top: 14%;width: 6%;height: 80%;">
            {{--<a href="#floor1"><img src="img/F1.png" alt="" /></a>--}}
            {{--<a href="#floor2"><img src="img/F2.png" alt="" /></a>--}}
            {{--<a href="#floor3"><img src="img/F3.png" alt="" /></a>--}}
            {{--<a href="#floor4"><img src="img/F4.png" alt="" /></a>--}}
           {{--  <a href="#floor1"><img src="{{asset('webStatic/images/F1.png')}}" alt="" /></a>--}}
           @foreach($product_list as $key => $items)
           <a style="text-decoration:none;margin-top:6px" ><span class="floor_span" style="background:'+bcolor+'">{{$items['category_name']}}</span></a>
           @endforeach
        </div>
 		@foreach($product_list as $key => $item)
        <div class="f1" id="floor{{$key}}">
            <div class="floor_div">
                <p style="float: left;"><span style="background:'+bcolor+'" class="fnumber"></span><span style="font-size: 26px;font-weight: bolder;">{{$item['category_name']}}</span>
              {{--  <!-- <ul class="head">
                	@foreach($item['labels'] as $label)
                    <li>{{$label}}</li>
                    @endforeach
                </ul>-->
                --}}
                </p>
            </div>

            <ul class="goods-list">
            
            @foreach($item['products'] as $product)
                <li onclick="ProductShow(this,'{{$product['product_id']}}')" >
                    <p>{{$product['product_name']}}</p>
                    <img class="proudut_img" src="{{\App\Models\MyFile::makeUrl($product['product_thumb'])}}" onerror="this.src='{{asset('webStatic/images/noimg.png')}}'"/>
                </li>
               @endforeach  
            </ul>

        </div>
       
        @endforeach 
        
    </section>
@endsection


@section('MyJs')
    {{--<script type="text/javascript" src="{{URL::asset('/css/***.js')}}" ></script>--}}
    {{--<script type="text/javascript">--}}
        {{--/* code ...*/--}}
    {{--</script>--}}
    
    <script>
    	
    	
    	$(function(){
    	
    	var spanColor=['#FF433A','#08ad00','#ffea95','#fe8d01'];	
	 
	  for(var i=0;i<$(".floor a").length;i++){
	  		var bcolor=spanColor[i%4];
	  	$(".floor a").eq(i).attr("href","#floor"+i);
	  	$(".floor a span").eq(i).css("background",bcolor);
	  	$(".fnumber").eq(i).css("background",bcolor);
	  }
    	
	    	
	    /*控制图片大小*/
	   	var imgWidth=$(".proudut_img").width();
	   	$(".proudut_img").height(imgWidth);
	   	
	   	$(".fnumber").each(function(i,index){
    		$(index).text("F"+Number(i+1));
    	})
	   	
	    		
    	})
    	 $(".floor a").css("width","100%");
    	
    	function ProductShow(elm,product_id){
	    	var indexlayer=layer.open({
	            type: 2,
	            title: false,
	            maxmin: false,
	             fixed :false,
	            shadeClose: true, //点击遮罩关闭层
	            closeBtn: 0,
	             isOutAnim: false,
	             anim: -1,
	             area: ['100%', '100%'],
	            content: "{{url('product/show')}}"+"/"+product_id,
	          
	           
	        });
	    		layer.full(indexlayer);
    		
            
           
    	/*	var url="{{url('product/show')}}"+"/"+product_id
    		
    		location.replace(url);*/
    	}
    	
    	
    	
    	
    	
    	$(function(){
    		var floora= $(".floor a").width();
    	/*console.log(floora);*/
    	$(".floor a").height(floora/2);
    		$(".floor a span").height(floora/2);
    	/*	console.log(floora+"px")*/
    		$(".floor a span").css("line-height",floora/2+"px");
    		$(".floor a span").css("font-size",floora/4.5+"px")
    	})
    	
    
    </script>
    
@endsection