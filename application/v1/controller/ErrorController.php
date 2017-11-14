<?php

namespace app\v1\controller;
use lea\Y;

/**
 * Created by PhpStorm.
 * User: YC
 * Date: 2017/5/11
 * Time: 15:45
 */
class  ErrorController
{
    /**
     * 空方法，404页面
     */
    public function _empty()
    {
        return Y::json(404, '请求的路径不存在');
    }
}