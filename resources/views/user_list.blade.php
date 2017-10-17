@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/css/user-management.css')}}">

@endsection
@section('content')
    <section>
        <div>
            <a href="#" class="umt-add"></a>
            <div class="umt-filtrate">
                <span style="margin-left: 3%;">账户分类</span>
                <select name="">
                    <option value="">全部</option>
                </select>
					<span>
						状态
					</span>
                <select name="">
                    <option value="">全部</option>
                </select>
                <span>姓名</span>
                <input type="text" name="" id="" value="" />
					<span style="margin-left: 3%;">
						手机
					</span>
                <input type="text" name="" id="" value="" />
            </div>
            <a class="umt-seek">搜索</a>
        </div>

        <table>
            <tbody>
            <tr class="tr1">
                <th style="width: 9%;"><span>序号</span></th>
                <th style="width: 11%;"><span>姓名</span></th>
                <th style="width: 17%;"><span>品名</span></th>
                <th style="width: 15%;"><span>手机</span></th>
                <th style="width: 13%;"><span style="">账户分类</span></th>
                <th style="width: 14%;"><span style="">状态</span></th>
                <th><span style="">操作</span></th>
            </tr>
            <tr>
                <td>1</td>
                <td>20170926110753</td>
                <td>白菜</td>
                <td>20170928</td>
                <td>500斤</td>
                <td>待确认</td>
                <td class="blueWord">
                    <a class="mly-caozuo">禁用</a>
                    <a style="margin-left: 5%;">删除</a>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>20170926110753</td>
                <td>白菜</td>
                <td>20170928</td>
                <td>500斤</td>
                <td>待确认</td>
                <td class="blueWord">
                    <a class="mly-caozuo">禁用</a>
                    <a style="margin-left: 5%;">删除</a>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>20170926110753</td>
                <td>白菜</td>
                <td>20170928</td>
                <td>500斤</td>
                <td>待确认</td>
                <td class="blueWord">
                    <a class="mly-caozuo">禁用</a>
                    <a style="margin-left: 5%;">删除</a>
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td>20170926110753</td>
                <td>白菜</td>
                <td>20170928</td>
                <td>500斤</td>
                <td>待确认</td>
                <td class="blueWord">
                    <a class="mly-caozuo">禁用</a>
                    <a style="margin-left: 5%;">删除</a>
                </td>
            </tr>
            <tr>
                <td>5</td>
                <td>20170926110753</td>
                <td>白菜</td>
                <td>20170928</td>
                <td>500斤</td>
                <td>待确认</td>
                <td class="blueWord">
                    <a class="mly-caozuo">禁用</a>
                    <a style="margin-left: 5%;">删除</a>
                </td>
            </tr>
            <tr>
                <td>6</td>
                <td>20170926110753</td>
                <td>白菜</td>
                <td>20170928</td>
                <td>500斤</td>
                <td>待确认</td>
                <td class="blueWord">
                    <a class="mly-caozuo">禁用</a>
                    <a style="margin-left: 5%;">删除</a>
                </td>
            </tr>
            <tr>
                <td>7</td>
                <td>20170926110753</td>
                <td>白菜</td>
                <td>20170928</td>
                <td>500斤</td>
                <td>已确认</td>
                <td class="blueWord">
                    <a class="mly-caozuo">禁用</a>
                    <a style="margin-left: 5%;">删除</a>
                </td>
            </tr>
            <tr>
                <td>8</td>
                <td>20170926110753</td>
                <td>白菜</td>
                <td>20170928</td>
                <td>500斤</td>
                <td>已发货</td>
                <td class="blueWord">
                    <a class="mly-caozuo">禁用</a>
                    <a style="margin-left: 5%;">删除</a>
                </td>
            </tr>
            <tr>
                <td>9</td>
                <td>20170926110753</td>
                <td>白菜</td>
                <td>20170928</td>
                <td>500斤</td>
                <td>待确认</td>
                <td class="blueWord">
                    <a class="mly-caozuo">启用</a>
                    <a style="margin-left: 5%;">删除</a>
                </td>
            </tr>

            </tbody>
        </table>

    </section>
@endsection


@section('MyJs')
    <script>
        ;
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
                        content: '添加账户.html'
                    });
                });
            });

        }();
    </script>
@endsection