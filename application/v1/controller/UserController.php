<?php
/**
 * Created by PhpStorm.
 * User: Y.c
 * Date: 2017/6/20
 * Time: 11:23
 */

namespace app\v1\controller;

use app\common\logic\Collection;
use app\common\logic\DealRecord;
use app\common\logic\Study;
use app\common\logic\Zan;
use app\common\util\Qiniu;
use app\v1\model\User;
use app\common\controller\ApiController;
use lea\Y;
use think\Db;
use think\Request;

class UserController extends ApiController
{

    /**
     * 个人信息
     * @return mixed
     */
    public function info()
    {
        $info = Db::name('user')->where('id', Y::$identity->id)->find();
        unset($info['password']);
        $info['face'] = Y::qiNiuResource($info['face'], 'file', 'imageView2/2/w/120/h/120/interlace/0/q/100');

        //累计学习时间
        $studyLogic = new Study(Y::$identity->id);
        $info['total_study_time'] = $studyLogic->getTotalStudyTime(true);
        $info['continuity_study_day'] = $studyLogic->getContinuityStudyDay();

        return Y::json($info);
    }


    /**
     * 退出登陆
     * @return mixed
     */
    public function logout()
    {
        $user_id = Y::$identity->id;
        if ($user_id) {
            cache('user:token:' . $user_id);
            User::clearUserInfoCache(Y::$identity->id); //清楚用户信息缓存
        }
        return Y::json();
    }

    /**
     * 获取上次级修改资料所需信息
     * @return mixed
     */
    public function getAuth()
    {
        $res['token'] = (new Qiniu())->getToken('file');
        $res['upload_url'] = config('upload.UploadUrl');

        $res['occupation'] = ['在校学生', '政府/机关干部/公务员', '企业管理者', '普通职员', '专业人员', '普通工人', '商业服务业职工', '个体经营者/承包商', '自由职业者', '农林牧渔劳动者', '退休', '暂无职业', '其他'];

        return Y::json($res);
    }

    //修改用户信息
    public function editInfo()
    {
        $post = Request::instance()->only(['nickname', 'face', 'sex', 'occupation'], 'post');
        if (!$post) {
            return Y::json(102, '参数错误');
        }
        foreach ($post as &$val) {
            if (empty($val)) {
                unset($val);
            }
        }
        if (Db::name('user')->where('id', Y::$identity->id)->update($post) !== false) {
            return Y::json('修改成功');
        }
        return Y::json(102, '修改失败');
    }

    //获取我的勋章
    public function medal()
    {
        //我的学习总天数
        $days = (new Study(Y::$identity->id))->getTotalStudyDay();
        $medal = config('other.medal');
        $my = [];
        foreach ($medal as $val) {
            if ($days > $val) {
                array_push($my, $val);
            }
        }

        return Y::json(['medal' => implode($my)]);
    }

    //收藏
    public function collect(Request $request)
    {
        $cate = $request->post('cate', '', 'trim');
        $id = $request->post('id', 0, 'intval');
        $status = $request->post('status', 0, 'intval');

        if (!in_array($cate, ['course', 'special_zone', 'free_zone', 'ting']) || $id <= 0 || !in_array($status, [1, 2])) {
            return Y::json(101, '参数错误');
        }

        if ($status == 1) {
            $data = [
                'user_id'  => Y::$identity->id,
                'cate'     => $cate,
                'audio_id' => $id,
                'at_time'  => time(),
            ];
            if (Db::name('user_collection')->where('user_id', Y::$identity->id)->where('cate', $cate)->where('audio_id', $id)->find()) {
                return Y::json(104, '您已经收藏该音频了');
            }

            if (Db::name('user_collection')->insert($data) > 0) {
                (new Collection(Y::$identity->id))->updateCollectionIds($cate);
                return Y::json('收藏成功');
            }
            return Y::json(105, '收藏失败');
        } else {
            if (Db::name('user_collection')->where('user_id', Y::$identity->id)->where('cate', $cate)->where('audio_id', $id)->delete() !== false) {
                (new Collection(Y::$identity->id))->updateCollectionIds($cate);
                return Y::json('取消成功');
            }
            return Y::json(105, '取消失败');
        }
    }

    //收藏
    public function zan(Request $request)
    {
        $cate = $request->post('cate', '', 'trim');
        $id = $request->post('id', 0, 'intval');
        $status = $request->post('status', 0, 'intval');

        if (!in_array($cate, ['course', 'special_zone', 'free_zone', 'ting']) || $id <= 0 || !in_array($status, [1, 2])) {
            return Y::json(101, '参数错误');
        }

        if ($status == 1) {
            $data = [
                'user_id'  => Y::$identity->id,
                'cate'     => $cate,
                'audio_id' => $id,
                'at_time'  => time(),
            ];
            if (Db::name('user_zan')->where('user_id', Y::$identity->id)->where('cate', $cate)->where('audio_id', $id)->find()) {
                return Y::json(104, '您已经点赞该音频了');
            }

            if (Db::name('user_zan')->insert($data) > 0) {
                (new Zan(Y::$identity->id))->updateZanIds($cate);
                return Y::json('点赞成功');
            }
            return Y::json(105, '点赞失败');
        } else {
            if (Db::name('user_zan')->where('user_id', Y::$identity->id)->where('cate', $cate)->where('audio_id', $id)->delete() !== false) {
                (new Zan(Y::$identity->id))->updateZanIds($cate);
                return Y::json('取消成功');
            }
            return Y::json(105, '取消失败');
        }
    }

