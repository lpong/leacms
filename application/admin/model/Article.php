<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/10
 * Time: 14:28
 */

namespace app\admin\model;

use think\Model;

class Article extends Model
{

    public static $cate = [
        1 => '系统单页',
        2 => '文章'
    ];

    /**
     * 自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 自动完成
     * @var array
     */
    protected $insert = ['create_aid'];

    /**
     * 设置操作人
     * @return mixed
     */
    protected function setCreateAidAttr()
    {
        return session('admin.id');
    }

}