<?php

namespace app\common\util;

use think\Exception;
use think\Log;

class RedisLock
{
    private $_config;
    private $_redis;

    /**
     * 初始化
     * @param Array $config redis连接设定
     */
    public function __construct($config = [])
    {
        $config['host']           = isset($config['host']) ? $config['host'] : '127.0.0.1';
        $config['port']           = isset($config['port']) ? $config['port'] : '6379';
        $config['index']          = isset($config['index']) ? $config['index'] : 2;
        $config['auth']           = isset($config['auth']) ? $config['auth'] : '';
        $config['timeout']        = isset($config['timeout']) ? $config['timeout'] : 1;
        $config['reserved']       = isset($config['reserved']) ? $config['reserved'] : null;
        $config['retry_interval'] = isset($config['retry_interval']) ? $config['retry_interval'] : 100;
        $this->_config            = $config;
        $this->_redis             = $this->connect();
    }

    /**
     * 获取锁
     * @param  String $key    锁标识
     * @param  Int    $expire 锁过期时间
     * @return Boolean
     */
    public function lock($key, $expire = 60)
    {
        $is_lock = $this->_redis->setnx($key, time() + $expire);
        // 不能获取锁
        if (!$is_lock) {

            // 判断锁是否过期
            $lock_time = $this->_redis->get($key);

            // 锁已过期，删除锁，重新获取
            if (time() > $lock_time) {
                $this->unlock($key);
                $is_lock = $this->_redis->setnx($key, time() + $expire);
            }
        }

        return $is_lock ? true : false;
    }

    /**
     * 释放锁
     * @param  String $key 锁标识
     * @return Boolean
     */
    public function unlock($key)
    {
        return $this->_redis->del($key);
    }

    /**
     * 创建redis连接
     * @return Link
     */
    private function connect()
    {
        try {
            $redis = new \Redis();
            $redis->connect($this->_config['host'], $this->_config['port'], $this->_config['timeout'], $this->_config['reserved'], $this->_config['retry_interval']);
            if (empty($this->_config['auth'])) {
                $redis->auth($this->_config['auth']);
            }
            $redis->select($this->_config['index']);
        } catch (\RedisException $e) {
            throw new Exception($e->getMessage());

            return false;
        }

        return $redis;
    }

}