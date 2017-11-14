<?php

namespace app\common\util;

/**
 * 环信即时聊天
 * Class Easemon
 * @package app\common\util
 */
class Easemob
{

    const URL = 'https://a1.easemob.com/';

    private $org_name;
    private $app_name;
    private $org_admin;
    private $app_key;
    private $client_id;
    private $client_secret;

    private $url;
    private $token;

    /**
     * 应用初始化，配置
     * Easemob constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $config = array_merge(config('other.easemob'), $config);
        foreach ($config as $key => $val) {
            $this->$key = $val;
        }
        if (empty($this->app_key)) {
            $this->app_key = $this->org_name . '#' . $this->app_name;
        }
        $this->url = self::URL . $this->org_name . '/' . $this->app_name;
        if (empty($this->token)) {
            $this->token = $this->getToken();
        }
    }

    /**
     * 注册一个用户
     * @param $uid
     * @return bool
     */
    public function registerUser($uid)
    {
        $url = $this->url . "/users";
        if (is_array($uid)) {
            $data = [];
            foreach ($uid as $id) {
                array_push($data, [
                    'username' => 'qmm_' . $id,
                    'password' => substr(md5('qmm' . $id), 6, 16),
                ]);
            }
        } else {
            $data = [
                'username' => 'qmm_' . $uid,
                'password' => substr(md5('qmm' . $uid), 6, 16),
            ];
        }
        $result = $this->curl($url, $data);
        if (!empty($result['entities']['uuid'])) {
            return $result['entities']['uuid'];
        }
        return false;
    }

    /**
     * 创建一个群聊天
     * @param $data
     * @return bool
     */
    public function createChatGroups($data)
    {
        $url = $this->url . "/chatgroups";
        $data = [
            'groupname' => 'chatgroups_' . $data['id'],
            'desc' => $data['task_name'],
            'public' => false,
            'maxusers' => 200,
            'members_only' => true,
            'allowinvites' => false,
            'owner' => 'yiqigo_' . $data['uid']
        ];
        $result = $this->curl($url, $data);
        if (!empty($result['data']['groupid'])) {
            return $result['data']['groupid'];
        }
        return false;
    }

    /**
     * 给一个聊天室加入一个用户
     * @param $group_id
     * @param $uid
     * @return bool
     */
    public function addUserToGroup($group_id, $uid)
    {
        $url = $this->url . "/chatgroups/{$group_id}/users/yiqigo_{$uid}";
        $result = $this->curl($url);
        if (!empty($result['data']['action']) && $result['data']['action'] == 'add_member') {
            return true;
        }
        return false;
    }

    /**
     * 给一个聊天室移除一个用户
     * @param $group_id
     * @param $uid
     * @return bool
     */
    public function removeUserToGroup($group_id, $uid)
    {
        $url = $this->url . "/chatgroups/{$group_id}/users/yiqigo_{$uid}";
        $result = $this->curl($url, [], [], 'DELETE');
        if (!empty($result['data']['action']) && $result['data']['action'] == 'remove_member') {
            return true;
        }
        return false;
    }

    /**
     * 给一个聊天室移除一个用户
     * @param $group_id
     * @param $uid
     * @return bool
     */
    public function removeChatGroup($group_id)
    {
        $url = $this->url . "/chatgroups/{$group_id}";
        $result = $this->curl($url, [], [], 'DELETE');
        if (!empty($result['data']['success']) && $result['data']['success'] == true) {
            return true;
        }
        return false;
    }

    /**
     * 获取授权码
     * @return mixed
     */
    private function getToken()
    {
        $token = cache('easemob_cache_token');
        if (!$token) {
            $url = $this->url . "/token";
            $data = [
                'grant_type' => 'client_credentials',
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret
            ];
            $result = $this->curl($url, $data);
            $token = $result['access_token'];
            cache('easemob_cache_token', $token, $result['expires_in'] - 600);
        }
        return $token;
    }

    /**
     * 发动curl请求
     * @param        $url
     * @param        $data
     * @param bool $header
     * @param string $method
     * @return mixed
     */
    private function curl($url, $data = [], $he = [], $method = "POST")
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $header = [
            'Content-Type: application/json',
        ];

        if ($this->token) {
            array_push($header, 'Authorization: Bearer ' . $this->token);
        }
        $header = array_merge($header, $he);
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $ret = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode != '200') {
            return false;
        }

        $result = json_decode($ret, true);
        return $result === null ? $ret : $result;
    }
}