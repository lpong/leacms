<?php
/**
 * Created by PhpStorm.
 * User: Y.c
 * Date: 2017/6/19
 * Time: 10:44
 */

namespace lea;

use app\common\util\Qiniu;
use Hashids\Hashids;
use think\Request;
use think\Response;

class Y
{

    public static $identity;

    /**
     * 静态成品变量 保存全局实例
     */
    private static $_instance = null;

    /**
     * 私有化默认构造方法，保证外界无法直接实例化
     */
    private function __construct()
    {
    }

    /**
     * 防止用户克隆实例
     */
    public function __clone()
    {
        die('Clone is not allowed.' . E_USER_ERROR);
    }

    /**
     * 静态工厂方法，返还此类的唯一实例
     */
    public static function app()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    //绑定用户
    public function bindUser($user)
    {
        self::$identity = $user;
    }

    /**
     * 返回用户id
     * @return null
     */
    public function getUserId()
    {
        return isset(static::$identity->id) ? static::$identity->id : null;
    }

    /**
     * 加密解密一个数字
     * @param      $id
     * @param bool $type
     * @return string
     */
    public static function hash($id, $type = false)
    {
        static $hash;
        if (empty($hash)) {
            $hash = new Hashids('', 10);
        }
        if ($type) {
            $Id = $hash->decode($id);
            return isset($Id[0]) ? $Id[0] : null;
        } else {
            return $hash->encode($id);
        }

    }


    /**
     * 获取七牛云资源路径
     * @param $key
     * @param string $bucket
     * @return string
     */
    public static function qiNiuResource($key, $bucket = 'file', $style = '')
    {
        static $qiniu;
        if (empty($qiniu)) $qiniu = new Qiniu();
        return $qiniu->getDownloadUrl($key, $bucket, $style);
    }


    /**
     * 返回数据格式化
     * @return mixed
     */
    public static final function json()
    {
        $data  = ['code' => 0, 'message' => 'success'];
        $field = func_get_args();

        if (count($field) == 1) {
            if (is_array($field[0])) {
                $data = ['code' => 0, 'message' => 'success', 'data' => $field[0]];
            } else {
                $data = ['code' => 0, 'message' => $field[0]];
            }
        }
        if (count($field) == 2) {
            if (is_numeric($field[0]) && is_string($field[1])) {
                $data = ['code' => $field[0], 'message' => $field[1]];
            } else if (is_array($field[1])) {
                $data = ['code' => 0, 'message' => $field[0], 'data' => $field[1]];
            } else {
                $data = ['code' => 500, 'message' => '服务器异常'];
            }
        }

        if (count($field) == 3) {
            $data = ['code' => $field[0], 'message' => $field[1], 'data' => $field[2]];
        }

        //将返回结果统一为字符串，方便客户端操作
        $data['code'] = intval($data['code']);
        if (!empty($data['data'])) {
            array_walk_recursive($data['data'], 'json_format');
        } else {
            $data['data'] = new \stdClass();
        }
        $code = in_array($data['code'], ['404', '500', '503']) ? $data['code'] : 200;
        return Response::create($data, 'json', $code)->send();
    }

}