<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/11/6
 * Time: 15:48
 */

namespace app\index\controller;


use think\Controller;

class Test extends Controller
{
    function index()
    {
//        $result = Config::set(2, "345");
//        $result = Config::updateCache();
//        \app\index\model\Config::updateCache();
//        $list=Cache::get("config_list");
//        dump($list);
        $result=\app\index\model\Config::get("Oauth.target_url");
        dump($result);
        dump($this->request);
    }

}