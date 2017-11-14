<?php

namespace app\admin\controller;

use app\common\util\Qiniu;
use Hashids\Hashids;
use lea\Y;
use think\Db;
use think\Image;
use think\Request;

class FileController extends CommonController
{

    public function upload(Request $request)
    {
        $type = $request->request('type', 'image');
        $file = $request->file('file');
        if (empty($file)) {
            $file = $request->file('video');
            $type = 'video';
        }
        if (empty($file)) {
            return json(['status' => 5, 'msg' => '文件不存在']);
        }

        if ($type == 'image') {
            $image = Image::open($file);
        }

        //获取上传配置
        $config = config('upload');
        $path   = $config['upload_path'] . DS . $type;
        if (!isset($config['upload_size_limit'][$type])) {
            return json(['code' => 2, 'msg' => '上传文件格式不允许']);
        }
        $info = $file->validate(['size' => $config['upload_size_limit'][$type], 'ext' => $config['upload_type_limit'][$type]])->move($path);

        if ($info) {
            $data          = [
                'type'     => $type,
                'ext'      => strtolower($info->getExtension()),
                'path'     => $info->getSaveName(),
                'filename' => $info->getFilename(),
                'size'     => $info->getSize(),
                'sha1'     => $info->hash('sha1'),
                'width'    => isset($image) ? $image->width() : 0,
                'height'   => isset($image) ? $image->height() : 0,
                'mime'     => $info->getMime(),
                'at_time'  => time()
            ];
            $id            = Db::name('uploads')->insertGetId($data);
            $result['src'] = '/uploads/' . $type . '/' . $info->getSaveName();
            $result['id']  = $id;
            return json(['code' => 0, 'msg' => '上传成功', 'data' => $result]);
        } else {
            return json(['code' => -10, 'msg' => $file->getError()]);
        }
    }

    public function image()
    {
        $id = Request::instance()->get('id', '', 'trim');
        if (!is_numeric($id)) {
            $id = (new Hashids('', 10))->decode($id);
            $id = $id[0];
        }
        $image = Db::name('uploads')->find($id);

        if ($image) {
            $path = config('upload.upload_path') . DS . $image['type'] . DS . $image['path'];
        } else {
            $path = ROOT_PATH . 'public/static/admin/dist/img/error.png';
        }
        $content = file_get_contents($path);
        return response($content, 200, ['Content-Length' => strlen($content)])->contentType('image/jpeg');
    }

    //上传文件
    public function uploadEditor()
    {
        //定义允许上传的文件扩展名
        $ext_arr = [
            'image' => ['gif', 'jpg', 'jpeg', 'png', 'bmp'],
            'flash' => ['swf', 'flv'],
            'media' => ['mp3', 'mp4', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'],
            'file'  => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'],
        ];

        //最大文件大小
        $max_size = 1000000000;

        $config    = config('upload');
        $save_path = $config['upload_path'] . DS . 'editor/';
        //文件保存目录URL
        $save_url = '/uploads/editor/';

        //PHP上传失败
        if (!empty($_FILES['imgFile']['error'])) {
            switch ($_FILES['imgFile']['error']) {
                case '1':
                    $error = '超过php.ini允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '图片只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择图片。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $error = '未知错误。';
            }
            return json(['error' => 1, 'message' => $error]);
        }

        //  有上传文件时
        if (empty($_FILES) === false) {
            //原文件名
            $file_name = $_FILES['imgFile']['name'];
            //服务器上临时文件名
            $tmp_name = $_FILES['imgFile']['tmp_name'];
            //文件大小
            $file_size = $_FILES['imgFile']['size'];
            //检查文件名
            if (!$file_name) {
                return json(['error' => 1, 'message' => '请选择文件。']);
            }
            //检查目录
            if (@is_dir($save_path) === false) {
                return json(['error' => 1, 'message' => '上传目录不存在。']);
            }
            //检查目录写权限
            if (@is_writable($save_path) === false) {
                return json(['error' => 1, 'message' => '上传目录没有写权限。']);
            }
            //检查是否已上传
            if (@is_uploaded_file($tmp_name) === false) {
                return json(['error' => 1, 'message' => '上传失败。']);
            }
            //检查文件大小
            if ($file_size > $max_size) {
                return json(['error' => 1, 'message' => '上传文件大小超过限制。']);
            }
            //检查目录名
            $dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
            if (empty($ext_arr[$dir_name])) {
                return json(['error' => 1, 'message' => '目录名不正确。']);
            }
            //获得文件扩展名
            $temp_arr = explode(".", $file_name);
            $file_ext = array_pop($temp_arr);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);
            //检查扩展名
            if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
                return json(['error' => 1, 'message' => "上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。"]);
            }
            //创建文件夹
            if ($dir_name !== '') {
                $save_path .= $dir_name . "/";
                $save_url  .= $dir_name . "/";
                if (!file_exists($save_path)) {
                    mkdir($save_path);
                }
            }
            $ymd       = date("Ymd");
            $save_path .= $ymd . "/";
            $save_url  .= $ymd . "/";
            if (!file_exists($save_path)) {
                mkdir($save_path);
            }
            //新文件名
            $new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
            //移动文件
            $file_path = $save_path . $new_file_name;
            if (move_uploaded_file($tmp_name, $file_path) === false) {
                return json(['error' => 1, 'message' => '上传文件失败。']);
            }
            @chmod($file_path, 0644);
            $file_url = $save_url . $new_file_name;
            return json(['error' => 0, 'url' => $file_url]);
        }
    }

    public function webUpload($field = 'file', $type = 'image', $value = '')
    {
        $param['type']  = $type;
        $param['field'] = $field;

        $param['single'] = substr($field, -2) == '[]' ? '' : 1;

        $qiniu          = new Qiniu();
        $bucket         = $type == 'image' ? 'file' : 'audio';
        $param['token'] = $qiniu->getToken($bucket);

        if (!empty($value)) {
            if (!is_array($value)) {
                $value = array_filter(array_unique(explode(',', $value)));
            }
            $temp = [];
            foreach ($value as $val) {
                $temp[$val] = $qiniu->getDownloadUrl($val, $bucket);
            }
            $param['value'] = $temp;
        }

        $param['time'] = uniqid();
        return $this->fetch('public/web_upload', $param);
    }
}