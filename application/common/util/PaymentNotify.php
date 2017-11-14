<?php

namespace app\common\util;

use app\common\model\Order;
use lea\Y;
use Payment\Notify\PayNotifyInterface;
use think\Db;
use think\exception\DbException;
use think\Log;

class PaymentNotify implements PayNotifyInterface
{

    public function notifyProcess(array $data)
    {
        $order_sn = $data['order_no'];

        $deal_record = Db::name('deal_record')->where('order_no', $order_sn)->find();
        if (!$deal_record || $deal_record['status'] != 0) {
            return false;
        }

        if ($data['trade_state'] == 'success') {
            Db::startTrans();
            try {
                //支付成功
                Db::name('deal_record')->where('id', $deal_record['id'])->update([
                    'status'     => 1,
                    'pay_time'   => strtotime($data['pay_time']),
                    'serial_no'  => $data['transaction_id'],
                    'pay_amount' => $data['amount'],
                ]);

                Db::name('order')->where('order_no', $deal_record['order_no'])->update([
                    'status'   => Order::COMPLETE_PAY,
                    'pay_time' => strtotime($data['pay_time']),
                ]);

                if ($deal_record['type'] == 'order') {
                    //购买的商品
                    $info = json_decode($deal_record['data'], true);
                    if (empty($info['cate']) || empty($info['id'])) {
                        Log::error('严重异常：deal_record:' . $deal_record['id']);
                        Db::rollback();
                        return false;
                    }

                    //设置购买成功的课程
                    Db::name('user_resource')->insert([
                        'user_id' => $deal_record['user_id'],
                        'cate'    => $info['cate'],
                        'data_id' => $info['id'],
                        'at_time' => time(),
                    ]);
                }

                if ($deal_record['type'] == 'recharge') {
                    //加余额
                    Db::name('user')->where('id', $deal_record['user_id'])->setInc('balance', $data['amount']);
                }
                Db::commit();
            } catch (DbException $e) {
                // 回滚事务
                Log::error($e->getMessage());
                Db::rollback();
                return false;
            }
            return true;
        } else {
            //支付成功
            $id = Db::name('deal_record')->where('id', $deal_record['id'])->update([
                'status'   => 2,
                'pay_time' => time(),
            ]);
            if ($id !== false) {
                return true;
            }
            return false;
        }
    }

}