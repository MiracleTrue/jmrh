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
        <div>
            <a href="#" class="umt-add"></a>
            <div class="umt-filtrate">
                <span style="margin-left: 3%;">账户分类</span>
                <select class="id_value" name="identity">
                    <option value="0">全部</option>
                     <option value="1" @if($page_search['identity'] == '1') selected="selected" @endif >超级管理员</option>
                      <option value="2"@if($page_search['identity'] == '2') selected="selected" @endif>平台运营员</option>
                       <option value="3" @if($page_search['identity'] == '3') selected="selected" @endif>供货商</option>
                        <option value="4" @if($page_search['identity'] == '4') selected="selected" @endif>军方</option>
                </select>
					<span>
						状态
					</span>
                <select name="is_disable" class="is_disable_val">
                    <option value="2">全部</option>
                     <option value="1" @if($page_search['is_disable'] == '1') selected="selected" @endif>禁用</option>
                      <option value="0" @if($page_search['is_disable'] == '0') selected="selected" @endif>正常</option>
                </select>
                <span>姓名</span>
                <input type="text" name="nick_name" id="nick_name" value=@if($page_search['nick_name'] != 'null') "{{$page_search['nick_name']}}" @else "" @endif />
					<span style="margin-left: 3%;">
						手机
					</span>
                <input type="text" name="phone" id="phone" value=@if($page_search['phone'] != 'null') "{{$page_search['phone']}}" @else "" @endif />
            </div>
            <a class="umt-seek">搜索</a>
        </div>

        <table>
            <tbody>
            <tr class="tr1">
                <th style="width: 9%;"><span>序号</span></th>
                <th style="width: 11%;"><span>姓名</span></th>
                <th style="width: 17%;"><span>用户名</span></th>
                <th style="width: 15%;"><span>手机</span></th>
                <th style="width: 13%;"><span style="">账户分类</span></th>
                <th style="width: 14%;"><span style="" class="staus">状态</span></th>
                <th><span style="">操作</span></th>
            </tr>
            @foreach($user_list as $item)
            
            <tr>
                <td>{{$item['user_id']}}</td>
                <td>{{$item['nick_name']}}</td>
                <td>{{$item['user_name']}}</td>
                <td>{{$item['phone']}}</td>
                <td>{{$item['identity_text']}}</td>
                @if($item['is_disable'] == '0')
                                <td>正常</td>
                @else
                                <td>冻结</td>               
                @endif
                <td class="blueWord">
                	@if($item['is_disable'] == '0')
                               <a class="mly-caozuo" onclick="stopuser(this,'{{$item['user_id']}}')">禁用</a>
                @else
                                <a class="mly-caozuo" onclick="startuser(this,'{{$item['user_id']}}')">启用</a>            
                @endif
                    <a style="margin-left: 5%;"onclick="UserEdit('{{$item['user_id']}}')">修改</a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @include('include.inc_pagination',['pagination'=>$user_list])

    </section>
@endsection


@section('MyJs')
    <script>
	var identity_value=0;
	var is_disable_val=2;
    	
    $(".umt-seek").on("click",function(){
    	
    	 if($("#nick_name").val()==""){
	    	var name=null;
	    }else{
	    	var name=$("#nick_name").val();
	    }
    	 var phone=$("#phone").val();
	    if($("#phone").val()==""){
	    	phone=null;
	    }
	     identity_value = $('.id_value option:selected').val();
    	 is_disable_val=$(".is_disable_val option:selected").val();	
    	var url="{{url('user/list')}}"+"/"+identity_value+"/"+is_disable_val+"/"+name+"/"+phone;
    	
    	location.replace(url);
    });
   
    function startuser(elm,user_id)
    {
    	
    		$.ajax({
    		url:'{{url("user/enable")}}',
    		data:{
    			user_id:user_id
    		},
    		success:function(res){
    			
    			var resData=JSON.parse(res);
    			console.log(resData);
				
    			if(!resData.code){
    				$(elm).parent().parent().children().eq(5).text("正常")
    				$(elm).text("禁用");
    			}
    		}
    		
    	});
    }
    function stopuser(elm,user_id)
    {
    
    	$.ajax({
    		url:'{{url("user/disable")}}',
    		data:{
    			user_id:user_id
    		},
    		success:function(res){
    			
    			var resData=JSON.parse(res);
    			console.log(resData);
    				
    			if(!resData.code){
    				$(elm).parent().parent().children().eq(5).text("冻结")
    				$(elm).text("启用");
    			}
    		}
    		
    	});
    	
    }
    
    function UserEdit(user_id){
    	$.ajax({
    		url:'{{url("user/edit")}}',
    		data:{
    			user_id:user_id
    		},
    		success:function(res){
    			
    			var resData=JSON.parse(res);
    			console.log(resData);
    				
    		}
    		
    	});
    }
    
    
     ! function() {

            //页面一打开就执行，放入ready是为了layer所需配件（css、扩展模块）加载完毕
            layer.ready(function() {
                $('.umt-add').on('click', function() {
                    layer.open({
                        type: 2,
                        title: false,
                        maxmin: false,
                        shadeClose: true, //点击遮罩关闭层
                        area: ['600px', '730px'],
                        content: '{{url('user/view')}}'
                    });
                });
            });

        }();
    </script>
@endsection