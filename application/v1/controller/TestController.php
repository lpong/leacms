<?php
/**
 * Created by PhpStorm.
 * User: YC
 * Date: 2017/5/15
 * Time: 10:55
 */

namespace app\v1\controller;

use app\common\logic\Study;
use app\common\util\ConfigApi;
use app\common\util\Easemob;
use app\common\util\Qiniu;
use app\v1\model\User;
use app\v1\util\BusinessIncome;
use app\v1\util\NobilityIncome;
use app\v1\util\NobilityUpgrade;
use app\v1\util\ShareholderBonus;
use app\v1\util\ThreeStageDistribution;
use app\v1\util\UpdateRelation;
use app\v1\util\UserRelationData;
use JPush\Config;
use think\Db;
use think\Queue;
use think\Request;

class  TestController
{

    public function __construct()
    {
        ConfigApi::config();
    }

    //音频切片测试
    public function avthumb()
    {
        $qiniu = new Qiniu();

        //$id='z1.59e56ebb8a3c0c37949137ba';
        var_dump($qiniu->avthumb('Fs1ckvF7TYaba97jEL22pGqf9DhB', 'audio'));
    }

    //音频加密处理
    public function audioEncrypt()
    {
        $qiniu = new Qiniu();
        $url   = $qiniu->getDownloadUrl('G4AfOCfmWTJndZS6RaRzJ9PgMfg=/Fs1ckvF7TYaba97jEL22pGqf9DhB', 'audio');
        var_dump($url);
    }

    public function avinfo()
    {
        $qiniu = new Qiniu();
        $url   = $qiniu->getAvInfo('Fs1ckvF7TYaba97jEL22pGqf9DhB', 'audio');
        var_dump($url);
    }

    public function updateAvInfo()
    {
        for ($i = 1; $i <= 20; $i++) {
            Queue::push('app\common\job\AvInfo', [
                'table' => 'free_zone',
                'id'    => $i
            ]);
            Queue::push('app\common\job\AvInfo', [
                'table' => 'ting',
                'id'    => $i
            ]);
            Queue::push('app\common\job\AvInfo', [
                'table' => 'course',
                'id'    => $i
            ]);
            Queue::push('app\common\job\AvInfo', [
                'table' => 'special_zone',
                'id'    => $i
            ]);
        }
    }

    //学习时长测试
    public function updateStudyTime()
    {
        $studyLogic = new Study(1);
        dump($studyLogic->updateStudyTime('course', 1, 100));
        dump($studyLogic->getTotalStudyTime());
        dump($studyLogic->getTotalStudyDay());
        dump($studyLogic->getContinuityStudyDay());
    }

}