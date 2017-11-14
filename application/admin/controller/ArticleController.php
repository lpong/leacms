<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/10
 * Time: 14:14
 */

namespace app\admin\controller;

use app\admin\model\Article;
use think\Db;

class ArticleController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        $this->assign('cate', Article::$cate);
    }

    public function index()
    {
        if ($this->request->isAjax()) {
            $cate = $this->request->post('cate', 0, 'intval');
            $keyword = $this->request->post('keyword', '', 'trim');

            $model = Db::name('article')->where('status', 'in', '0,1');
            if ($cate != '') {
                $model->where('cate', $cate);
            }
            if ($keyword) {
                $model->where('title', 'like', '%' . $keyword . '%');
            }
            $list = $model->order('id desc')->paginate(10);
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
            $Article = new Article();
            $post = $this->request->post();
            $post['content'] = $_POST['content'];
            if ($Article->validate(true)->allowField(true)->save($post) === false) {
                $this->error($Article->getError());
            }
            $this->success('新增成功', url('index'));
        } else {
            return view('edit', [
                'cate' => Article::$cate,
            ]);
        }
    }

    /**
     * 修改页面和修改操作
     * @return mixed
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $Article = new Article();
            $post = $this->request->post();
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
            $info = Article::get($id);
            $this->assign('info', $info);
            $this->assign('cate', Article::$cate);
            return view();
        }
    }

    /**
     * 设置状态
     * @return json
     */
    public function setStatus()
    {
        $id = $this->request->get('id', 0, 'intval');
        $status = $this->request->get('status', 0, 'intval');

        if ($id > 0 && (new Article())->where('id', $id)->update(['status' => $status]) !== false) {
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
        if ($id > 0 && Db::name('article')->where('id', $id)->where('cate', 'neq', 1)->setField('status', 2) !== false) {
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

}