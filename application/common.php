<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function json_format(&$val)
{
    if (!is_array($val) && !is_object($val)) {
        $val = strval($val);
    }
    return $val;
}

/**
 * 把返回的数据集转换成Tree
 *
 * @param array  $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 *
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = [];
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                } else {
                    $tree[] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 *
 * @param  array  $tree 原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list 过渡用的中间数组，
 *
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = [])
{
    if (is_array($tree)) {
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string)
{
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value = [];
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr")) {
        $slice = mb_substr($str, $start, $length, $charset);
    } elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array  $list 查询结果
 * @param string $field 排序的字段名
 * @param array  $sortby 排序类型
 *                       asc正向排序 desc逆向排序 nat自然排序
 * @return mixed
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = [];
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }

        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }

        return $resultSet;
    }
    return false;
}


/**
 * 是否是手机号码，含虚拟运营商的170号段
 * @author wei sun
 * @param string $phone 手机号码
 * @return boolean
 */
function is_phone($phone)
{
    if (is_numeric($phone) && strlen($phone) == 11) {
        return true;
    }
    return false;
    //    if (strlen($phone) != 11 || !preg_match('/^1[3|4|5|7|8][0-9]\d{4,8}$/', $phone)) {
    //        return false;
    //    } else {
    //        return true;
    //    }
}

/**
 * 是否是正确的身份证号码
 * @author yc
 * @param string $id 身份证号码
 * @return boolean
 */
function is_idcard($id)
{
    $id = strtoupper($id);
    $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
    $arr_split = [];
    if (!preg_match($regx, $id)) {
        return false;
    }
    if (15 == strlen($id)) //检查15位
    {
        $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

        @preg_match($regx, $id, $arr_split);
        //检查生日日期是否正确
        $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) {
            return false;
        } else {
            return true;
        }
    } else //检查18位
    {
        $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($regx, $id, $arr_split);
        $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) //检查生日日期是否正确
        {
            return false;
        } else {
            //检验18位身份证的校验码是否正确。
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
            $arr_int = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
            $arr_ch = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
            $sign = 0;
            for ($i = 0; $i < 17; $i++) {
                $b = (int)$id{$i};
                $w = $arr_int[$i];
                $sign += $b * $w;
            }
            $n = $sign % 11;
            $val_num = $arr_ch[$n];
            if ($val_num != substr($id, 17, 1)) {
                return false;
            } //phpfensi.com
            else {
                return true;
            }
        }
    }

}

/**
 * 验证是否为中文姓名
 * @param $name
 */
function isChineseName($name)
{
    if (preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $name)) {
        return true;
    }
    return false;
}

/**
 * 数组转对象
 * @param $array
 * @return StdClass
 */
function array2object($array)
{
    if (is_array($array)) {
        $obj = new StdClass();
        foreach ($array as $key => $val) {
            $obj->$key = is_array($val) ? array2object($val) : $val;
        }
    } else {
        $obj = $array;
    }
    return $obj;
}

/**
 * 对象转数组
 * @param $object
 * @return mixed
 */
function object2array($object)
{
    if (is_object($object)) {
        foreach ($object as $key => $value) {
            $array[$key] = $value;
        }
    } else {
        $array = $object;
    }
    return $array;
}

/**
 * 获取图片路径
 * @param int $id
 * @return string
 */
function get_file_path($id = 0)
{
    if (!$id) {
        return '';
    }

    static $list;
    /* 获取缓存数据 */
    if (empty($list)) {
        $list = cache('sys_uploads_list');
    }
    /* 查找用户信息 */
    $key = "u{$id}";
    $image = '';
    if (isset($list[$key])) {
        //已缓存，直接使用
        $image = $list[$key];
    } else {
        //调用接口获取用户信息
        $x = db('uploads')->field('type,path')->find($id);
        if ($x) {
            $image = '/uploads/' . $x['type'] . '/' . $x['path'];
            $list[$key] = $image;
            $count = count($list);
            while ($count-- > 3000) {
                array_shift($list);
            }
            cache('sys_uploads_list', $list);
        }
    }
    return $image;
}

/**
 * 生成一个固定长度的邀请码
 * @return string
 */
function generateCode()
{
    mt_srand((double)microtime() * 10000);
    $char_id = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);// "-"
    $uuid = substr($char_id, 0, 8) . $hyphen
        . substr($char_id, 8, 4) . $hyphen
        . substr($char_id, 12, 4) . $hyphen
        . substr($char_id, 16, 4) . $hyphen
        . substr($char_id, 20, 12);
    return $uuid;
}


/**
 * 生成订单号
 * @return string
 */
function build_order_no()
{
    return date('Ymd') . substr(time(), -5) . substr(microtime(), 2, 5);     //生成16位数字基本号
}

