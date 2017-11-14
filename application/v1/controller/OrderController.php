<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/18
 * Time: 14:08
 */

namespace app\v1\controller;

use app\common\logic\DealRecord;
use app\common\controller\ApiController;
use app\common\model\Order;
use lea\Y;
use Payment\Client\Charge;
use Payment\Common\PayException;
use think\Db;
use think\Log;
use think\Request;

class OrderController extends ApiController
{

    //充值
    public function recharge(Request $request)
    {
        $money   = $request->post('money', '', 'intval');
        $payment = $request->post('payment', '', 'trim');

        if (!isset(DealRecord::$payments[$payment]) || $payment == 'balance') {
            return Y::json(102, '不支持当前支付方式');
        }

        if ($money <= 0) {
            return Y::json(103, '充值金额必须大于0.01元');
        }

        $order_no = build_order_no();

        $logic = new DealRecord(Y::$identity->id);

        $record_id = $logic->type('recharge')->payment($payment)->change($money)->set('order_no', $order_no)->set('cate', 'recharge')->record();
        if (!$record_id) {
            return Y::json(103, '订单生产异常，请稍后重试');
        }

        $config  = $payment == 'wx_app' ? config('payment.wechat') : config('payment.alipay');
        $payData = [
            'body'            => '充值余额',
            'subject'         => config('web_site_title') . '充值余额',
            'order_no'        => $order_no,
            'timeout_express' => time() + config('pay_time_limit') * 60,
            'amount'          => $money,
            'client_ip'       => $request->ip(),
            'goods_type'      => '1',
            'store_id'        => '',
            'return_param'    => 'recharge',
        ];

        try {
            $str = Charge::run($payment, $config, $payData);
        } catch (PayException $e) {
            Log::error('支付:' . $e->getTraceAsString());
            return Y::json(1001, $e->getMessage());
        }

        return Y::json(['text' => $str]);
    }


    //下单，订阅
    public function submitOrder(Request $request)
    {
        $cate    = $request->post('cate', '', 'trim');
        $payment = $request->post('payment', '', 'trim');
        $id      = $request->post('id', 0, 'intval');

        if (!in_array($cate, ['course', 'special_zone', 'free_zone', 'ting']) || $id <= 0 || !$payment) {
            return Y::json(101, '参数错误');
        }

        $info = Db::name($cate)->find($id);
        if ($info['status'] != 1) {
            return Y::json(102, '您要购买的商品不存在或已下架');
        }

        if ($info['fee'] <= 0) {
            return Y::json(102, '当前商品异常，无法购买');
        }

        if (!isset(DealRecord::$payments[$payment])) {
            return Y::json(102, '不支持当前支付方式');
        }

        if (Db::name('user_resource')->where('user_id', Y::$identity->id)->where('cate', $cate)->where('data_id', $id)->find()) {
            return Y::json(102, '您已经购买过该栏目了，无须重复购买');
        }

        $order_no = build_order_no();

        //生产订单
        $order_id = Db::name('order')->insertGetId([
            'user_id'     => Y::$identity->id,
            'order_no'    => $order_no,
            'serial_no'   => '',
            'audio_title' => $info['title'],
            'audio_cate'  => $cate,
            'audio_id'    => $id,
            'at_time'     => time(),
            'pay_time'    => time(),
            'status'      => Order::WAIT_PAY,
        ]);

        $logic     = new DealRecord(Y::$identity->id);
        $record_id = $logic->type('order')->payment($payment)->change(0 - $info['fee'])->set('order_no', $order_no)->set('cate', $cate)->data(['title' => $info['title'], 'cate' => $cate, 'id' => $id, 'order_id' => $order_id])->record();
        if (!$record_id) {
            return Y::json(103, '订单生产异常，请稍后重试');
        }

        if ($payment == 'balance') {
            Db::startTrans();
            try {
                $balance = Db::name('user')->where('id', Y::$identity->id)->value('balance');
                if ($balance < $info['fee']) {
                    return Y::json(104, '余额不足，无法购买');
                }

                //设置已支付
                Db::name('deal_record')->where('id', $record_id)->update([
                    'status'     => 1,
                    'pay_amount' => $info['fee'],
                    'pay_time'   => time(),
                ]);

                Db::name('order')->where('id', $order_id)->update([
                    'status'   => Order::COMPLETE_PAY,
                    'pay_time' => time(),
                ]);

                //改动余额
                Db::name('user')->where('id', Y::$identity->id)->setDec('balance', $info['fee']);

                //设置购买成功的课程
                Db::name('user_resource')->insert([
                    'user_id' => Y::$identity->id,
                    'cate'    => $cate,
                    'data_id' => $id,
                    'at_time' => time(),
                ]);
                Db::commit();
            } catch (\Exception $e) {
                Log::error('下单失败:' . $e->getMessage());
                Db::rollback();
            }
            return Y::json('购买成功');
        }

        if ($payment == 'wx_app' || $payment == 'ali_app') {
            $cate_name = config('other.cate_name');
            $config    = $payment == 'wx_app' ? config('payment.wechat') : config('payment.alipay');
            $payData   = [
                'body'            => '购买' . $cate_name[$cate] . ' ' . $info['title'],
                'subject'         => config('web_site_title') . '订单支付',
                'order_no'        => $order_no,
                'timeout_express' => time() + config('pay_time_limit') * 60,
                'amount'          => $info['fee'],
                'client_ip'       => $request->ip(),
                'goods_type'      => '1',
                'store_id'        => '',
                'return_param'    => 'order',
            ];

            try {
                $str = Charge::run($payment, $config, $payData);
            } catch (PayException $e) {
                Log::error('支付:' . $e->getTraceAsString());
                return Y::json(1001, $e->getMessage());
            }

            return Y::json(['text' => $str]);
        }
        return Y::json(104, '服务器异常，请稍后重试');
    }

}