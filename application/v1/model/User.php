<?php
/**
 * Created by PhpStorm.
 * User: Y.c
 * Date: 2017/6/19
 * Time: 13:02
 */

namespace app\v1\model;

use app\common\util\Easemob;
use Hashids\Hashids;
use think\Db;
use think\Log;
use think\Model;
use think\Request;

class User extends Model
{

    const STATUS_FORBIDDEN = '0';  //状态：禁用
    const STATUS_ACTIVE    = '1';   //状态:正常
    const STATUS_DELETE    = '2';   //状态:删除

    public static function passwordEncrypt($pass)
    {
        return md5(sha1($pass, substr(md5($pass), 6, 16)));
    }

    //注册
    public static function register($data)
    {

        $post['mobile']        = $data['mobile'];
        $post['openid']        = empty($data['openid']) ? null : $data['openid'];
        $post['face']          = empty($data['face']) ? null : $data['face'];
        $post['nickname']      = isset($data['nickname']) ? trim($data['nickname']) : '';
        $post['password']      = static::passwordEncrypt($data['password']);
        $post['register_time'] = date('Y-m-d H:i:s');
        $post['update_time']   = time();
        $post['balance']       = 0;
        $post['status']        = self::STATUS_ACTIVE;

        Db::startTrans();
        try {
            $udata   = [];
            $user_id = Db::name('user')->insertGetId($post);
            $hashids      = new Hashids('', 8, '0123456789ABCDEF');
            $post['uuid'] = $udata['uuid'] = $hashids->encode($user_id + substr(time(), -6));
            if (empty($post['nickname'])) {
                $nickname         = (new Hashids('', 5))->encode($user_id);
                $post['nickname'] = $udata['nickname'] = '家长' . $nickname;
            }
            Db::name('user')->where('id', $user_id)->update($udata);
            $token = static::generateToken($user_id);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            Log::error('注册失败:' . $e->getMessage());
            return '服务器异常,请稍后再试';
        }
        return [
            'nickname' => $post['nickname'],
            'uuid'     => $post['uuid'],
            'token'    => $token
        ];
    }

    /**
     * 获取用户信息
     * @param int $uid
     * @return array|mixed|null
     */
    public static function getUserInfo($uid = 0)
    {
        if (!$uid) return null;
        $key  = 'user:info:' . $uid;
        $info = cache($key);
        if (empty($info)) {
            $info = Db::name('user')->find($uid);
            if (!$info) {
                return null;
            }
            unset($info['password']);
            cache($key, $info, 2 * 60);
        }
        return $info;
    }

    //清楚用户缓存
    public static function clearUserInfoCache($uid = 0)
    {
        if (!$uid) return null;
        $key = 'user:info:' . $uid;
        cache($key, null);
    }

    //生成token
    public static function generateToken($user_id = 0)
    {
        if (!$user_id) return false;
        $token = md5(uniqid() . $user_id);
        $data  = [
            'user_id' => $user_id,
            'token'   => $token,
            'agent'   => substr(Request::instance()->header('user-agent'), 0, 256),
            'ip'      => Request::instance()->ip(),
            'time'    => date('Y-m-d H:i:s')
        ];
        if (Db::name('user_token')->insert($data) > 0) {
            cache('user:token:' . $user_id, $token, config('token_expiration_date'));
            $hash = new Hashids(config('data_auth_key'), 32);
            $uuid = $hash->encode($user_id, time());

            $access_token = base64_encode($uuid . '-' . $token);
            return $access_token;
        }
        return false;
    }

    public static function logout($user_id)
    {
        cache('user:token:' . $user_id);
    }

}