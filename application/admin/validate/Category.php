<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/10
 * Time: 14:28
 */

namespace app\admin\validate;


use think\Validate;

class Category extends Validate
{
    protected $rule = [
        'name|名称'  => 'require|max:32',
        'sort|排序' => 'require|number',
    ];

}