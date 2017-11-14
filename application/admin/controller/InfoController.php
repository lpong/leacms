<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use Qiniu\Auth;
use think\Db;
use think\Validate;

class InfoController extends CommonController
{
    /**
     * 用户个人中心
     * @return mixed
     */
    public function user()
    {
        $info = Db::name('admin')->find(session('admin.id'));
        $this->assign('info', $info);

        return view();
    }

    /**
     * 更新昵称
     */
    public function updateNickname()
    {
        $nickname = $this->request->post('nickname', '', 'trim');
        if (!$nickname) {
            $this->error('昵称不能为空');
        }
        if (Db::name('admin')->where('id', session('admin.id'))->setField('nickname', $nickname) !== false) {
            session('admin.nickname', $nickname);

            $this->success('修改成功');
        }

        $this->success('修改失败');
    }

    //更新头像
    public function updateFace()
    {
        $face = $this->request->post('face', 0, 'intval');
        if (Db::name('admin')->where('id', session('admin.id'))->setField('face', $face) !== false) {
            session('admin.face', get_file_path($face));

            $this->success('修改成功');
        }

        $this->success('修改失败');
    }

    //修改密码
    public function updatePassword()
    {
        $post     = $this->request->post();
        $validate = new Validate([
            'password|密码'     => 'require|length:6,16',
            'repassword|重复密码' => 'require|length:6,16'
        ], [
            'password.length' => '密码的长度为6到16位'
        ]);
        extract($post);
        if (!$validate->check($post)) {
            $this->error($validate->getError());
        }
        if ($password != $repassword) {
            $this->error('两次密码输入不一致');
        }
        $admin    = new Admin();
        $password = $admin->encryptPassword($password);
        if (Db::name('admin')->where('id', session('admin.id'))->setField('password', $password) !== false) {
            $this->success('修改成功');
        }

        $this->success('修改失败');
    }
}