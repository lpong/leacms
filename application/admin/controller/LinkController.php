<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/11/18
 * Time: 14:45
 */

namespace app\admin\controller;

use think\Db;

class LinkController extends CommonController
{
    public function index()
    {
        $list = Db::name('link')->order('id asc')->select();
        return view('index', [
            'list' => $list,
        ]);
    }

    /**
     * 添加页面和添加操作
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            if (Db::name('link')->insert($this->request->post())) {
                $this->success('新增成功', url('index'));
            }
            $this->error('新增失败');
        } else {
            return view('edit');
        }
    }

    /**
     * 修改页面和修改操作
     * @return mixed
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            if (Db::name('link')->where('id',$this->request->post('id'))->update($this->request->post())) {
                $this->success('修改成功', url('index'));
            }
            $this->success('修改失败', url('index'));
        } else {
            $id = $this->request->get('id', 0, 'intval');
            if (!$id) {
                $this->error('内容不存在');
            }
            $info = Db::name('link')->find($id);
            $this->assign('info', $info);
            return view();
        }
    }

    /**
     * 删除
     */
    public function delete()
    {
        $id = $this->request->get('id', 0, 'intval');
        if ($id > 0 && Db::name('link')->where('id', $id)->delete() !== false) {
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }
}