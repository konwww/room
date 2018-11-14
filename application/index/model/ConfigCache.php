<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/11/8
 * Time: 15:16
 */

namespace app\index\model;


use think\Cache;
use think\Model;

class ConfigCache extends Model
{
    public $id;
    public $group;
    public $key;
    public $value;
    public $description;
    public $create_time;
    public $update_time;
    protected $autoWriteTimestamp = 'datetime';
    protected $table = 'config';
    protected $enabled = true;

    static public function updateCache()
    {
        Cache::rm('config_list');
        $config_list = self::all();
        $result = [];
        array_walk($config_list, function ($value) use (&$result) {
            $result[$value['group'] . '.' . $value['key']] = ['value' => $value["value"], 'enabled' => $value["enabled"]];
        });
        Cache::set('config_list', ksort($result), 3600 * 24 * 15);
    }
}