    //我的收藏
    public function myCollection()
    {
        $collection = Db::name('user_collection')->where('user_id', Y::$identity->id)->order('id desc')->paginate(10);
        $list = $collection->getCollection()->toArray();
        if ($list) {
            foreach ($list as $key => &$val) {
                $temp_info = Db::name($val['cate'] . '_audio')->find($val['audio_id']);
                if (!$temp_info) {
                    Db::name('user_collection')->delete($val['id']);
                    unset($list[$key]);
                    continue;
                }
                $val['title'] = $temp_info['title'];
                $val['time'] = $temp_info['time'];
                $val['cate_name'] = Db::name($val['cate'])->where('id', $temp_info[$val['cate'] . '_id'])->value('title');
                if ($val['cate_name'] === null) {
                    Db::name('user_collection')->delete($val['id']);
                    unset($list[$key]);
                }
            }
        }

        return Y::json([
            'list'     => $list,
            'pageinfo' => [
                'total'        => $collection->total(),
                'list_rows'    => $collection->listRows(),
                'current_page' => $collection->currentPage(),
                'last_page'    => $collection->lastPage(),
            ],
        ]);
    }

    //接触微信绑定
    public function unbind()
    {
        if (Db::name('user')->where('id', Y::$identity->id)->setField('openid', null) !== false) {
            return Y::json('解绑成功');
        }
        return Y::json(102, '解绑失败');
    }

    //写留言
    public function comment()
    {
        $message = Request::instance()->post('message', '', 'trim');
        $special_zone_id = Request::instance()->post('special_zone_id', '', 'trim');
        if (!$message || !$special_zone_id) {
            return Y::json(102, '参数错误');
        }
        $data = [
            'special_zone_id' => $special_zone_id,
            'user_id'         => Y::$identity->id,
            'message'         => $message,
            'at_time'         => time(),
        ];
        if (Db::name('special_zone_comment')->insert($data) > 0) {
            return Y::json('留言成功');
        }
        return Y::json(102, '留言失败');
    }

    //余额记录
    public function balanceRecord()
    {
        $record = Db::name('deal_record')->where('user_id', Y::$identity->id)->where('status', 1)
            ->where(function ($query) {
                $query->where('type', 'recharge')->whereOr('type=\'order\' and payment=\'balance\'');
            })->order('id desc')->paginate(10);

        $list = $record->getCollection()->toArray();
        if ($list) {
            $temp = [];
            $cate = config('other.cate_name');
            $type = DealRecord::$typeName;
            $payment = DealRecord::$payments;
            foreach ($list as $val) {
                $data = json_decode($val['data'], true);
                if ($val['type'] == 'recharge') {
                    $title = $type[$val['type']];
                } else {
                    if (isset($cate[$data['cate']]) && isset($data['title'])) {
                        $title = '购买' . $cate[$data['cate']] . '<<' . $data['title'] . '>>';
                    } else {
                        $title = '购买';
                    }
                }
                array_push($temp, [
                    'title'  => $title,
                    'change' => $val['change'],
                    'date'   => date('Y-m-d', $val['at_time']),
                ]);
            }
            $list = $temp;
        }

        return Y::json([
            'list'     => $list,
            'pageinfo' => [
                'total'        => $record->total(),
                'list_rows'    => $record->listRows(),
                'current_page' => $record->currentPage(),
                'last_page'    => $record->lastPage(),
            ],
        ]);
    }


    //获取消息
    public function msg()
    {
        $msgM = Db::name('msg')->where(function ($query) {
            $query->where('user_id', 0)->where('at_time', 'gt', strtotime(Y::$identity->register_time));
        })->whereOr('user_id', Y::$identity->id)->order('id desc')->paginate(10);
        //设置已读
        Db::name('msg')->where('user_id', Y::$identity->id)->where('is_read', 0)->setField('is_read', 1);
        $list = $msgM->getCollection()->toArray();
        if ($list) {
            foreach ($list as &$val) {
                $val['at_time'] = date('m/d H:i', $val['at_time']);
                $temp = json_decode($val['data'], true);
                $val['data'] = $temp ? $temp : (new \stdClass());
            }
        }

        return Y::json([
            'list'     => $list,
            'pageinfo' => [
                'total'        => $msgM->total(),
                'list_rows'    => $msgM->listRows(),
                'current_page' => $msgM->currentPage(),
                'last_page'    => $msgM->lastPage(),
            ],
        ]);
    }

}
