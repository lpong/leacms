<?php
/**
 * Created by PhpStorm.
 * User: Y.c
 * Date: 2017/6/20
 * Time: 14:53
 */

namespace app\admin\controller;

use app\common\logic\DealRecord;
use app\common\logic\Study;
use app\v1\logic\BalanceRecord;
use app\v1\model\User;
use think\Db;
use think\Request;

class UserController extends CommonController
{

    public function index()
    {
        return view();
    }

    public function lists()
    {
        $type       = $this->request->post('type', 0, 'intval');
        $keyword    = $this->request->post('keyword', '', 'trim');
        $range_time = $this->request->post('range_time', '', 'trim');   //开始注册时间

        $userModel = Db::name('user')->order('id desc');
        if ($keyword) {
            if (is_phone($keyword)) {
                $userModel->where('mobile', $keyword);
            } else {
                $userModel->where('nickname', 'like', '%' . $keyword . '%');
            }
        }
        //时间
        if ($range_time) {
            $range_time = range_time($range_time, false);
            $userModel->where('register_time', 'between', [$range_time[0], $range_time[1]]);
        }
        $users = $userModel->order('id desc')->paginate(20);

        $list = $users->getCollection()->toArray();
        $page = $users->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        return view();
    }

    /**
     * 快速禁用
     * @return json
     */
    public function setStatus()
    {
        $id     = $this->request->get('id', 0, 'intval');
        $status = $this->request->get('status', 0, 'intval');

        if ($id > 0 && Db::name('user')->where('id', $id)->setField('status', $status) > 0) {
            if ($status != 1) {
                cache('user:token:' . $id, null);
                User::clearUserInfoCache($id);
            }
            $this->success('设置成功');
        }

        $this->error('更新失败');
    }


    //余额明细
    public function balance()
    {
        if ($this->request->isPost()) {
            $user_id = $this->request->post('user_id', 0, 'intval');
            $keyword = $this->request->post('keyword', '', 'trim');
            $cate    = $this->request->post('cate', '', 'trim');

            $model = Db::name('deal_record')->order('id desc');
            if ($user_id) {
                $model->where('user_id', $user_id);
            }
            if ($keyword) {
                $model->where('order_no', $keyword);
            }

            if ($cate) {
                $model->where('cate', $cate);
            }

            $record = $model->where('status', 1)->paginate(15);

            return view('balance_list', [
                'record'  => $record,
                'cate'    => DealRecord::$typeName,
                'payment' => DealRecord::$payments
            ]);

        } else {
            return view();
        }
    }


}