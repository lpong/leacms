<?php
/**
 * Created by PhpStorm.
 * User: YC
 * Date: 2017/4/29
 * Time: 13:59
 */

namespace app\admin\controller;

use app\common\controller\BaseCotroller;
use app\common\util\Auth;
use think\Response;

class CommonController extends BaseCotroller
{

    /**
     * 权限控制类
     * @var auth
     */
    protected $auth = null;

    /**
     * 用户信息
     * @var admin
     */
    protected $admin = null;


    public function _initialize()
    {
        parent::_initialize();
        //先校验是否登陆
        $this->admin = session('admin');
        if (!$this->admin || empty($this->admin['id'])) {
            if ($this->request->isAjax()) {
                abort(302, '请您登录');
            } else {
                $this->redirect('public/login');
            }

        }

        //ip限制
        if (config('admin_allow_ip')) {
            if (!in_array($this->request->ip(), explode(',', config('admin_allow_ip')))) {
                $this->error('禁止访问!');
            }
        }

        //站点关闭，只有超管可以访问
        if (config('web_site_close') == 0 && session('admin.id') != 1) {
            $this->error('站点已关闭，无法访问!');
        }

        //auth验证
        $this->auth = Auth::instance();

        $path = '/admin/' . $this->request->controller() . '/' . $this->request->action();
        $path = str_replace('.', '/', $path);
        //FileController和InfoController为所有用户有权限的控制器，无需验证
        $contr = strtolower($this->request->controller());
        if (!($contr == 'file' || $contr == 'info')) {
            //验证是否有权限
            if (!(in_array($path, config('allow_visit')) || $this->auth->check($path, $this->admin['id']))) {
                $this->error('No access：未授权访问');
            }
        }

        //非异步生成菜单信息
        if (!$this->request->isAjax()) {
            //设置菜单
            $data = $this->auth->getMenu($this->admin['id'], $path);
            $this->assign('__MENU__', $data);
        }
    }

}