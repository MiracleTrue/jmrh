<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 11:17
 */
namespace App\Http\Controllers;

use App\Models\Army;
use App\Models\CommonModel;
use App\Models\Product;
use App\Tools\M3Result;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


/**
 * 平台控制器
 * Class PlatformController
 * @package App\Http\Controllers
 */
class PlatformController extends Controller
{
    public $ViewData = array(); /*传递页面的数组*/

    public function NeedRelease(Request $request)
    {
        
    }

    public function Need()
    {
        
    }

}