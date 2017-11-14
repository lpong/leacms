<?php

namespace app\common\util;

/**
 * RSA非对称加密解密
 * 单例模式
 * @author yc
 */
class Rsa
{
    //RSA 私钥
    private $rsa_private_key;

    //RSA 公钥
    private $rsa_public_key;

    //静态变量保存全局实例
    private static $_instance = null;

    /**
     * 初始化，获取公钥和密钥
     * RsaUtil constructor.
     */
    private function __construct()
    {
        $this->rsa_private_key = openssl_pkey_get_private(file_get_contents(ROOT_PATH . 'data/rsa_private_key.pem'));
        $this->rsa_public_key  = openssl_pkey_get_public(file_get_contents(ROOT_PATH . 'data/rsa_public_key.pem'));
    }

    //私有克隆函数，防止外办克隆对象
    private function __clone()
    {

    }

    //静态方法，单例统一访问入口
    static public function ins()
    {
        if (is_null(self::$_instance) || isset (self::$__instance)) {
            self::$_instance = new self ();
        }

        return self::$_instance;
    }

    /**
     * 私钥加密
     * @param $str
     * @return string
     */
    public function encrypt($str)
    {
        return $this->privateEncrypt($str);
    }

    /**
     * 私钥解密（公钥加密）
     * @param $str
     * @return array || string
     */
    public function decrypt($str)
    {
        if (empty($str)) {
            return false;
        }

        $data = array_filter(explode('-', $str));
        $nstr = '';
        foreach ($data as $val) {
            $nstr .= $this->privateDecrypt($val);
        }
        $arr = json_decode($nstr, true);

        return $arr === null ? $nstr : $arr;
    }

    /**
     * 私钥加密
     * @param $str
     * @return string
     */
    public function privateEncrypt($str)
    {
        openssl_private_encrypt($str, $encrypted, $this->rsa_private_key);
        $encrypted = bin2hex($encrypted);

        return $encrypted;
    }

    /**
     * 公钥加密
     * @param $str
     * @return string
     */
    public function publicEncrypt($str)
    {
        openssl_public_encrypt($str, $encrypted, $this->rsa_public_key);
        $encrypted = bin2hex($encrypted);

        return $encrypted;
    }

    /**
     * 私钥解密
     * @param $str 公钥加密的字符串
     * @return string
     */
    public function privateDecrypt($str)
    {
        openssl_private_decrypt(hex2bin($str), $decrypted, $this->rsa_private_key);

        return $decrypted;
    }

    /**
     * 公钥解密
     * @param $str 私钥加密的字符串
     * @return string
     */
    public function publicDecrypt($str)
    {
        openssl_public_decrypt(hex2bin($str), $decrypted, $this->rsa_public_key);

        return $decrypted;
    }

}
