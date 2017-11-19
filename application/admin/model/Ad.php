<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/11
 * Time: 10:29
 */

namespace app\admin\model;

use think\Model;

class Ad extends Model
{

    /**
     * 自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    protected $insert = ['status' => 1];

    /**
     * 广告类型
     * @var array
     */
    public static $adType = [
        1 => '轮播图'
    ];
}