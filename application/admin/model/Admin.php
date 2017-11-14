<?php


namespace app\admin\model;

use app\common\util\Auth;
use think\Model;
use think\Session;
use think\Cookie;

class Admin extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'create_time';
    protected $updateTime         = false;

    /**
     * 密码加密
     * @param $password
     * @return string
     */
    public function encryptPassword($password)
    {
        return md5(sha1($password, substr($password, 0, 3)));
    }

    public function login($username, $password)
    {
        $admin = self::get(['username' => $username]);
        if (!$admin) {
            return '用户名不存在';
        }
        if ($admin->password !== $this->encryptPassword($password)) {
            return '用户名或密码错误';
        }

        if (1 != $admin->status) {
            return '该用户已被禁用，无法登陆';
        }

        //登录成功
        $admin->login_times     = $admin->login_times + 1;
        $admin->last_login_ip   = \think\Request::instance()->ip();
        $admin->last_login_time = time();
        $admin->token           = md5($password . $username . uniqid());
        $admin->save();

        //获取用户组
        $group = Auth::instance()->getGroups($admin->id);
        foreach ($group as &$val) {
            unset($val['rules']);
        }

        //写入session
        Session::set('admin', [
            'id'              => $admin->id,
            'username'        => $admin->username,
            'nickname'        => $admin->nickname,
            'last_login_time' => $admin->last_login_time,
            'last_login_ip'   => $admin->last_login_ip,
            'group'           => $group,
            'face'            => get_file_path($admin->face)
        ]);
        Cookie::set('username', $admin->username);
        return true;
    }

    public function logout()
    {
        Session::clear(config('session.prefix'));
        return true;
    }
}