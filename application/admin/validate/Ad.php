<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/10
 * Time: 14:28
 */

namespace app\admin\validate;


use think\Validate;

class Ad extends Validate
{
    protected $rule = [
        'title|标题'        => 'require|max:64',
        'remark|备注'       => 'max:255',
        'action_param|参数' => 'max:255',
        'status|状态'       => 'in:0,1',
    ];

}