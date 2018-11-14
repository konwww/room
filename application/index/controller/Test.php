<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/11/6
 * Time: 15:48
 */

namespace app\index\controller;


use think\Cache;
use think\Controller;
use think\Response;

class Test extends Controller
{
    function userTest()
    {
//        $result = Config::set(2, "345");
//        $result = Config::updateCache();
        $result=Cache::get("config_list");
        return Response::create($result, "JSON");
    }

}