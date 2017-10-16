<?php
/**
 * Created by BocWeb.
 * Author: Walker  QQ:120007700
 * Date  : 2017/10/12
 * Time  : 13:38
 */

namespace App\Models;

/**
 * 交易相关模型
 * Class Transaction
 * @package App\Models
 */
class Transaction extends CommonModel
{
    private $errors = array(); /*错误信息*/


    /**
     * 返回 模型 发生的错误信息
     * @return mixed
     */
    public function messages()
    {
        return $this->errors;
    }
}