<?php

namespace app\common\controller;

use app\v1\model\User;
use app\common\util\ConfigApi;
use lea\Auth;
use lea\Y;
use think\Request;
use think\Response;

class ApiController
{

    public function __construct(Request $request)
    {
        //注册配置项
        ConfigApi::config();

        //ip限制
        if (config('admin_allow_ip')) {
            if (!in_array($request->ip(), explode(',', config('admin_allow_ip')))) {
                Response::create(['code' => 401, 'message' => '您所在区域被限制访问，请稍后再试~', 'data' => new \stdClass()], 'json')->send();
                exit;
            }
        }

        //验证通过，注入user
        $auth = new Auth();
        Y::app();
        if ($auth->checkUser() === true) {
            $user_id = $auth->getUserId();
            $user_info = User::getUserInfo($user_id);
            if ($user_info) {
                $request->token = $auth->getToken();
                Y::app()->bindUser(array2object($user_info));
            } else {
                Response::create(['code' => 20, 'message' => '用户信息异常，请重新登录', 'data' => new \stdClass()], 'json')->send();
                exit;
            }
        } else {
            if ($auth->checkUrl() !== true) {
                Response::create(['code' => $auth->getCode(), 'message' => $auth->getError(), 'data' => new \stdClass()], 'json')->send();
                exit;
            }
        }
    }

    /**
     * 空方法，404页面
     */
    public function _empty()
    {
        return Y::json(404, '请求的路径不存在');
    }
}
