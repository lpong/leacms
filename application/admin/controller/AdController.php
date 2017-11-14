<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/10
 * Time: 14:14
 */

namespace app\admin\controller;

use app\admin\model\Ad;
use think\Db;

class AdController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        $this->assign('type', Ad::$adType);
        $this->assign('action_type', Ad::$actionType);
    }

    public function index()
    {
        if ($this->request->isAjax()) {
            $type = $this->request->post('type', 0, 'intval');

            $model = Db::name('ad')->where('status', 'in', '0,1');
            if ($type) {
                $model->where('type', $type);
            }
            $list = $model->order('sort asc,id desc')->paginate(10);
            return view('index_list', [
                'list' => $list,
            ]);
        } else {
            return view();
        }
    }

    /**
     * 添加页面和添加操作
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $Article = new Ad();
            if ($Article->validate(true)->allowField(true)->save($this->request->post()) === false) {
                $this->error($Article->getError());
            }
            $this->success('新增成功', url('index'));
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
            $Article = new Ad();
            if ($Article->validate(true)->isUpdate(true)->allowField(true)->save($this->request->post()) === false) {
                $this->error($Article->getError());
            }
            $this->success('修改成功', url('index'));
        } else {
            $id = $this->request->get('id', 0, 'intval');
            if (!$id) {
                $this->error('信息不存在');
            }
            $info = Ad::get($id);
            $this->assign('info', $info);
            return view();
        }
    }

    /**
     * 设置状态
     * @return json
     */
    public function setStatus()
    {
        $id     = $this->request->get('id', 0, 'intval');
        $status = $this->request->get('status', 0, 'intval');

        if ($id > 0 && (new Ad())->where('id', $id)->where('status', 'neq', 2)->update(['status' => $status]) !== false) {
            $this->success('更新成功');
        }
        $this->error('更新失败');
    }

    /**
     * 排序
     * @return json
     */
    public function sort()
    {
        $id   = $this->request->get('id', 0, 'intval');
        $sort = $this->request->post('sort', 0, 'intval');

        if ($id > 0 && (new Ad())->where('id', $id)->update(['sort' => $sort]) !== false) {
            $this->success('更新成功');
        }
        $this->error('更新失败');
    }


    /**
     * 删除
     */
    public function delete()
    {
        $id = $this->request->get('id', 0, 'intval');
        if ($id > 0 && Db::name('ad')->where('id', $id)->setField('status', 2) !== false) {
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

}