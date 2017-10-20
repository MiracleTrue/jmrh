@extends('layouts.master')

@section('MyCss')
    {{--<link rel="stylesheet" href="{{URL::asset('/css/***.css')}}">--}}
    <link rel="stylesheet" href="{{asset('webStatic/css/military.css')}}">
    <link rel="stylesheet" href="{{asset('webStatic/css/user-management.css')}}">

@endsection
@section('content')
<section>
			<div>

				<div class="czi-filtrate">
					<span style="margin-left: 3%;">账户分类</span>
					<select name="">
						<option value="">全部</option>
					</select>

					<span>账号</span>
					<input type="text" name="" id="" value="" />

				</div>
				<a class="umt-seek">搜索</a>
			</div>

			<table>
				<tbody>
					<tr class="tr1">
						<th style="width: 9%;"><span>序号</span></th>
						<th style="width: 11%;"><span>姓名</span></th>
						<th style="width: 17%;"><span>手机</span></th>
						<th style="width: 16%;"><span style="">账户分类</span></th>
						<th><span style="">操作</span></th>
					</tr>
					<tr>
						<td>1</td>
						<td>张三</td>
						<td>15888888888</td>
						<td>供应商</td>
						<td>对20170926110753 订单进行报价</td>
						
					</tr>
					<tr>
						<td>2</td>
						<td>张三三</td>
						<td>15888888888</td>
						<td>供应商</td>
						<td>20170926110753 </td>
					</tr>
					<tr>
						<td>3</td>
						<td>张三</td>
						<td>15888888888</td>
						<td>军方</td>
						<td>发布了20170926110753 需求</td>
					</tr>
					<tr>
						<td>4</td>
						<td>张三三</td>
						<td>15888888888</td>
					<td>军方</td>
						<td>发布了20170926110753 需求</td>
					</tr>
					<tr>
						<td>5</td>
						<td>张三</td>
						<td>15888888888</td>
						<td>军方</td>
						<td>发布了20170926110753 需求</td>
					</tr>
					<tr>
						<td>6</td>
						<td>张三三</td>
						<td>15888888888</td>
						<td>平台运营者</td>
						<td>分配订单20170926110753</td>
					</tr>
					<tr>
						<td>7</td>
						<td>张三</td>
						<td>15888888888</td>
						<td>平台运营者</td>
						<td>增加账号ZHANG</td>
					</tr>
					<tr>
						<td>8</td>
						<td>张三三</td>
						<td>15888888888</td>
						<td>平台运营者</td>
						<td>发布了20170926110753 需求</td>
					</tr>
					<tr>
						<td>9</td>
						<td>张三</td>
						<td>15888888888</td>
						<td>平台运营者</td>
						<td>分配订单20170926110753 </td>
					</tr>

				</tbody>
			</table>

		</section>
@endsection