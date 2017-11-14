<?php
/**
 * Created by PhpStorm.
 * User: Y.c
 * Date: 2017/6/19
 * Time: 10:44
 */

namespace lea;

use Hashids\Hashids;
use think\Config;
use think\Db;
use think\Request;

class Auth
{

    protected $request;
    protected $code = 0;
    protected $message = '';
    protected $user_id = null;
    protected $token = null;

    public function __construct()
    {
        $this->request = Request::instance();
    }

    /**
     * 设置code，设置错误信息
     * @param $code
     * @param $error
     */
    public function setError($code = 0, $error = '')
    {
        $this->code = $code;
        $this->message = $error;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->message;
    }

    /**
     * 获取code
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 获取用户
     * @return null
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * 获取用户
     * @return null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * 校验签名
     * @return bool
     */
    public function checkSign()
    {
        $request = $this->request;
        if ($request->header('Postman-Token', '')) {
            return true;
        }
        //校验签名
        $client_property = $request->header('client-property', '');
        $client_property = explode('-', $client_property);
        if (!$client_property || count($client_property) != 3) {
            $this->setError(403, '非法请求');
            return false;
        }
        list($timestamp, $rand_str, $sign) = $client_property;
        if (empty($timestamp) || empty($rand_str) || empty($sign) || (time() - $timestamp > 60) || strlen($rand_str) != 32) {
            $this->setError(403, '非法请求');
            return false;
        }
        //计算签名
        $sign_complete = md5(dechex($timestamp * 2017) . substr($rand_str, -12) . substr($rand_str, 4, 20));
        if ($sign_complete != $sign) {
            $this->setError(403, '非法请求');
            return false;
        }

        return true;
    }

    /**
     * 校验用户
     * @return bool
     */
    public function checkUser()
    {
        $request = $this->request;
        //验证token
        $authHeader = $request->header('Authorization');

        if ($authHeader != null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            $access_token = base64_decode(trim($matches[1]));
            $t = explode('-', $access_token);
            if (count($t) !== 2) {
                $this->setError(15, 'token无效');
                return false;
            }
            list($uuid, $token) = $t;
            $hash = new Hashids(config('data_auth_key'), 32);
            $info = $hash->decode($uuid);
            if (count($info) != 2) {
                $this->setError(12, '非法token');
                return false;
            }

            $token_expiration_date = config('token_expiration_date');
            if ($token_expiration_date > 0 && (time() - $info[1] > $token_expiration_date)) {
                $this->setError(13, '登陆已过期，请重新登陆');
                return false;
            }

            //验证token
            $check_token = cache('user:token:' . $info[0]);
            if (!$check_token || !$info[0]) {
                $this->setError(13, '用户验证失败，请重新登陆');
                return false;
            }
            if ($check_token != $token) {
                //验证是否登录过
                if (Db::name('user_token')->where('user_id', $info[0])->where('token', $token)->find()) {
                    $this->setError(20, '您的账号在异地登录而被迫下线，请重新登录');
                    return false;
                } else {
                    $this->setError(14, '用户验证失效，请重新登录');
                    return false;
                }
            }

            $this->user_id = $info[0];
            $this->token = $token;
            return true;
        } else {
            //Log::error(json_encode($request->header()));
            $this->setError(11, '缺少token');
            return false;
        }
    }

    /**
     * 校验url，是否需要用户验证
     * @return bool
     */
    public function checkUrl()
    {
        $request = $this->request;
        $urls = Config::get('other.no_token_url');
        if (!$urls || !is_array($urls)) {
            return false;
        }

        $m = $request->module();
        //获取路由
        $path = '/' . $request->module() . '/' . str_replace('.', '/', strtolower($request->controller())) . '/' . strtolower($request->action());

        //判断是否需要验证
        foreach ($urls as $val) {
            $val = '/' . $m . '/' . trim($val, '/');
            $val = str_replace('/' . $m . '/' . $m, '/' . $m, $val);
            if ($path == $val) return true;
            $position = strspn($path ^ $val, "\0");
            $str = substr($val, $position);
            if ($str == '*') return true;
        }
        return false;
    }
}