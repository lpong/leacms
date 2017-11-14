<?php

namespace app\common\util;

use think\Cache;
use think\Config;
use think\Db;

/**
 * Created by PhpStorm.
 * User: YC
 * Date: 2017/4/29
 * Time: 13:56
 */
class ConfigApi
{
    //获取配置
    public static function config()
    {
        $config = Cache::get('sys:cache:config');
        if (!$config) {
            $config = self::lists();
            Cache::set('sys:cache:config', $config);
        }
        Config::set($config);
    }

    /**
     * 获取数据库中的配置列表
     * @return array 配置数组
     */
    public static function lists()
    {
        $data   = Db::name('config')->field('type,name,value')->select();
        $config = [];
        if ($data && is_array($data)) {
            foreach ($data as $value) {
                $config[$value['name']] = self::parse($value['type'], $value['value']);
            }
        }

        return $config;
    }

    /**
     * 根据配置类型解析配置
     * @param  integer $type  配置类型
     * @param  string  $value 配置值
     */
    private static function parse($type, $value)
    {
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = [];
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }

        return $value;
    }
}