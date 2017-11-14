<?php

namespace app\v1\controller;

use app\v1\model\User;
use app\v1\validate\User as UserValidate;
use app\common\controller\ApiController;
use app\common\model\Sms;
use Hashids\Hashids;
use lea\Curl;
use lea\Y;
use think\Db;
use think\Log;
use think\Request;
use third\Http;
use third\Wechat;


class PublicController extends ApiController
{

    //登陆
    public function login(Request $request)
    {
        $mobile   = $request->post('mobile', '');
        $password = $request->post('password', '');

        if (!$mobile || !$password) {
            return Y::json(100, '手机号或用户名不能为空');
        }
        if (!is_phone($mobile)) {
            return Y::json(100, '手机号格式错误');
        }

        $user = User::get(['mobile' => $mobile]);
        if (!$user || $user->password != User::passwordEncrypt($password)) {
            return Y::json(100, '手机号不存在或密码不匹配');
        }

        //用户状态
        if ($user->status != User::STATUS_ACTIVE) {
            return Y::json(100, '用户已被禁用，无法登录');
        }

        //登陆成功

        $token = User::generateToken($user->id);
        if (!$token) {
            return Y::json(100, '登录异常，请稍后再试');
        }

        return Y::json('登陆成功', [
            'nickname' => $user->nickname,
            'uuid'     => $user->uuid,
            'token'    => $token
        ]);
    }


    //注册
    public function signUp(Request $request)
    {
        $post     = $request->post();
        $validate = new UserValidate();
        $result   = $validate->scene('signUp')->check($post);
        if ($result !== true) {
            return Y::json(101, $validate->getError());
        }

        //验证短信验证码
        $result = (new Sms())->check($post['mobile'], 'signUp', $post['code']);
        if ($result !== true) {
            return Y::json(101, $result);
        }

        //成功注册
        $result = User::register($post);
        if (is_array($result) && $result['token']) {
            return Y::json('注册成功', $result);
        } else {
            return Y::json(101, is_string($result) ? $result : '服务器异常，请联系客服');
        }

    }

    //微信登录
    public function wechat()
    {
        $post = Request::instance()->post();
        if (!$post) {
            return Y::json(102, '参数错误');
        }

        $wechat = new Wechat();
        $result = $wechat->getUserInfo($post);
        if (!$result) {
            return Y::json(102, '获取用户信息失败，请稍后重试');
        }

        $user = User::get(['openid' => $result['openid']]);
        if (!$user) {
            return Y::json(105, '用户不存在', ['step' => 'relation_user', 'nickname' => $result['userinfo']['nickname']]);
        }

        $token = User::generateToken($user->id);
        return Y::json('修改成功', [
            'token'    => $token,
            'nickname' => $user->nickname,
            'uuid'     => $user->uuid
        ]);

    }

    //用户关联
    public function relationUser(Request $request)
    {
        $post     = $request->post();
        $validate = new UserValidate();
        $result   = $validate->scene('relationUser')->check($post);
        if ($result !== true) {
            return Y::json(101, $validate->getError());
        }

        if (empty($post['access_token'])) {
            return Y::json(101, '参数错误');
        }

        //验证短信验证码
        $result = (new Sms())->check($post['mobile'], 'relationUser', $post['code']);
        if ($result !== true) {
            return Y::json(101, $result);
        }

        $wechat = new Wechat();
        $result = $wechat->getUserInfo(['access_token' => $post['access_token']]);
        if (!$result || empty($result['openid'])) {
            return Y::json(102, '获取用户信息失败，请稍后重试');
        }

        $user = User::get(['mobile' => $post['mobile']]);
        if ($user) {
            if ($user->password != User::passwordEncrypt($password)) {
                return Y::json(103, '用户不存在或密码不匹配');
            }

            //用户状态
            if ($user->status != User::STATUS_ACTIVE) {
                return Y::json(103, '此手机帐号已被禁用');
            }

            if ($user->openid) {
                return Y::json(105, '此手机账号已关联其他微信帐号，若您想继续关联请先登录此手机帐号进行微信帐号解绑。');
            }

            if (strtotime($user->register_time) == $user->update_time) {
                $user->nickname    = $result['userinfo']['nickname'];
                $user->face        = $result['userinfo']['headimgurl'];
                $user->openid      = $result['openid'];
                $user->update_time = time();
                $user->save();
                User::clearUserInfoCache($user->id);
                $token = User::generateToken($user->id);
                return Y::json('关联成功', [
                    'token'    => $token,
                    'nickname' => $user->nickname,
                    'uuid'     => $user->uuid
                ]);
            }
        } else {
            $post['nickname'] = $result['userinfo']['nickname'];
            $post['face']     = $result['userinfo']['headimgurl'];
            $post['openid']   = $result['openid'];
            $result           = User::register($post);
            if (is_array($result) && $result['token']) {
                return Y::json('关联成功', $result);
            } else {
                return Y::json(101, is_string($result) ? $result : '服务器异常，请联系客服');
            }
        }
    }

    //忘记密码,修改密码
    public function forgetPassword(Request $request)
    {
        $post     = $request->post();
        $validate = new UserValidate();
        $result   = $validate->scene('forgetPassword')->check($post);
        if ($result !== true) {
            return Y::json(1001, $validate->getError());
        }

        //验证短信验证码
        $result = (new Sms())->check($post['mobile'], 'forgetPassword', $post['code']);
        if ($result !== true) {
            return Y::json(1002, $result);
        }

        $user = User::get(['mobile' => $post['mobile']]);
        if (!$user) {
            return Y::json(1003, '账号不存在');
        }
        if ($user->status != User::STATUS_ACTIVE) {
            return Y::json(1003, '该账户已被禁用');
        }
        $new_password = User::passwordEncrypt($post['password']);
        if (Db::name('user')->where('id', $user->id)->setField('password', $new_password) !== false) {
            $token = User::generateToken($user->id);
            return Y::json('修改成功', [
                'token'    => $token,
                'nickname' => $user->nickname,
                'uuid'     => $user->uuid
            ]);
        }
        return Y::json(1003, '修改失败');
    }

    /**
     * 获取登陆的短信验证码
     * @return mixed
     */
    public function sendSms(Request $request)
    {
        $mobile = $request->post('mobile', '');
        $mark   = $request->post('mark', 'login');
        if (!$mobile || !is_phone($mobile)) {
            return Y::json(1110, '手机号格式不正确');
        }

        if ($mark == 'signUp' || $mark == 'editMobile') {
            //判断该用户是否已注册
            if (User::get(['mobile' => $mobile])) {
                return Y::json(1101, '该手机号已注册');
            }
        }

        if ($mark == 'forgetPassword') {
            //判断该用户是否已注册
            if (!User::get(['mobile' => $mobile])) {
                return Y::json(1101, '手机号未注册');
            }
        }

        $result = (new Sms())->sendCode($mobile, $mark);
        if ($result !== true) {
            return Y::json(1112, $result);
        }
        return Y::json('发送成功');
    }

}
