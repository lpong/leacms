<?php
return [

    //短信接口
    'sms'           => [
        'account'  => '',
        'password' => '',
    ],
    //不需要登录的接口
    'no_token_url'  => [
        '/v1/public/*',
        '/v1/file/*',
        '/v1/app/*',
        '/v1/data/*',
    ],
    //极光推送
    'jiguang_push'  => [
        'app_key'       => '',
        'master_secret' => '',
    ],

    //第三方开放平台登录
    'third'         => [
        'wechat' => [
            'app_id'     => '',
            'app_secret' => '',
            'callback'   => '',
            'scope'      => 'snsapi_userinfo',
        ],
    ],
    'easemob'       => [
        'org_name'  => '',
        'app_name'  => '',
        'org_admin' => '',
        'app_key'   => '',

        'client_id'     => '',
        'client_secret' => '',
    ],
];