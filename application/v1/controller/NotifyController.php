<?php

namespace app\v1\controller;

use app\common\util\PaymentNotify;
use app\common\util\ConfigApi;
use Payment\Client\Notify;
use Payment\Common\PayException;
use Payment\Config;
use think\Log;

class NotifyController
{

    public function __construct()
    {
        //注册配置项
        ConfigApi::config();
    }

    public function alipay()
    {
        $callback = new PaymentNotify();
        $config   = config('payment.alipay');
        $ret      = 'fail';
        try {
            $ret = Notify::run(Config::ALI_CHARGE, $config, $callback);
        } catch (PayException $e) {
            Log::error('alipay:' . '支付通知：' . $e->getTraceAsString());
        }
        exit($ret);
    }

    public function wechat()
    {
        $callback = new PaymentNotify();
        $config   = config('payment.wechat');
        $ret      = 'fail';
        try {
            $ret = Notify::run(Config::WX_CHARGE, $config, $callback);
        } catch (PayException $e) {
            Log::error('wechat:' . '支付通知：' . $e->getTraceAsString());
        }
        exit($ret);
    }


}