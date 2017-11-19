<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/11/18
 * Time: 16:15
 */

namespace app\home\controller;

use app\common\util\ConfigApi;
use app\common\util\Tree;
use think\Controller;
use think\Db;

class BaseController extends Controller
{

    public function _initialize()
    {
        parent::_initialize();
        ConfigApi::config();
        $category = Db::name('category')->where('status', 1)->where('id', 'neq', 7)->select();
        $list     = Tree::unlimitForLayer($category);
        $this->assign('__category__', $list);

        $banner = Db::name('ad')->where('type', 1)->select();
        $this->assign('banner', $banner);
    }
}