<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/11/18
 * Time: 15:43
 */

namespace app\home\controller;

use app\common\model\Category;
use app\common\util\Tree;
use think\Db;
use think\Request;

class ArticleController extends BaseController
{

    public function lists()
    {
        $id   = Request::instance()->param('id', 0, 'intval');
        $info = Db::name('category')->find($id);
        if (!$info) {
            abort(404, '内容不存在或已被删除');
        }

        $pid           = $info['pid'] == 0 ? $info['id'] : $info['pid'];
        $category_list = Db::name('category')->where('pid', $pid)->field('id,name')->select();

        if ($info['pid']) {
            $parent = Db::name('category')->find($info['pid']);
        } else {
            $parent = $info;
        }

        if ($info['type'] == Category::LIST_PAGE) {
            $category     = Category::all(['status' => 1]);
            $category_ids = (array)Tree::getChildsId($category, $info['id']);
            array_push($category_ids, $info['id']);
            $article = Db::name('article')->field('id,title,cover,update_time')->where('category_id', 'in', $category_ids)->order('update_time desc')->paginate(10);
            $this->assign('article', $article);
        }

        $template = $info['template_list'] ? $info['template_list'] : 'single';

        $this->assign('info', $info);
        $this->assign('category_list', $category_list);
        $this->assign('parent', $parent);
        return view("./template/{$template}.html",[
            'meta_title'       => $info['meta_title'],
            'meta_keyword'     => $info['meta_keyword'],
            'meta_description' => $info['meta_description'],
        ]);
    }

    public function search()
    {
        $keyword = $this->request->param('q', '', 'trim,htmlspecialchars');
        if (!$keyword) {
            abort(404, '请输入关键词');
        }
        $id   = 2;
        $info = Db::name('category')->find($id);
        if (!$info) {
            abort(404, '内容不存在或已被删除');
        }

        $pid           = $info['pid'] == 0 ? $info['id'] : $info['pid'];
        $category_list = Db::name('category')->where('pid', $pid)->field('id,name')->select();

        if ($info['pid']) {
            $parent = Db::name('category')->find($info['pid']);
        } else {
            $parent = $info;
        }

        if ($info['type'] == Category::LIST_PAGE) {
            $category     = Category::all(['status' => 1]);
            $category_ids = (array)Tree::getChildsId($category, $info['id']);
            array_push($category_ids, $info['id']);
            $article = Db::name('article')->field('id,title,cover,update_time')->where('category_id', 'in', $category_ids)->where('title', 'like', '%' . $keyword . '%')->order('update_time desc')->paginate(10);
            $this->assign('article', $article);
        }

        $template = $info['template_list'] ? $info['template_list'] : 'single';

        $this->assign('info', $info);
        $this->assign('category_list', $category_list);
        $this->assign('parent', $parent);
        return view("./template/search.html");
    }

    public function article()
    {
        $id      = Request::instance()->param('id', 0, 'intval');
        $article = Db::name('article')->find($id);
        if (!$article) {
            abort(404, '内容不存在或已被删除');
        }

        $info          = Db::name('category')->find($article['category_id']);
        $pid           = $info['pid'] == 0 ? $info['id'] : $info['pid'];
        $category_list = Db::name('category')->where('pid', $pid)->field('id,name')->select();

        if ($info['pid']) {
            $parent = Db::name('category')->find($info['pid']);
        } else {
            $parent = $info;
        }

        $template = $info['template_detail'] ? $info['template_detail'] : 'view';
        return view("./template/{$template}.html", [
            'info'             => $info,
            'article'          => $article,
            'parent'           => $parent,
            'category_list'    => $category_list,
            'meta_title'       => $article['meta_title'],
            'meta_keyword'     => $article['meta_keyword'],
            'meta_description' => $article['meta_description'],
        ]);
    }
}