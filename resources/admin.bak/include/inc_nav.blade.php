<nav class="breadcrumb">
    @if(!empty($nav_position))
        <a href="{{action('Admin\IndexController@Welcome')}}"><i class="Hui-iconfont">&#xe67f;</i> {{__('common.homePage')}}</a>
        @foreach($nav_position as $value)
            <a href="{{$value['url']}}" class="c-gray en">&gt;&nbsp;{{$value['name']}}</a>
        @endforeach
    @endif
    <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="{{__('common.refresh')}}" ><i class="Hui-iconfont">&#xe68f;</i></a>
</nav>