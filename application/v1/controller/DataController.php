<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/16
 * Time: 10:01
 */

namespace app\v1\controller;

use app\common\controller\ApiController;
use app\common\logic\Collection;
use app\common\logic\Resource;
use app\common\logic\Zan;
use app\common\model\Course;
use app\common\model\CourseAudio;
use app\common\model\FreeZoneAudio;
use app\common\model\SpecialZone;
use app\common\model\SpecialZoneAudio;
use app\common\model\Ting;
use lea\Y;
use think\Db;
use think\Request;

class DataController extends ApiController
{
    protected $zan;
    protected $collection;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->zan        = new Zan(Y::app()->getUserId());
        $this->collection = new Collection(Y::app()->getUserId());
    }

    /**
     * 列表，搜索
     * @param Request $request
     * @return mixed
     */
    public function lists(Request $request)
    {
        $_type                 = $request->post('type', 1, 'intval');
        $free_zone             = Db::name('free_zone')->find(1);
        $free_zone['thumbpic'] = Y::qiNiuResource($free_zone['thumbpic'], 'file');
        $free_zone['picture']  = Y::qiNiuResource($free_zone['picture'], 'file');
        $list                  = Db::name('free_zone_audio')->where('type', $_type)->where('status', 1)->order('serial_number asc,id desc')->paginate(3);
        $list                  = $list->getCollection()->toArray();
        if ($list) {
            $type = FreeZoneAudio::$autoType;
            foreach ($list as &$val) {
                $val['type']          = isset($type[$val['type']]) ? $type[$val['type']] : '';
                $val['is_zan']        = $this->zan->isZan('free_zone', $val['id']);
                $val['is_collection'] = $this->collection->isCollection('free_zone', $val['id']);
                $val['src']           = Y::qiNiuResource($val['resource'], 'audio');
                $val['publish_time']  = date('Y-m-d', $val['publish_time']);
            }
        }
        $free_zone['lists'] = $list;

        //专栏
        $special_zone = Db::name('special_zone')->where("FIND_IN_SET($_type,type)")->where('status', 1)->limit(3)->order('update_time desc')->select();
        if ($special_zone) {
            $type      = SpecialZone::$autoType;
            $audioType = SpecialZoneAudio::$autoType;
            foreach ($special_zone as &$val) {
                $val['type']       = '面向·' . str2name($val['type'], $type);
                $val['fee']        = floatval($val['fee']);
                $val['thumbpic']   = Y::qiNiuResource($val['thumbpic'], 'file');
                $last_update_audio = Db::name('special_zone_audio')->where('special_zone_id', $val['id'])->where('need_charge', 1)->where('status', 1)->order('serial_number desc,id desc')->find();
                if ($last_update_audio) {
                    $last_update_audio['type'] = isset($audioType[$last_update_audio['type']]) ? $audioType[$last_update_audio['type']] : '';
                    $val['last_update_audio']  = $last_update_audio;
                } else {
                    $val['last_update_audio'] = (new \stdClass());
                }
            }
        }


        //听书
        $ting = Db::name('ting')->where('type', $_type)->where('status', 1)->limit(3)->order('update_time desc')->select();
        if ($ting) {
            $type = Ting::$autoType;
            foreach ($ting as &$val) {
                $val['type']       = isset($type[$val['type']]) ? $type[$val['type']] : '';
                $val['fee']        = floatval($val['fee']);
                $val['thumbpic']   = Y::qiNiuResource($val['thumbpic'], 'file');
                $val['audio_num']  = Db::name('ting_audio')->where('ting_id', $val['id'])->where('need_charge', 1)->where('status', 1)->count();
                $val['audio_time'] = Db::name('ting_audio')->where('ting_id', $val['id'])->where('need_charge', 1)->where('status', 1)->sum('time');
                $val['buy_num']    = Db::name('user_resource')->where('cate', 'ting')->where('data_id', $val['id'])->count();
            }
        }

        return Y::json([
            'free_zone'    => $free_zone,
            'special_zone' => $special_zone,
            'ting'         => $ting,
        ]);

    }

    /**
     * 列表，搜索
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request)
    {
        $cate    = $request->post('cate', '');
        $page    = $request->post('page', 1, 'intval');
        $num     = $request->post('num', 3, 'intval');
        $keyword = $request->post('keyword', '');
        $words1  = ['幼儿' => 1, '小学' => 2, '初中' => 3, '高中' => 4];
        $words2  = ['专家' => 1, '成功家长' => 2, '学霸' => 3];

        if (!$keyword) {
            return Y::json(102, '请输入关键词搜索');
        }
        $type1 = isset($words1[$keyword]) ? $words1[$keyword] : 0;
        $type2 = isset($words2[$keyword]) ? $words2[$keyword] : 0;

        if (!$cate || $cate == 'free_zone') {
            $free_zone             = Db::name('free_zone')->find(1);
            $free_zone['thumbpic'] = Y::qiNiuResource($free_zone['thumbpic'], 'file');
            $free_zone['picture']  = Y::qiNiuResource($free_zone['picture'], 'file');
            $list                  = Db::name('free_zone_audio')->where(function ($query) use ($keyword, $type1) {
                $query->where('type', $type1)->whereOr('title', 'like', '%' . $keyword . '%');
            })->where('status', 1)->order('serial_number asc,id desc')->paginate($num);
            $list                  = $list->getCollection()->toArray();
            if ($list) {
                $type = FreeZoneAudio::$autoType;
                foreach ($list as &$val) {
                    $val['type']          = isset($type[$val['type']]) ? $type[$val['type']] : '';
                    $val['is_zan']        = $this->zan->isZan('free_zone', $val['id']);
                    $val['is_collection'] = $this->collection->isCollection('free_zone', $val['id']);
                    $val['src']           = Y::qiNiuResource($val['resource'], 'audio');
                    $val['publish_time']  = date('Y-m-d', $val['publish_time']);
                }
            }
            $free_zone['lists'] = $list;
        }


        //专栏
        if (!$cate || $cate == 'special_zone') {
            $special_zone = Db::name('special_zone')->where(function ($query) use ($keyword, $type1) {
                $query->where("FIND_IN_SET($type1,type)")->whereOr('title', 'like', '%' . $keyword . '%');
            })->where('status', 1)->order('update_time desc')->paginate($num);
            $special_zone = $special_zone->getCollection()->toArray();
            if ($special_zone) {
                $type      = SpecialZone::$autoType;
                $audioType = SpecialZoneAudio::$autoType;
                foreach ($special_zone as &$val) {
                    $val['type']       = str2name($val['type'], $type);
                    $val['fee']        = floatval($val['fee']);
                    $val['thumbpic']   = Y::qiNiuResource($val['thumbpic'], 'file');
                    $last_update_audio = Db::name('special_zone_audio')->where('special_zone_id', $val['id'])->where('need_charge', 1)->where('status', 1)->order('serial_number desc,id desc')->find();
                    if ($last_update_audio) {
                        $last_update_audio['type'] = isset($audioType[$last_update_audio['type']]) ? $audioType[$last_update_audio['type']] : '';
                        $val['last_update_audio']  = $last_update_audio;
                    } else {
                        $val['last_update_audio'] = (new \stdClass());
                    }
                }
            }
        }


        //精品课
        if (!$cate || $cate == 'course') {
            $course = Db::name('course')->where(function ($query) use ($keyword, $type2) {
                $query->where('type', $type2)->whereOr('title', 'like', '%' . $keyword . '%')->where('author', $keyword);
            })->where('status', 1)->order('update_time desc')->paginate($num);
            $course = $course->getCollection()->toArray();
            if ($course) {
                $type = Course::$autoType;
                foreach ($course as &$val) {
                    $val['type']       = isset($type[$val['type']]) ? $type[$val['type']] : '';
                    $val['fee']        = floatval($val['fee']);
                    $val['thumbpic']   = Y::qiNiuResource($val['thumbpic'], 'file');
                    $val['buy_num']    = Db::name('course_audio')->where('course_id', $val['id'])->where('need_charge', 1)->where('status', 1)->count();
                    $val['audio_time'] = Db::name('course_audio')->where('course_id', $val['id'])->where('need_charge', 1)->where('status', 1)->sum('time');
                }
            }
        }


        //听书
        if (!$cate || $cate == 'ting') {
            $ting = Db::name('ting')->where(function ($query) use ($keyword, $type1) {
                $query->where('type', $type1)->whereOr('title', 'like', '%' . $keyword . '%');
            })->where('status', 1)->order('update_time desc')->paginate($num);
            $ting = $ting->getCollection()->toArray();
            if ($ting) {
                $type = Ting::$autoType;
                foreach ($ting as &$val) {
                    $val['type']       = isset($type[$val['type']]) ? $type[$val['type']] : '';
                    $val['fee']        = floatval($val['fee']);
                    $val['thumbpic']   = Y::qiNiuResource($val['thumbpic'], 'file');
                    $val['buy_num']    = Db::name('ting_audio')->where('ting_id', $val['id'])->where('need_charge', 1)->where('status', 1)->count();
                    $val['audio_time'] = Db::name('ting_audio')->where('ting_id', $val['id'])->where('need_charge', 1)->where('status', 1)->sum('time');
                }
            }
        }

        return Y::json([
            'free_zone'    => isset($free_zone) ? $free_zone : (new \stdClass()),
            'special_zone' => isset($special_zone) ? $special_zone : [],
            'ting'         => isset($ting) ? $ting : [],
            'course'       => isset($course) ? $course : [],
        ]);

    }

    //免费详情
    public function free_zone(Request $request)
    {
        $page = $request->post('page', 1, 'intval');
        $type = $request->post('type', 0, 'intval');

        if ($page == 1) {
            $free_zone             = Db::name('free_zone')->find(1);
            $free_zone['thumbpic'] = Y::qiNiuResource($free_zone['thumbpic'], 'file');
            $free_zone['picture']  = Y::qiNiuResource($free_zone['picture'], 'file');
        } else {
            $free_zone = new \stdClass();
        }

        $free_zone_model = Db::name('free_zone_audio');
        if ($type) {
            $free_zone_model->where('type', $type);
        }
        $free_zone_list = $free_zone_model->where('status', 1)->order('serial_number asc,id desc')->paginate(10);
        $lists          = $free_zone_list->getCollection()->toArray();

        if ($lists) {
            $type = FreeZoneAudio::$autoType;
            foreach ($lists as &$val) {
                $val['src']           = Y::qiNiuResource($val['resource'], 'audio');
                $val['type']          = isset($type[$val['type']]) ? $type[$val['type']] : '';
                $val['publish_time']  = date('Y-m-d', $val['publish_time']);
                $val['is_zan']        = $this->zan->isZan('free_zone', $val['id']);
                $val['is_collection'] = $this->collection->isCollection('free_zone', $val['id']);
            }
        }

        return Y::json([
            'free_zone' => $free_zone,
            'lists'     => $lists,
            'pageinfo'  => [
                'total'        => $free_zone_list->total(),
                'list_rows'    => $free_zone_list->listRows(),
                'current_page' => $free_zone_list->currentPage(),
                'last_page'    => $free_zone_list->lastPage(),
            ],
        ]);
    }

    //全部专栏
    public function special_zone(Request $request)
    {
        $type         = $request->post('type', 1, 'intval');
        $special_zone = Db::name('special_zone')->where("FIND_IN_SET($type,type)")->where('status', 1)->order('update_time desc')->paginate(10);
        $list         = $special_zone->getCollection()->toArray();
        if ($list) {
            $type      = SpecialZone::$autoType;
            $audioType = SpecialZoneAudio::$autoType;
            foreach ($list as &$val) {
                $val['type']       = str2name($val['type'], $type);
                $val['fee']        = floatval($val['fee']);
                $val['thumbpic']   = Y::qiNiuResource($val['thumbpic'], 'file');
                $last_update_audio = Db::name('special_zone_audio')->where('special_zone_id', $val['id'])->where('need_charge', 1)->where('status', 1)->order('serial_number desc,id desc')->find();
                if ($last_update_audio) {
                    $last_update_audio['type'] = isset($audioType[$last_update_audio['type']]) ? $audioType[$last_update_audio['type']] : '';
                    $val['last_update_audio']  = $last_update_audio;
                } else {
                    $val['last_update_audio'] = (new \stdClass());
                }
            }
        }
        return Y::json([
            'lists'    => $list,
            'pageinfo' => [
                'total'        => $special_zone->total(),
                'list_rows'    => $special_zone->listRows(),
                'current_page' => $special_zone->currentPage(),
                'last_page'    => $special_zone->lastPage(),
            ],
        ]);

    }

    //全部专栏详情
    public function special_zone_detail()
    {
        $id     = Request::instance()->post('id', 0, 'intval');
        $detail = Db::name('special_zone')->find($id);
        if (!$detail || $detail['status'] != 1) {
            return Y::json(101, '信息不存在');
        }

        $detail['type']     = str2name($detail['type'], SpecialZone::$autoType);
        $detail['thumbpic'] = Y::qiNiuResource($detail['thumbpic'], 'file');
        $detail['picture']  = Y::qiNiuResource($detail['picture'], 'file');

        //免费课程
        $free_audio = Db::name('special_zone_audio')->where('special_zone_id', $id)->where('need_charge', 0)->where('status', 1)->order('id desc')->select();

        $audio = Db::name('special_zone_audio')->where('special_zone_id', $id)->where('need_charge', 1)->where('status', 1)->order('serial_number desc,id desc')->select();

        $audioType = SpecialZoneAudio::$autoType;
        if ($free_audio) {
            foreach ($free_audio as &$val) {
                $val['type']          = isset($audioType[$val['type']]) ? $audioType[$val['type']] : '';
                $val['src']           = Y::qiNiuResource($val['resource'], 'audio');
                $val['publish_time']  = date('Y-m-d', $val['publish_time']);
                $val['is_zan']        = $this->zan->isZan('special_zone', $val['id']);
                $val['is_collection'] = $this->collection->isCollection('special_zone', $val['id']);
            }
        }

        if ($audio) {
            foreach ($audio as &$val) {
                $val['type']          = isset($audioType[$val['type']]) ? $audioType[$val['type']] : '';
                $val['publish_time']  = date('Y-m-d', $val['publish_time']);
                $val['src']           = '';
                $val['is_zan']        = $this->zan->isZan('special_zone', $val['id']);
                $val['is_collection'] = $this->collection->isCollection('special_zone', $val['id']);

            }
        }

        $is_buy = (new Resource(Y::app()->getUserId()))->isBuy('special_zone', $id);

        return Y::json([
            'detail'           => $detail,
            'free_audio'       => $free_audio,
            'recently_updated' => $audio,
            'is_buy'           => $is_buy,
        ]);

    }

    /**
     * 全部精品课
     * @return mixed
     */
    public function course()
    {
        $type   = Request::instance()->post('type', 1, 'intval');
        $course = Db::name('course')->where('type', $type)->where('status', 1)->order('update_time desc')->paginate(10);
        $list   = $course->getCollection()->toArray();
        if ($list) {
            $type = Course::$autoType;
            foreach ($list as &$val) {
                $val['type']       = isset($type[$val['type']]) ? $type[$val['type']] : '';
                $val['fee']        = floatval($val['fee']);
                $val['thumbpic']   = Y::qiNiuResource($val['thumbpic'], 'file');
                $val['buy_num']    = Db::name('course_audio')->where('course_id', $val['id'])->where('need_charge', 1)->where('status', 1)->count();
                $val['audio_num']  = Db::name('course_audio')->where('course_id', $val['id'])->where('need_charge', 1)->where('status', 1)->count();
                $val['audio_time'] = Db::name('course_audio')->where('course_id', $val['id'])->where('need_charge', 1)->where('status', 1)->sum('time');
            }
        }

        return Y::json([
            'lists'    => $list,
            'pageinfo' => [
                'total'        => $course->total(),
                'list_rows'    => $course->listRows(),
                'current_page' => $course->currentPage(),
                'last_page'    => $course->lastPage(),
            ],
        ]);
    }

    //全部专栏详情
    public function course_detail()
    {
        $id     = Request::instance()->post('id', 0, 'intval');
        $detail = Db::name('course')->find($id);
        if (!$detail || $detail['status'] != 1) {
            return Y::json(101, '信息不存在');
        }

        $detail['type']     = isset(Course::$autoType[$detail['type']]) ? Course::$autoType[$detail['type']] : '';
        $detail['thumbpic'] = Y::qiNiuResource($detail['thumbpic'], 'file');
        $detail['picture']  = Y::qiNiuResource($detail['picture'], 'file');

        $audio = Db::name('course_audio')->where('course_id', $id)->where('status', 1)->order('need_charge asc,serial_number desc,id desc')->select();
        if ($audio) {
            foreach ($audio as &$val) {
                if ($val['need_charge'] == 0) {
                    $val['src'] = Y::qiNiuResource($val['resource'], 'audio');
                } else {
                    $val['src'] = '';
                }
                $val['is_zan']        = $this->zan->isZan('course', $val['id']);
                $val['is_collection'] = $this->collection->isCollection('course', $val['id']);
                $val['publish_time']  = date('Y-m-d', $val['publish_time']);
            }
        }
        $is_buy = (new Resource(Y::app()->getUserId()))->isBuy('course', $id);
        return Y::json([
            'detail' => $detail,
            'audio'  => $audio,
            'is_buy' => $is_buy,
        ]);

    }

    public function ting()
    {
        $type = Request::instance()->post('type', 1, 'intval');
        //听书
        $ting = Db::name('ting')->where('type', $type)->where('status', 1)->order('update_time desc')->paginate(10);
        $list = $ting->getCollection()->toArray();
        if ($list) {
            $type = Ting::$autoType;
            foreach ($list as &$val) {
                $val['type']       = isset($type[$val['type']]) ? $type[$val['type']] : '';
                $val['fee']        = floatval($val['fee']);
                $val['thumbpic']   = Y::qiNiuResource($val['thumbpic'], 'file');
                $val['buy_num']    = Db::name('user_resource')->where('cate', 'ting')->where('data_id', $val['id'])->count();
                $val['audio_num']  = Db::name('ting_audio')->where('ting_id', $val['id'])->where('need_charge', 1)->where('status', 1)->count();
                $val['audio_time'] = Db::name('ting_audio')->where('ting_id', $val['id'])->where('need_charge', 1)->where('status', 1)->sum('time');
            }
        }

        return Y::json([
            'lists'    => $list,
            'pageinfo' => [
                'total'        => $ting->total(),
                'list_rows'    => $ting->listRows(),
                'current_page' => $ting->currentPage(),
                'last_page'    => $ting->lastPage(),
            ],
        ]);
    }

    //全部专栏详情
    public function ting_detail()
    {
        $id     = Request::instance()->post('id', 0, 'intval');
        $detail = Db::name('ting')->find($id);
        if (!$detail || $detail['status'] != 1) {
            return Y::json(101, '信息不存在');
        }

        $detail['thumbpic'] = Y::qiNiuResource($detail['thumbpic'], 'file');

        $detail['type'] = isset(Course::$autoType[$detail['type']]) ? Course::$autoType[$detail['type']] : '';

        //正式
        $audio = Db::name('ting_audio')->where('ting_id', $id)->where('status', 1)->order('need_charge asc,serial_number desc,id desc')->select();

        foreach ($audio as &$val) {
            if ($val['need_charge'] == 0) {
                $val['src'] = Y::qiNiuResource($val['resource'], 'audio');
            } else {
                $val['src'] = '';
            }
            $val['is_zan']        = $this->zan->isZan('ting', $val['id']);
            $val['is_collection'] = $this->collection->isCollection('ting', $val['id']);
            $val['publish_time']  = date('Y-m-d', $val['publish_time']);
        }

        $is_buy = (new Resource(Y::app()->getUserId()))->isBuy('ting', $id);

        return Y::json([
            'detail' => $detail,
            'audio'  => $audio,
            'is_buy' => $is_buy,
        ]);
    }

    //音频详情接口
    public function audio_detail(Request $request)
    {
        $cate = $request->post('cate', '', 'trim');
        $id   = $request->post('id', 0, 'intval');
        if (!in_array($cate, ['course', 'special_zone', 'free_zone', 'ting']) || $id <= 0) {
            return Y::json(101, '参数错误');
        }

        $detail = Db::name($cate . '_audio')->find($id);

        if ($detail['status'] != 1) {
            return Y::json(101, '该音频不存在或已被删除');
        }

        $detail['cate'] = $cate;

        if ($cate !== 'free_zone' && $detail['need_charge'] == 1) {
            if (empty(Y::$identity)) {
                return Y::json(101, '请您先登录');
            }
            if (!(new Resource(Y::app()->getUserId()))->isBuy($cate, $detail[$cate . '_id'])) {
                return Y::json(101, '您还未购买该音频');
            }
        }


        //音频链接
        $detail['src'] = Y::qiNiuResource($detail['resource'], 'audio');

        //是否已点赞,已收藏
        if (Y::$identity) {
            $is_zan  = Db::name('user_zan')->where('user_id', Y::$identity->id)->where('cate', $cate)->where('audio_id', $id)->value('id');
            $is_coll = Db::name('user_collection')->where('user_id', Y::$identity->id)->where('cate', $cate)->where('audio_id', $id)->value('id');
        }
        $detail['is_zan']        = $this->zan->isZan($cate, $id);
        $detail['is_collection'] = $this->collection->isCollection($cate, $id);

        //总共多少个
        //$detail['total_audio'] = Db::name($cate . '_audio')->where($cate . '_id', $detail[$cate . '_id'])->where('need_charge', $detail['need_charge'])->where('status', 1)->count();

        //缩略图
        $detail['thumbpic'] = Y::qiNiuResource(Db::name($cate)->where('id', $detail[$cate . '_id'])->value('thumbpic'), 'file');

        return Y::json($detail);

    }

}