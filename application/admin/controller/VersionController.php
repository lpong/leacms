<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/9/14
 * Time: 14:23
 */

namespace app\admin\controller;

use think\Db;

class VersionController extends CommonController
{


    //版本列表
    public function index()
    {
        $list = Db::name('version')->order('id desc')->select();
        $this->assign('list', $list);
        return view();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $post            = $this->request->post();
            $post['at_time'] = time();
            if (Db::name('version')->insert($post)) {
                $this->success('发布成功', url('index'));
            }
            $this->error('发布失败');
        } else {
            $version = Db::name('version')->order('id desc')->max('version');
            $this->assign('version', $version);
            return view();
        }
    }
}