/**
 * 计算两点地理坐标之间的距离
 * @param  Decimal $longitude1 起点经度
 * @param  Decimal $latitude1 起点纬度
 * @param  Decimal $longitude2 终点经度
 * @param  Decimal $latitude2 终点纬度
 * @param  Int     $unit 单位 1:米 2:公里
 * @param  Int     $decimal 精度 保留小数位数
 * @return Decimal
 */
function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 2, $decimal = 0)
{

    $EARTH_RADIUS = 6370.996; // 地球半径系数
    $PI = 3.1415926;

    $radLat1 = $latitude1 * $PI / 180.0;
    $radLat2 = $latitude2 * $PI / 180.0;

    $radLng1 = $longitude1 * $PI / 180.0;
    $radLng2 = $longitude2 * $PI / 180.0;

    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;

    $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
    $distance = $distance * $EARTH_RADIUS * 1000;

    if ($unit == 2) {
        $distance = $distance / 1000;
    }

    return round($distance, $decimal);
}

//验证坐标
function verfiyPos($pos, $city = true)
{
    if (!$pos) {
        return '坐标不能为空';
    }
    $num = $city ? 3 : 2;
    $pos = explode(',', $pos);

    if (count($pos) != $num) {
        return '坐标格式错误';
    }

    if ($pos[0] > 180 || $pos[0] < 0) {
        return '经度格式错误';
    }
    if ($pos[1] > 90 || $pos[1] < 0) {
        return '纬度格式错误';
    }

    if ($city) {
        if (!$pos[2] || !is_numeric($pos[2])) {
            return '城市编码错误';
        }
    }
    return $pos;
}

function is_wechat()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    return false;
}


/**
 * 大小转化
 * @param $num
 * @return string
 */
function formatSize($num)
{
    if (!$num) return '获取中...';
    $format = 'bytes';
    if ($num > 0 && $num < 1024) {
        $p = 0;
        return number_format($num) . ' ' . $format;
    }
    if ($num >= 1024 && $num < pow(1024, 2)) {
        $p = 1;
        $format = 'Kb';
    }
    if ($num >= pow(1024, 2) && $num < pow(1024, 3)) {
        $p = 2;
        $format = 'Mb';
    }
    if ($num >= pow(1024, 3) && $num < pow(1024, 4)) {
        $p = 3;
        $format = 'Gb';
    }
    if ($num >= pow(1024, 4) && $num < pow(1024, 5)) {
        $p = 3;
        $format = 'Tb';
    }
    $num /= pow(1024, $p);
    return number_format($num, 2) . ' ' . $format;
}

/**
 *      把秒数转换为时分秒的格式
 * @param Int $times 时间，单位 秒
 * @return String
 */
function formatTime($times)
{
    if (!$times) return '获取中...';
    $result = '00:00';
    if ($times > 0) {
        $hour = floor($times / 3600);
        $minute = floor(($times - 3600 * $hour) / 60);
        $second = floor((($times - 3600 * $hour) - 60 * $minute) % 60);
        $result = ($hour ? ($hour . ':') : '') . $minute . ':' . $second;
    }
    return $result;
}

/**
 * 秒转年日时分
 * @param $time
 * @return array|bool
 */
function sec2time($time)
{
    if (is_numeric($time)) {
        $value = [
            "years"   => 0, "days" => 0, "hours" => 0,
            "minutes" => 0, "seconds" => 0,
        ];
        if ($time >= 31556926) {
            $value["years"] = floor($time / 31556926);
            $time = ($time % 31556926);
        }
        if ($time >= 86400) {
            $value["days"] = floor($time / 86400);
            $time = ($time % 86400);
        }
        if ($time >= 3600) {
            $value["hours"] = floor($time / 3600);
            $time = ($time % 3600);
        }
        if ($time >= 60) {
            $value["minutes"] = floor($time / 60);
            $time = ($time % 60);
        }
        $value["seconds"] = floor($time);
        return $value;

    } else {
        return (bool)false;
    }
}

/**
 * 时间转化
 * @param $range_time
 * @return array
 */
function range_time($range_time, $time = true)
{
    $range_time = explode('~', $range_time);
    $range_time[0] = trim($range_time[0]);

    $range_time[1] = trim($range_time[1]);
    if (strlen($range_time[1]) <= 10) {
        $range_time[1] .= ' 23:59:59';
    }
    if ($time) {
        $range_time[0] = strtotime($range_time[0]);
        $range_time[1] = strtotime($range_time[1]);
    }
    return $range_time;
}

/**
 * 1,2,3 解析文字
 * @param $val
 * @param $arr
 * @return string
 */
function str2name($val, $arr)
{
    if (!$val) return '';
    $val = explode(',', $val);
    $res = [];
    foreach ($val as $v) {
        if (isset($arr[$v])) {
            array_push($res, $arr[$v]);
        }
    }

    return implode(',', $res);
}

/**
 * @param $arr
 * @return array
 */
function arr2java($arr)
{
    $res = [];
    foreach ($arr as $key => $val) {
        array_push($res, [
            'key' => $key,
            'val' => $val,
        ]);
    }

    return $res;

}