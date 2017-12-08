<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/11/18
 * Time: 14:45
 */

namespace app\admin\controller;

use think\Db;

class MessageController extends CommonController
{
    public function index()
    {
        $list = Db::name('message')->order('id asc')->paginate(20);
        return view('index', [
            'list' => $list,
        ]);
    }

    /**
     * 删除
     */
    public function delete()
    {
        $id = $this->request->get('id', 0, 'intval');
        if ($id > 0 && Db::name('message')->where('id', $id)->delete() !== false) {
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }
}