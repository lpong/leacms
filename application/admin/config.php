<?php
/**
 * Created by PhpStorm.
 * User: YC
 * Date: 2017/4/29
 * Time: 15:43
 */
return [
    'view_replace_str' => [
        '__PUBLIC__' => '/static/admin',
    ],

    // URL伪静态后缀
    'url_html_suffix'  => '',

    'auth' => [
        'auth_on'           => 1, // 权限开关
        'auth_type'         => 1, // 认证方式，1为实时认证；2为登录认证。
        'auth_group'        => 'auth_group', // 用户组数据表名
        'auth_group_access' => 'auth_group_access', // 用户-用户组关系表
        'auth_rule'         => 'auth_rule', // 权限规则表
        'auth_user'         => 'admin', // 用户信息表
    ],
    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session' => [
        'prefix'         => 'education_',
        'type'           => 'redis',
        'expire'         => 21600,
        'var_session_id' => '',
        // redis主机
        'host'           => '127.0.0.1',
        // redis端口
        'port'           => 6379,
        // 密码
        'password'       => '',
        'auto_start'     => true,
    ],
];