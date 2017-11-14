<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/10
 * Time: 14:28
 */

namespace app\admin\model;

use think\Model;

class Category extends Model
{
    /**
     * 自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;


    public static function getCategory()
    {
        return self::all(['status', 'in', '0,1']);
    }
}