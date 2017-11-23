<?php
/**
 * Created by PhpStorm.
 * User: Y.c
 * Date: 2017/6/12
 * Time: 15:27
 */

namespace app\home\controller;

use app\common\model\Category;
use app\common\util\Tree;
use think\controller\Yar;
use think\Db;
use think\Validate;

class IndexController extends BaseController
{

    public function index()
    {
        //产品
        $category     = Category::all(['status' => 1]);
        $category_ids = Tree::getChildsId($category, 2);
        array_push($category_ids, 2);
        $article = Db::name('article')->where('category_id', 'in', $category_ids)->order('update_time desc')->limit(4)->select();

        //公司新闻
        $new1 = Db::name('article')->field('id,title,update_time')->where('category_id', 12)->order('update_time desc')->limit(4)->select();
        $new2 = Db::name('article')->field('id,title,update_time')->where('category_id', 13)->order('update_time desc')->limit(4)->select();
        return view('./template/index.html', [
            'article' => $article,
            'new1'    => $new1,
            'new2'    => $new2
        ]);
    }

    /**
     * 留言
     * @return \think\response\View
     */
    public function message()
    {
        if ($this->request->isPost()) {
            $post     = $this->request->post();
            $validate = new Validate([
                'mobile'  => 'require|is_phone',
                'name'    => 'require|max:64',
                'content' => 'require:max:255'
            ]);
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }
            $post['at_time'] = time();
            if (Db::name('message')->insert($post) > 0) {
                $this->success('留言成功');
            }
            $this->error('留言失败');
        } else {
            return view();
        }
    }
}