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
            <a href="#floor1"><img src="{{asset('webStatic/images/F1.png')}}" alt="" /></a>
            <a href="#floor2"><img src="{{asset('webStatic/images/F2.png')}}" alt="" /></a>
            <a href="#floor3"><img src="{{asset('webStatic/images/F3.png')}}" alt="" /></a>
            <a href="#floor4"><img src="{{asset('webStatic/images/F4.png')}}" alt="" /></a>
        </div>

        <div class="f1" id="floor1">
            <div style="overflow: hidden;padding-left: 90px;">
                <p style="float: left;"><span style="font-size: 26px;font-weight: bolder;">肉类</span>
                <ul class="head">
                    <li>猪肉</li>
                    <li>牛肉</li>
                    <li>羊肉</li>
                    <li>鸡肉</li>
                </ul>
                </p>
            </div>

            <ul class="goods-list">
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />
                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
            </ul>

        </div>
        <div class="f1" id="floor2">
            <div style="overflow: hidden;padding-left: 90px;">
                <p style="float: left;"><span style="font-size: 26px;font-weight: bolder;">肉类</span>
                <ul class="head">
                    <li>猪肉</li>
                    <li>牛肉</li>
                    <li>羊肉</li>
                    <li>鸡肉</li>
                </ul>
                </p>
            </div>

            <ul class="goods-list">
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />
                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
            </ul>

        </div>
        <div class="f1" id="floor3">
            <div style="overflow: hidden;padding-left: 90px;">
                <p style="float: left;"><span style="font-size: 26px;font-weight: bolder;">肉类</span>
                <ul class="head">
                    <li>猪肉</li>
                    <li>牛肉</li>
                    <li>羊肉</li>
                    <li>鸡肉</li>
                </ul>
                </p>
            </div>

            <ul class="goods-list">
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />
                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
            </ul>

        </div>
        <div class="f1" id="floor4">
            <div style="overflow: hidden;padding-left: 90px;">
                <p style="float: left;"><span style="font-size: 26px;font-weight: bolder;">肉类</span>
                <ul class="head">
                    <li>猪肉</li>
                    <li>牛肉</li>
                    <li>羊肉</li>
                    <li>鸡肉</li>
                </ul>
                </p>
            </div>

            <ul class="goods-list">
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />
                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
                <li>
                    <p>商品名称</p>
                    <img src="{{asset('webStatic/images/goods.png')}}" alt="商品" />

                </li>
            </ul>

        </div>
    </section>
@endsection


@section('MyJs')
    {{--<script type="text/javascript" src="{{URL::asset('/css/***.js')}}" ></script>--}}
    {{--<script type="text/javascript">--}}
        {{--/* code ...*/--}}
    {{--</script>--}}
@endsection