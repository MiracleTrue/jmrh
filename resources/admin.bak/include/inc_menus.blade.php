<aside class="Hui-aside">
    <div class="menu_dropdown bk_2">

        @foreach($menu_list as $value)
            <dl id="{{$value['menu_id']}}">
                <dt>
                    <i style="font-size: 18px; color: #333;" class="Hui-iconfont">{{$value['menu_icon']}}</i>&nbsp;{{\App\Models\CommonModel::languageFormat($value['menu_name'] , $value['menu_en_name'])}}<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                    @if($value['child'])
                        @foreach($value['child'] as $child_value)
                            <li><a data-href='{{url($child_value['menu_url'])}}' data-title="{{\App\Models\CommonModel::languageFormat($child_value['menu_name'] , $child_value['menu_en_name'])}}" href="javascript:void(0);"><i class="Hui-iconfont">{{$child_value['menu_icon']}}</i>&nbsp;{{\App\Models\CommonModel::languageFormat($child_value['menu_name'] , $child_value['menu_en_name'])}}</a></li>
                        @endforeach
                    @endif
                    </ul>
                </dd>
            </dl>
        @endforeach

    </div>
</aside>