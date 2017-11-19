<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/11/14
 * Time: 14:16
 */

namespace app\admin\controller;

use app\common\model\Category;
use app\common\util\Tree;
use think\Db;

class CategoryController extends CommonController
{
    public function index()
    {
        $list = Db::name('category')->order('pid asc,sort desc')->select();
        $list = Tree::unlimitForLevel($list, '├─', 0);
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
            $Article         = new Category();
            $post            = $this->request->post();
            $post['content'] = $_POST['content'];
            if ($Article->validate(true)->allowField(true)->save($post) === false) {
                $this->error($Article->getError());
            }
            $this->success('新增成功', url('index'));
        } else {
            $category = Category::all();
            $category = Tree::unlimitForLevel($category);
            $this->assign('category', $category);
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
            $Article         = new Category();
            $post            = $this->request->post();
            $post['content'] = $_POST['content'];
            if ($Article->validate(true)->isUpdate(true)->allowField(true)->save($post) === false) {
                $this->error($Article->getError());
            }
            $this->success('修改成功', url('index'));
        } else {
            $id = $this->request->get('id', 0, 'intval');
            if (!$id) {
                $this->error('文章不存在');
            }
            $info = Category::get($id);
            $this->assign('info', $info);
            $category = Category::all();
            $category = Tree::unlimitForLevel($category);
            $this->assign('category', $category);
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

        if ($id > 0 && (new Category())->where('id', $id)->update(['status' => $status]) !== false) {
            $this->success('设置成功');
        }
        $this->error('更新失败');
    }


    /**
     * 删除
     */
    public function delete()
    {
        $id = $this->request->get('id', 0, 'intval');
        if ($id > 0 && Db::name('article')->where('id', $id)->setField('status', 2) !== false) {
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }
}