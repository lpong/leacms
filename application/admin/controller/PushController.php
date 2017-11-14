<?php

namespace app\admin\controller;

use JPush\Client;
use think\Db;
use think\Log;

class PushController extends CommonController
{

    public function index()
    {
        return view();
    }

    public function lists()
    {
        $start_time = $this->request->post('start_time', '', 'trim');   //开始注册时间
        $end_time   = $this->request->post('end_time', '', 'trim');   //结束注册时间

        $msgM = Db::name('msg')->where('user_id', 0);
        //时间
        $start_time = $start_time ? strtotime($start_time) : 0;
        $end_time   = $end_time ? strtotime($end_time) : time();
        if ($end_time && $start_time) {
            $msgM->where('send_time', 'between', [$start_time, $end_time]);
        }

        $list = $msgM->order('id desc')->paginate(20);
        $this->assign('list', $list);
        return view();
    }

    public function publish()
    {
        if ($this->request->isPost()) {
            $msg = $this->request->post('msg', '', 'trim');
            if (!$msg || strlen($msg) > 255) {
                $this->error('推送内容位1-255个字符');
            }
            if (!Db::name('msg')->insert(['user_id' => 0, 'msg' => $msg, 'at_time' => time(), 'is_read' => 0])) {
                $this->error('推送失败');
            }

            //开始推送
            $config        = config('other.jiguang_push');
            $app_key       = $config['app_key'];
            $master_secret = $config['master_secret'];


            $client = new Client($app_key, $master_secret, null);
            $push   = $client->push();
            $push->setPlatform(['ios', 'android']);
            try {
                $push->addAllAudience();

                $push->iosNotification($msg, [
                    'content-available' => true,
                    'mutable-content'   => true,
                    'extras'            => [
                        'type' => 0
                    ],
                ]);
                $push->androidNotification($msg, [
                    'extras' => new \stdClass(),
                ]);

                $push->options([
                    'apns_production' => !config('app_debug'),
                ]);
                $push->send();
            } catch (\JPush\Exceptions\APIConnectionException $e) {
                Log::error($e->getMessage());
                $this->error($e->getMessage());
            } catch (\JPush\Exceptions\APIRequestException $e) {
                Log::error($e->getMessage());
                $this->error($e->getMessage());
            }
            $this->success('推送成功');
        } else {
            return view();
        }
    }
}