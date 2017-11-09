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
			<div>

				<div class="czi-filtrate">
					<span style="margin-left: 3%;">账户分类</span>
					<select name="" class="log_select">
					<option value="0">全部</option>
                     <option value="1" @if($page_search['identity'] == '1') selected="selected" @endif >超级管理员</option>
                     <option value="2"@if($page_search['identity'] == '2') selected="selected" @endif>平台运营员</option>
                     <option value="3" @if($page_search['identity'] == '3') selected="selected" @endif>供货商</option>
                     <option value="4" @if($page_search['identity'] == '4') selected="selected" @endif>军方</option>
					</select>

					<span>账号</span>
					<input type="text" name="log_ser" id="log_ser" value=@if($page_search['nick_name'] != 'null') "{{$page_search['nick_name']}}" @else "" @endif />

				</div>
				<a class="umt-seek">搜索</a>
			</div>

			<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 9%;"><span>序号</span></th>
						<th style="width: 11%;"><span>姓名</span></th>
						<th style="width: 17%;"><span>手机</span></th>
						<th style="width: 14%;"><span>操作时间</span></th>
						<th style="width: 16%;"><span style="">账户分类</span></th>
						<th><span style="">操作</span></th>
					</tr>
					  @foreach($log_list as $item)
					<tr>
						<td>{{$item->log_id}}</td>
						<td>{{$item->nick_name}}</td>
						<td>{{$item->phone}}</td>
						<td>{{$item->create_time}}</td>
						<td>{{$item->identity_text}}</td>
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