
<div class="home-header">
    <p><span>当前时间：<b class="lu_year">2017</b>年<b class="lu_month">9</b>月<b class="lu_day">30</b>日</span></p>
    <div class="headerdiv1">
    @foreach($order_status as $key => $item)
       <a href="@if($manage_user['identity'] == '3') {{url('supplier/need/list/'.$key)}} @elseif($manage_user['identity'] == '4') {{url('army/need/list/'.$key)}} @else {{url('platform/order/list/'.'0/'.$key)}}  @endif"><span>{{$key}}：<b>{{$item}}</b>个</span></a>
        <!--<span>已确认订单：<b>1</b>个</span>
        <span>已发货订单：<b>2</b>个</span>
        <span>已到货订单：<b>3</b>个</span>-->
	@endforeach
	
	
	
    </div>
    @if($manage_user['identity'] != '3')
    <a class="cartbutton" href="{{url('cart/list')}}" style="margin-left: 20px;"><img src="{{asset('webStatic/images/cartbutton.jpg')}}"/></a>
    @endif
    <div class="clearfloat" style="clear: both;"></div> 
   <a href="{{url('logout')}}"> <div class="headerdiv2">退出平台</div></a>
</div>
@section('MyJs')
<script type="text/javascript">

</script>

@endsection