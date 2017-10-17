@extends('admin.layouts.master')
{{--@section('title', '页面title不设置默认取master')--}}


@section('content')
    <div class="page-container">
        <p class="f-20 text-success">欢迎使用LaravelShop <span class="f-14">v1.0</span>后台系统！</p>
        <p>登录次数：18 </p>
        <p>上次登录IP：222.35.131.79.1  上次登录时间：2014-6-14 11:19:55</p>
        <table class="table table-border table-bordered table-bg">
            <thead>
            <tr>
                <th colspan="7" scope="col">信息统计</th>
            </tr>
            <tr class="text-c">
                <th>统计</th>
                <th>资讯库</th>
                <th>图片库</th>
                <th>产品库</th>
                <th>用户</th>
                <th>管理员</th>
            </tr>
            </thead>
            <tbody>
            <tr class="text-c">
                <td>总数</td>
                <td>92</td>
                <td>9</td>
                <td>0</td>
                <td>8</td>
                <td>20</td>
            </tr>
            <tr class="text-c">
                <td>今日</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr class="text-c">
                <td>昨日</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr class="text-c">
                <td>本周</td>
                <td>2</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr class="text-c">
                <td>本月</td>
                <td>2</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
            </tr>
            </tbody>
        </table>
        <table class="table table-border table-bordered table-bg mt-20">
            <thead>
            <tr>
                <th colspan="2" scope="col">服务器信息</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>服务器IP地址</td>
                <td>{{$_SERVER['SERVER_ADDR']}}</td>
            </tr>
            <tr>
                <td>服务器域名</td>
                <td>{{$_SERVER['SERVER_NAME']}}</td>
            </tr>
            <tr>
                <td>服务器端口 </td>
                <td>{{$_SERVER['SERVER_PORT']}}</td>
            </tr>
            <tr>
                <td>服务器版本 </td>
                <td>{{$_SERVER['SERVER_SOFTWARE']}}</td>
            </tr>
            <tr>
                <td>本文件所在文件夹 </td>
                <td>{{$_SERVER['SCRIPT_FILENAME']}}</td>
            </tr>
            <tr>
                <td>服务器操作系统 </td>
                <td>{{PHP_OS}}</td>
            </tr>
            <tr>
                <td>服务器最大上传限制 </td>
                <td>{{get_cfg_var ("upload_max_filesize") ? get_cfg_var ("upload_max_filesize"):"不允许上传附件"}}</td>
            </tr>
            <tr>
                <td>服务器当前时间 </td>
                <td>{{date('Y-m-d H:i:s',time())}}</td>
            </tr>
            </tbody>
        </table>
    </div>

    @include('admin.include.inc_footer')
@endsection

@section('MyJs')

@endsection

