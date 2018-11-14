<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/11/8
 * Time: 15:31
 */

namespace app\index\model;


use think\Cache;

class Config
{
    static $count = 0;

    /**
     * 设置配置信息
     * @param $key
     * @param $value
     * @return ConfigCache
     */
    static public function set($key, $value)
    {
        $argc = self::keyDecode($key);
        $result = ConfigCache::update(["value" => $value], ["group" => $argc[0], "key" => $argc[1]]);
        self::updateCache();
        return is_null($result->getData());
    }

    /**
     * 获取配置信息
     * @param $key
     * @return mixed
     */
    static public function get($key)
    {
        $config_list = Cache::get("config_list");
        //如果配置项存在且可用性为true,则直接返回该value
        if (!empty($config_list[$key]) && $config_list[$key]['enabled'] === 1) return $config_list[$key]["value"];
        return false;
    }
    static public function all(){
        $config_list=Cache::get("config_list");
        return $config_list;
    }

    static public function has($key)
    {
        $result = Cache::get($key);
        return !empty($result) && $result[$key]["enabled"] === 1;
    }

    /**
     * 按分组名获取配置项
     * @param array $groupNames
     * @return false|\PDOStatement|string|\think\Collection
     */
    static public function queryByGroup($groupNames = [])
    {
        if (is_array($groupNames)) {
            $group_name_list = array($groupNames);
        } else {
            $group_name_list = $groupNames;
        }
        $expression = ["group" => $group_name_list];
        return self::query($expression);
    }

    /**
     * 读取数据库中配置项信息
     * todo: 增加从缓存中读取的方法
     * @param $expression
     * @return false|\PDOStatement|string|\think\Collection
     */
    static protected function query($expression)
    {
        $expression["enabled"] = 1;
        return (new ConfigCache())->where($expression)->select();
    }

    static public function disable($key)
    {
        $para = self::keyDecode($key);
        ConfigCache::update(["enabled" => 0], ["group" => $para[0], "key" => $para[1]]);
        self::updateCache();
    }

    static public function enable($key)
    {
        $para = self::keyDecode($key);
        ConfigCache::update(["enabled" => 1], ["group" => $para[0], "key" => $para[1]]);
        self::updateCache();
    }

    static protected function keyDecode($key)
    {
        $argc = strtok($key, ".");
        return $argc;
    }

    static public function updateCache()
    {
        ConfigCache::updateCache();

    }
}