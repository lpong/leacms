<?php
/**
 * Created by PhpStorm.
 * User: YC
 * Date: 2017/4/30
 * Time: 13:51
 */

namespace app\v1\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'mobile|手机号码' => 'require|checkMobile|unique:user',
        'password|密码' => 'require|length:6,16',
        'code|验证码'    => 'require|number|length:4',
        'nickname|昵称' => 'chsDash'
    ];

    protected $message = [
        'mobile.require'     => '请输入您的手机号码',
        'mobile.checkMobile' => '手机号格式不正确',
        'mobile.unique'      => '该手机号已注册,请直接登录',
        'password.require'   => '密码不能为空',
        'password.length'    => '密码为6到16位',
        'code.require'       => '请输入4位短信验证码',
        'code.length'        => '请输入4位短信验证码',
    ];

    //验证场景
    protected $scene = [
        'signUp'         => ['mobile', 'password', 'code'],
        'relationUser'   => ['mobile', 'password', 'code'],
        'forgetPassword' => ['mobile' => 'require|checkMobile', 'password', 'code'],
        'login'          => ['mobile', 'password'],
    ];

    // 验证是否手机号
    protected function checkMobile($value)
    {
        return is_phone($value);
    }

}