<?php

namespace app\common\model;

use think\Db;
use think\Model;
use app\common\util\Y;

class Sms extends Model
{

    private $base_url = 'http://dx.ipyy.net/smsJson.aspx';

    //发送短信验证码
    public function sendCode($mobile, $type)
    {
        if (!is_phone($mobile)) {
            return '手机号码有误';
        }
        if (!$type) {
            return '参数错误';
        }

        if (!config('app_debug')) {
            //判断次数,5分钟内次数
            $count = Db::name('sms')->where('mobile', $mobile)->where('send_time', 'gt', time() - 300)->count();
            if ($count >= 3) {
                return '您发送短信太频繁，请稍后再试';
            }
            $count = Db::name('sms')->where('mobile', $mobile)->where('send_time', 'gt', time() - 24 * 60 * 60)->count();
            if ($count >= 10) {
                return '您今天发送短信太频繁，请以后再试';
            }
        }

        $code    = config('app_debug') ? 1234 : rand(1000, 9999);
        $content = str_replace('{code}', $code, config('sms_template'));

        if (config('app_debug')) {
            $ret = true;
        } else {
            $ret = $this->send($mobile, $content);
        }

        $data = ['code' => md5($code), 'send_time' => time()];

        $data['mobile']      = $mobile;
        $data['type']        = $type;
        $data['status']      = $ret === true ? 1 : 0;
        $data['content']     = $content;
        $data['sms_ret_msg'] = $ret === true ? 'success' : (string)$ret;

        Db::name('sms')->insert($data);
        if ($ret !== true) {
            return $ret;
        }

        cache('sms:' . $type . ':' . $mobile, $data, 1800);
        return true;
    }

    //验证短信沿着干嘛
    public function check($mobile, $type, $code)
    {
        $sms = cache('sms:' . $type . ':' . $mobile);
        if (!$sms) {
            return '请先发送短信验证码';
        }
        if ((time() - $sms['send_time'] > 1800)) {
            return '验证码已过期';
        }
        if ($sms['code'] != md5($code)) {
            return '验证码错误';
        }
        cache('sms:' . $type . ':' . $mobile, null);
        return true;
    }


    protected function send($mobile, $content)
    {
        $sms_config = config('other.sms');
        $post_data  = [
            "account"  => $sms_config['account'],
            "password" => strtoupper(md5($sms_config['password'])),
            "mobile"   => $mobile,
            "content"  => $content,
            "action"   => 'send',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($output, true);
        if (is_array($data) && isset($data['returnstatus']) && $data['returnstatus'] == 'Success') {
            return true;
        } else {
            return empty($data['message']) ? '发送失败' : $data['message'];
        }

    }

}
