<?php
return [
    'view_replace_str'    => [
        '__PUBLIC__' => '/static/api',
    ],

    //默认返回类型
    'default_return_type' => 'json',

    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'      => 'trim,strip_tags',
];
