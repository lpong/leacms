<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/10
 * Time: 13:46
 */

namespace app\v1\controller;

use app\common\controller\ApiController;
use lea\Auth;
use lea\Y;
use think\Db;
use think\Image;
use think\Request;

class  FileController extends ApiController
{

    /**
     * 针对api上传的文件
     * @return mixed
     */
    public function upload(Request $request)
    {
        $type = $request->request('type', 'image');
        $file = $request->file('file');
        if (empty($file)) {
            return Y::json(5001, '文件不存在');
        }
        $auth = new Auth();
        if ($auth->checkUser() !== true) {
            return Y::json(5001, '用户验证失败');
        }

        if ($type == 'image') {
            $image = Image::open($file);
        }

        //获取上传配置
        $config = config('upload');
        $path   = $config['upload_path'] . DS . $type;
        if (!isset($config['upload_size_limit'][$type])) {
            return Y::json(5002, '上传文件格式不允许');
        }
        $info = $file->validate(['size' => $config['upload_size_limit'][$type], 'ext' => $config['upload_type_limit'][$type]])->move($path);
        if ($info) {
            $data = [
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
            $id   = Db::name('uploads')->insertGetId($data);
            return Y::json('上传成功', ['id' => Y::hash($id), 'url' => Y::file($id)]);
        } else {
            return Y::json(5003, $file->getError());
        }
    }


    /**
     * 图片展示
     * @param Request $request
     * @return $this
     */
    public function image(Request $request)
    {
        $id = $request->param('id', '', 'trim');
        $id = Y::hash($id, true);
        if ($id) {
            $image = Db::name('uploads')->find($id[0]);
        }

        if (!empty($image)) {
            $path = config('upload.upload_path') . DS . $image['type'] . DS . $image['path'];
        } else {
            $path = ROOT_PATH . 'public/static/admin/dist/img/error.png';
        }
        $content = file_get_contents($path);
        return response($content, 200, ['Content-Length' => strlen($content)])->contentType('image/jpeg');
    }
}