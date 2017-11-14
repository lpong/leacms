<?php

namespace app\admin\controller\system;

use app\admin\controller\CommonController;
use app\common\util\Database;
use think\Db;

class DbController extends CommonController
{


    public function index()
    {

        return view();
    }

    //table列表
    public function lists()
    {
        $list = Db::query('SHOW TABLE STATUS');
        $list = array_map('array_change_key_case', $list);
        $this->assign('list', $list);
        return view();
    }

    //备份
    public function backup()
    {
        if ($this->request->isPost()) {
            $config = [
                'path'     => ROOT_PATH . '/data/backup/', //数据库备份根路径
                'part'     => 20971520,   //数据库备份卷大小
                'compress' => 1,  //数据库备份文件是否启用压缩
                'level'    => 9,   //数据库备份文件压缩级别 1:普通4:一般9:最高
            ];

            if (!is_dir($config['path'])) {
                mkdir($path, 0755, true);
            }

            $lock = $config['path'] . "backup.lock";
            if (is_file($lock)) {
                $this->error('检测到有一个备份任务正在执行，请稍后再试！');
            } else {
                //创建锁文件
                file_put_contents($lock, NOW_TIME);
            }

            //检查备份目录是否可写
            if (!is_writeable($config['path'])) {
                $this->error('备份目录不存在或不可写，请检查后重试！');
            }

            $file = [
                'name' => date('Ymd-His', time()),
                'part' => 1,
            ];

            session('backup_file', $file);
            session('backup_config', $config);

            $database = new Database(session('backup_file'), session('backup_config'));
            if (false !== $database->create()) {
                $tab = ['id' => 0, 'start' => 0];
                $this->success('初始化成功!', ['tables' => $tables, 'tab' => $tab]);
            } else {
                $this->error('初始化失败，备份文件创建失败！');
            }
        } else {
            $table  = $this->request->param('table', '', 'trim');
            $start  = $this->request->param('start', 0, 'intval');
            $is_end = $this->request->param('is_end', 0, 'intval');
            if ($is_end) {
                unlink(session('backup_config.path') . 'backup.lock');
                session('backup_file', null);
                session('backup_config', null);
                $this->success('备份完成！');
            }
            //备份指定表
            $Database = new Database(session('backup_file'), session('backup_config'));
            $start    = $Database->backup($table, $start);
            if (false === $start) { //出错
                $this->error('备份出错！');
            } else if (0 === $start) { //下一表
                $tab = ['table' => $table, 'start' => 0];
                $this->success('备份完成！', '', $tab);
            } else {
                $tab  = ['table' => $table, 'start' => $start[0]];
                $rate = floor(100 * ($start[0] / $start[1]));
                $this->success("正在备份...({$rate}%)", '', $tab);
            }
        }
    }

    public function showTables()
    {
        $list = Db::query('SHOW TABLE STATUS');
        $list = array_map('array_change_key_case', $list);
        $this->assign('list', $list);
        return view();
    }

    public function repair()
    {
        $list = Db::query("SHOW TABLES");
        foreach ($list as $key => $row) {
            $name = reset($row);
            Db::execute("REPAIR TABLE {$name}");
        }
        $this->error('修复完成');
    }

}