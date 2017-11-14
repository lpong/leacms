<?php

namespace app\v1\controller;

use app\common\controller\ApiController;
use app\common\logic\Collection;
use app\common\logic\Zan;
use app\common\model\Course;
use app\common\model\CourseAudio;
use app\common\model\FreeZone;
use app\common\model\FreeZoneAudio;
use app\common\model\Setting;
use app\common\model\SpecialZone;
use app\common\model\SpecialZoneAudio;
use app\common\model\Ting;
use lea\Y;
use think\Db;
use think\Request;

class AppController extends ApiController
{
    /**
     * 获取服务器时间
     * @return mixed
     */
    public function getTime()
    {
        $time = time();
        return Y::json([
            'timestamp' => $time,
            'datetime'  => date('Y-m-d H:i:s', $time),
        ]);
    }

    /**
     * 应用初始化
     * @return mixed
     */
    public function init()
    {
        $zan = new Zan(Y::app()->getUserId());
        $collection = new Collection(Y::app()->getUserId());

        //获取banner图
        $banner = cache('banner_index');
        if (!$banner) {
            $banner = Db::name('ad')->where('type', 1)->where('status', 1)->order('sort asc')->limit(9)->select();
            if ($banner) {
                $temp = [];
                foreach ($banner as $val) {
                    array_push($temp, [
                        'title'        => $val['title'],
                        'picture'      => Y::qiNiuResource($val['picture'], 'file'),
                        'action_type'  => $val['action_type'],
                        'action_param' => $val['action_param'],
                    ]);
                }
                $banner = $temp;
            }
            cache('banner', $banner, 600);
        }

        //免费专区
        $free_zone = Db::name('free_zone')->find(1);
        $free_zone['thumbpic'] = Y::qiNiuResource($free_zone['thumbpic'], 'file');
        $free_zone['picture'] = Y::qiNiuResource($free_zone['picture'], 'file');
        //$free_zone['lists']    = Db::name('free_zone_audio')->field('id,type,zan,collection,title,size,time,resource')->order('serial_number asc,id desc')->where('status', 1)->limit(4)->select();
        $free_zone['lists'] = Db::query("select s.* from (SELECT max(id) as id FROM `free_zone_audio` group by `type` limit 10) t left join `free_zone_audio` as s on t.id=s.id where status=1 ORDER BY type asc");
        if ($free_zone['lists']) {
            $temp = [];
            $type = FreeZoneAudio::$autoType;
            foreach ($free_zone['lists'] as $val) {
                array_push($temp, [
                    'id'            => $val['id'],
                    'type'          => isset($type[$val['type']]) ? $type[$val['type']] : '',
                    'title'         => $val['title'],
                    'resource'      => $val['resource'],
                    'collection'    => $val['collection'],
                    'is_collection' => $collection->isCollection('free_zone', $val['id']),
                    'is_zan'        => $zan->isZan('free_zone', $val['id']),
                    'zan'           => $val['zan'],
                    'time'          => $val['time'],
                    'src'           => Y::qiNiuResource($val['resource'], 'audio'),
                ]);
            }
            $free_zone['lists'] = $temp;
        }

        //专栏
        $special_zone = Db::name('special_zone')->field('id,type,thumbpic,title,rss_num,fee')->where('is_recommend', 1)->where('status', 1)->limit(3)->order('update_time desc')->select();
        if ($special_zone) {
            $type = SpecialZone::$autoType;
            $audioType = SpecialZoneAudio::$autoType;
            foreach ($special_zone as &$val) {
                $val['type'] = str2name($val['type'], $type);
                $val['fee'] = floatval($val['fee']);
                $val['thumbpic'] = Y::qiNiuResource($val['thumbpic'], 'file');
                $last_update_audio = Db::name('special_zone_audio')->field('id,title,type,serial_number')->where('special_zone_id', $val['id'])->where('need_charge', 1)->where('status', 1)->order('serial_number desc,id desc')->find();
                if ($last_update_audio) {
                    $last_update_audio['type'] = isset($audioType[$last_update_audio['type']]) ? $audioType[$last_update_audio['type']] : '';
                    $val['last_update_audio'] = $last_update_audio;
                } else {
                    $val['last_update_audio'] = (new \stdClass());
                }

            }
        }

        //精品课
        $course = Db::name('course')->field('id,type,thumbpic,author,author_remark,title,buy_num,fee')->where('is_recommend', 1)->where('status', 1)->limit(3)->order('update_time desc')->select();
        if ($course) {
            $type = Course::$autoType;
            foreach ($course as &$val) {
                $val['type'] = isset($type[$val['type']]) ? $type[$val['type']] : '';
                $val['fee'] = floatval($val['fee']);
                $val['thumbpic'] = Y::qiNiuResource($val['thumbpic'], 'file');
                $val['audio_num'] = Db::name('course_audio')->where('course_id', $val['id'])->where('need_charge', 1)->where('status', 1)->count();
                $val['audio_time'] = Db::name('course_audio')->where('course_id', $val['id'])->where('need_charge', 1)->where('status', 1)->sum('time');
            }
        }
        return Y::json([
            'banner'            => $banner,
            'free_zone'         => $free_zone,
            'special_zone'      => $special_zone,
            'course'            => $course,
            'free_zone_cate'    => arr2java(FreeZoneAudio::$autoType),
            'special_zone_cate' => arr2java(SpecialZone::$autoType),
            'course_cate'       => arr2java(Course::$autoType),
            'ting_cate'         => arr2java(Ting::$autoType),
        ]);

    }


    /**
     * 单页
     * @param Request $request
     * @return \think\response\View
     */
    public function article(Request $request)
    {
        $id = $request->param('id', 0, 'intval');
        $info = Db::name('article')->where('id', $id)->where('status', 1)->find();
        return view('article', [
            'info' => $info,
        ]);
    }

    //获取字段说明
    public function getFieldRemark()
    {
        $data = [];
        $list = Db::query('SHOW TABLE STATUS');
        foreach ($list as $val) {
            $data[$val['Name']]['name'] = $val['Comment'];
        }
        foreach ($data as $key => &$val) {
            $field = Db::query("SHOW FULL FIELDS FROM `{$key}`");
            foreach ($field as $v) {
                $data[$key]['field'][$v['Field']] = $v['Comment'];
            }
        }
        if (config('app_debug')) {
            return Y::json($data);
        }

    }

}