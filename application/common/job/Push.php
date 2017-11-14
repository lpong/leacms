<?php

namespace app\common\job;

use JPush\Client;
use think\Log;
use think\queue\Job;

class Push
{

    public function fire(Job $job, $data)
    {
        $isJobDone = $this->pushMessage($data);

        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
            echo 'success' . PHP_EOL;
        } else {
            if ($job->attempts() > 3) {
                $job->delete();
            } else {
                $job->release(60);  //1min后重试
            }
        }
    }

    /**
     * 根据消息中的数据进行实际的业务处理
     * @param array|mixed $data 发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function pushMessage($data)
    {
        if (!isset($data['user_ids'])) {
            return true;
        }

        $data['alert'] = isset($data['alert']) ? $data['alert'] : '您有一条新的消息';
        if (!isset($data['extra']['type'])) {
            $data['extra']['type'] = 0;
        }

        $config        = config('other.jiguang_push');
        $app_key       = $config['app_key'];
        $master_secret = $config['master_secret'];


        $client = new Client($app_key, $master_secret, null);
        $push   = $client->push();
        $push->setPlatform(['ios', 'android']);
        try {
            if ($data['user_ids']) {
                $push->addAlias($data['user_ids']);
            } else {
                $push->addAllAudience();
            }

            $push->iosNotification($data['alert'], [
                'content-available' => true,
                'mutable-content'   => true,
                'extras'            => $data['extra'],
            ]);
            $push->androidNotification($data['alert'], [
                'extras' => $data['extra'],
            ]);

            $push->options([
                'apns_production' => !config('app_debug'),
            ]);
            $push->send();
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            //Log::error($e->getMessage());
            return false;
        } catch (\JPush\Exceptions\APIRequestException $e) {
            //Log::error($e->getMessage());
            return false;
        }
        return true;
    }

}