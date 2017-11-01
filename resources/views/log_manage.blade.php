@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/css/user-management.css')}}">
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
			

			<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 9%;"><span>序号</span></th>
						<th style="width: 40%;"><span>操作时间</span></th>
						<th><span style="width: 14%;">操作</span></th>
					</tr>
					  @foreach($log_list as $item)
					<tr>
						<td>{{$item->log_id}}</td>
						<td>{{$item->create_time}}</td>
						<td>{{$item->log_desc}}</td>
						
					</tr>
					  @endforeach
				</tbody>
			</table>
  @include('include.inc_pagination',['pagination'=>$log_list])
		</section>
@endsection
@section('MyJs')
<script>
	/*搜索*/
	
	$(".umt-seek").on("click",function(){
		
		var mlystate_val=$(".log_select option:selected").val();
		
		 if($("#log_ser").val()==""){
	    	var cre_time=null;
	    }else{
	    	var cre_time=$("#log_ser").val();
	    }
	    
	var url="{{url('log/list')}}"+"/"+mlystate_val+"/"+cre_time;
	console.log(url)
	location.replace(url);
	})
</script>
@endsection