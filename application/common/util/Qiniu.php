<?php

/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/13
 * Time: 14:23
 */

namespace app\common\util;

use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Processing\PersistentFop;
use third\Http;

class Qiniu
{
    private $config;
    private $auth;

    public function __construct($config = [])
    {
        if (!$config) {
            $config = config('upload');
        }
        $this->config = $config;
        $this->auth   = new Auth($config['AccessKey'], $config['SecretKey']);
    }

    /**
     * 获取上传token
     * @param $bucket
     * @return mixed|string
     */
    public function getToken($bucket = 'file')
    {
        $token = cache('qiniu_token_' . $bucket);
        if (!$token) {
            $token = $this->auth->uploadToken($bucket);
            cache('qiniu_token_' . $bucket, $token, 30 * 60);
        }

        return $token;
    }

    /**
     * 获取文件信息
     * @param $key
     * @return mixed
     */
    public function getFileInfo($key)
    {
        $bucketManager = new \Qiniu\Storage\BucketManager($this->auth, (new Config()));
        list($fileInfo, $err) = $bucketManager->stat($this->config['bucket'], $key);
        if ($err) {
            return $err;
        } else {
            return $fileInfo;
        }
    }

    /**
     * 获取下载链接
     * @param $key
     * @return string
     */
    public function getDownloadUrl($key, $bucket, $style = '')
    {
        $config  = $this->config['bucket'][$bucket];
        $baseUrl = $config['host'] . '/' . $key;
        if ($style) {
            $baseUrl .= '?' . $style;
        }
        if ($config['private'] === true) {
            // 对链接进行签名
            $baseUrl = $this->auth->privateDownloadUrl($baseUrl);
        }
        return $baseUrl;
    }

    /**
     * 获取下载链接
     * @param $key
     * @return string
     */
    public function getAvInfo($key, $bucket)
    {
        $config  = $this->config['bucket'][$bucket];
        $baseUrl = $config['host'] . '/' . $key . '?avinfo';
        if ($config['private'] === true) {
            // 对链接进行签名
            $baseUrl = $this->auth->privateDownloadUrl($baseUrl);
        }
        $res = Http::get($baseUrl);
        return $res;
    }

    /**
     * 音频切片
     * @param $key
     * @return mixed
     */
    public function avthumb($key, $bucket)
    {
        $pipeline  = 'avthumb';
        $force     = false;
        $notifyUrl = '';

        $config = new Config();
        $pfop   = new PersistentFop($this->auth, $config);

        $fops = 'avthumb/m3u8/noDomain/1';

        list($id, $err) = $pfop->execute($bucket, $key, $fops, $pipeline, $notifyUrl, $force);
        if ($err != null) {
            return '切片失败';
        } else {
            return $id;
        }
    }

}