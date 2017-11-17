@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    {{--<link rel="stylesheet" type="text/css" href="css/iframe.css" />--}}
    <link rel="stylesheet" type="text/css" href="{{asset('webStatic/css/iframe.css')}}" />
 	
@endsection
@section('content')
    <section style="position: relative;">
        <div class="floor" style="position: fixed;right: 6%;top: 35%;width: 5%;">
            {{--<a href="#floor1"><img src="img/F1.png" alt="" /></a>--}}
            {{--<a href="#floor2"><img src="img/F2.png" alt="" /></a>--}}
            {{--<a href="#floor3"><img src="img/F3.png" alt="" /></a>--}}
            {{--<a href="#floor4"><img src="img/F4.png" alt="" /></a>--}}
           {{--  <a href="#floor1"><img src="{{asset('webStatic/images/F1.png')}}" alt="" /></a>--}}
           
        </div>
 		@foreach($product_list as $key => $item)
        <div class="f1" id="floor{{$key}}">
            <div class="floor_div">
                <p style="float: left;"><span style="font-size: 26px;font-weight: bolder;">{{$item['category_name']}}</span>
                <ul class="head">
                	@foreach($item['labels'] as $label)
                    <li>{{$label}}</li>
                    @endforeach
                </ul>
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
  
    	
	    	for(var i=0;i<$(".f1").length;i++){
	    		var bcolor=spanColor[i%4];
	    		var fNum=i+1;
	    		$(".floor").append('<a href="#floor'+i+'" style="text-decoration:none;margin-top:6px" ><span class="floor_span" style="background:'+bcolor+'">F'+fNum+'</span></a>')
	    		//console.log(bcolor)
	    		$(".floor_div").eq(i).prepend('<span class="floor_spansmall" style="background:'+bcolor+'">F'+fNum+'</span>')
	    		
	    	}
	    /*控制图片大小*/
	   	var imgWidth=$(".proudut_img").width();
	   	$(".proudut_img").height(imgWidth);
	    		
    	})
    
    	function ProductShow(elm,product_id){
    		var url="{{url('product/show')}}"+"/"+product_id
    		console.log(url)
    		location.replace(url)
    	}
    </script>
    
@endsection