<?php

namespace app\common\util;

use think\Db;
use think\Queue;

class Push
{

    /**
     * 消息通知
     * @param $data
     * @return mixed
     */
    public static function notice($message = '', $user_id, $data = [])
    {
        $insert_data = [
            'user_id' => $user_id,
            'msg'     => $message,
            'data'    => $data ? json_encode($data) : '',
            'at_time' => time(),
            'is_read' => 0
        ];
        Db::name('msg')->insert($insert_data);

        //激光要求，转为字符串
        $user_id = strval($user_id);
        return Queue::push('app\common\job\Push', [
            'alert'    => $message,
            'user_ids' => $user_id,
            'extra'    => $data
        ]);
    }

    /**
     * 自定义消息
     * @param $data
     * @return mixed
     */
    public static function msg($title = '', $message = '', $user_ids = '', $data = [])
    {
        if (is_array($user_ids)) {
            foreach ($user_ids as &$val) {
                $val = strval($val);
            }
        } else {
            $user_ids = strval($user_ids);
        }
        return Queue::push('app\common\job\Push', [
            'title'    => $title,
            'message'  => $message,
            'user_ids' => $user_ids,
            'extra'    => $data,
            'device'   => $device
        ]);
    }
}