@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/css/user-management.css')}}">
    	 <link rel="stylesheet" href="{{asset('webStatic/css/page.css')}}">
<style>
	
</style>
@endsection
@section('content')
<section>
				<div class="refresh" style="top: 39px;">
			  		<img src="{{asset('webStatic/images/refresh.png')}}" />
			  		<span style="color: #4eb4e5;">点击刷新</span>
			  	</div>

			<table style="margin-top: 50px;">
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
	/*刷新*/
$(".refresh").on("click",function(){
	location.reload();
})
	/*搜索*/
	
	$(".umt-seek").on("click",function(){
		
		var mlystate_val=$(".log_select option:selected").val();
		
		 if($("#log_ser").val()==""){
	    	var cre_time=null;
	    }else{
	    	var cre_time=$("#log_ser").val();
	    }
	    
	var url="{{url('log/list')}}"+"/"+mlystate_val+"/"+cre_time;
	
	location.replace(url);
	})
</script>
@endsection