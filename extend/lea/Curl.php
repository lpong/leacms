<?php
/**
 * Created by PhpStorm.
 * User: Y.c
 * Date: 2017/6/12
 * Time: 14:18
 */

namespace lea;

class Curl
{

    private $url     = '';
    private $data    = [];
    private $method  = 'get';
    private $timeout = 30;

    /**
     * 设置数据
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * get方法
     * @return $this
     */
    public function get($url = '', $data = [])
    {
        $this->url    = $url;
        $this->data   = $data;
        $this->method = 'get';
        return $this->doRequest();
    }

    public function post()
    {
        $field = func_get_args();
        if (count($field) == 1) {
            $this->data = $field[0];
        }
        if (count($field) == 2) {
            $this->url  = $field[0];
            $this->data = $field[1];
        }
        $this->method = 'post';
        return $this->doRequest();
    }


    public function doRequest()
    {
        $curl = curl_init();
        if (substr($this->url, 0, 5) == 'https') {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
            //curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        }


        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if (is_array($this->data)) {
            $this->data = http_build_query($this->data);
        }

        if ($this->method == 'get') {
            if ($this->data) {
                $this->url = trim($this->url, '/') . '?lea=1' . $this->data;
            }
            curl_setopt($curl, CURLOPT_URL, $this->url);
        } else {
            curl_setopt($curl, CURLOPT_URL, $this->url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $this->data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Expect:']);
        }

        $output = curl_exec($curl);
        $error  = curl_error($curl);
        curl_close($curl);
        if ($error) {
            return $error;
        }
        $data = json_decode($output, true);
        return $data ? $data : $output;
    }
}