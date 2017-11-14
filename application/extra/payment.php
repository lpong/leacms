<?php

//支付配置

return [
    'alipay' => [
        'name'            => '支付宝支付',
        'use_sandbox'     => false,
        'partner'         => '',
        'app_id'          => '',
        'sign_type'       => 'RSA2',
        'ali_public_key'  => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlt1P8mU8ihzxYKV7HAGGfU9QnceV7Lead9lefWadQWD9yAdSIccVCAoBndvZGFOzG8N29DxiOXkUs1QqG91N2hRxJUdMilFYupdMF4MJSzL9FIg3gRiOc+E6837j83HrcoGiwWH9Mdf6f/KRWCgVpTxHWL0hCyikoWJSIbdJgjCr8CLx93a/gKjX7Z3UPuO36IO7ZG7ifxAyhRE2ZnRKYdVuuc4x90QS0CkRDJ0CuY3wHSRvedrC6r49AApKBMeIuqtNNnkQYIMUJlvvjJ38nQvWgSWoRgBy/c+ZCwyoSyEqd66thfPhkg9DBU9PCbHK3TE87CZksAYtYF6buNIzsQIDAQAB',
        'rsa_private_key' => 'MIIEpAIBAAKCAQEA0i5T1LSR4hs1+zJ55xP9GJX5zgCox2nyBkPn2CBsY5hlbfLdS1R8fS7MqA2YZXXBxpNovrmEaQAnTQDqDi+agDnmA4PZHXrMgjeY1d0Vr6cck1RVfbweABww3iIj9N4PqnRfxarL1gghi4ZzBV9hFfNFzMsWWQZqglrOkkyW6RDVNjMN6sXQGgGgVHfTRvw9nTte1UcaNUoaOuq1vivO4EGo5Rzxo2zGL/bgfRUiGZhc5VyMRqIPGXoyWSlyz//9yBt9341rn84IrTq5lk7loBrK7lrQCCHdFYI5jolECc3Kt/2jqNSo2uRLeIb6FP4tQlUydUYiVxjXDH5YWzRC+wIDAQABAoIBAFGlMWdlJW5Bx7xmpTgAacbNK5a8ZtPf8eZBJqSsI66kmnIVb6U9koZGUbXOYi63iaiLxpqgEjF/n8Sn4EPWdhvtYc5nv0VWWG8VDce19CCht9X7YqJPGxBL4yfG91S9ljROtI1ihYAE9JLwjWm+3UXblHezRGEcvu8YOzpViiHKdZGFzc82ywz1tewEAgCiBx040I9/33GCwqAObr9i8hNzuotMimtqRN5K9B+Ta52UZ3wq0JOYDX7sZ7dzqbpxX7puwCLVC2StZJIEo2JYP+Tg0kyFx3SSV3QB7tC6LW46c8pAemwJ+gL/xPOko55VHslTuPnv2XElY74ern3I1bECgYEA92f16mjgHOfYCtNDwidY9x4804l1bvfe7y2O3bkuTU5YLAUuy7UNmR7JOSXgZE9nJIjLW201MATF+no/f6JlNvWdhHyYbKlX9v/0LDQ9njLfppctyhryBT4ZVkw9q3D9p4PP3510afyONSNBaUqADtxGchaHvIAbb4uMqlyNoI8CgYEA2XtYZYX+dGPZtk807ncFrwsh4d+N3QWv/RGm6YSzpExy3q/fcw80/895xZAgVhWmj3WAoavJLCidi8w3Yz0Zeky5rf4V77iR7l3sYMku4X1vCb1SWbUtoW+pDjDEpV0aXnxQ9Elvq66skULF8fCtVFVW/TYCcQMGm0Fylq5FlNUCgYEA5fQ4/EGyXOuZQKaQyjssU7RFMREIl7XH/mNH8QoX4T66cV/32NtV4kZdHcL4yAONiMWkzju5PgKRyXgq4QUeMnEkkWoFeqoD2s6YMyXzR8FdY0gNBVb6MKlBf5apk1oYScUYR23gMErQGs/EEotub2GKEKykSS6BUbJRcl9IPD0CgYA4MCnycOAA6htBgs1GHvIU+2dRmBRq2iFR3NGm22YeOLIk3oN4kqE6xjvL0yTHmOhjgBQvsNZU1ll92NEmjo6ajmAal97LBICPeSytBM1b7LBXb9Zq3uhfIR4oUGk94AQEDVJNCvwS+xoSnZHOpEPj2fiTivSuhf2a9xnH3FaV0QKBgQDlxnM/zsUjtBvHlANQkqSgcDOttMatUUOtebxxwnv4pHSYkWgBUoo2WS/eeS3FMXpnfMR+j0zNOnlFd++DvwvN6qhrS7Aftpxu5EgqTo19OCQhj4YFMybY4qhrl5n0dcX4YC3Bklyy1yrPvph9cBjBpz8kHNWYf87vT9UV2md/Kw==',
        'limit_pay'       => [
        ],
        'notify_url'      => \think\Request::instance()->domain() . '/v1/notify/alipay',
        'return_raw'      => false,
    ],
    'wechat' => [
        'name'         => '微信支付',
        //微信支付配置数组
        'app_id'       => '',
        'mch_id'       => '',
        'md5_key'      => '',
        'app_cert_pem' => ROOT_PATH . 'data/rsa/apiclient_cert.pem',
        'app_key_pem'  => ROOT_PATH . 'data/rsa/apiclient_key.pem',
        'sign_type'    => 'MD5',// MD5  HMAC-SHA256
        'limit_pay'    => [
        ],
        'fee_type'     => 'CNY',// 货币类型  当前仅支持该字段
        'notify_url'   => \think\Request::instance()->domain() . '/v1/notify/wechat',
        //'redirect_url' => \think\Request::instance()->domain() . '/api/pay/return?type=wechat',
        'return_raw'   => false,
    ]
];
