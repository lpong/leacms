<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\template\taglib;

use think\template\TagLib;

/**
 * CX标签库解析类
 * @category   Think
 * @package  Think
 * @subpackage  Driver.Taglib
 * @author    liu21st <liu21st@gmail.com>
 */
class Lea extends Taglib
{

    // 标签定义
    protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'banner' => ['attr' => 'type,limit'],
    ];

    public function tagBanner($tag, $content)
    {
        $type  = empty($tag['type']) ? 1 : $tag['type'];
        $limit = empty($tag['limit']) ? 5 : $tag['limit'];
       // $parseStr = "$list=Db::name('ad')->where('type',".$type.")->limit(".$limit.")->select();";


    }
}
