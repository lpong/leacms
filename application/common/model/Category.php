<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/10
 * Time: 14:28
 */

namespace app\common\model;

use think\Db;
use think\Model;

class Category extends Model
{
    const SINGLE_PAGE = 1;
    const LIST_PAGE = 2;
    /**
     * 自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;


    public static function getCategory($map = [])
    {
        $model = Db::name('category');
        if ($map) {
            $model->where($map);
        }
        return $model->column('name', 'id');
    }


}