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
//        $list=Cache::get("config_list");
//        dump($list);
//        \app\index\model\Config::updateCache();
//        $result=\app\index\model\Config::get("Oauth.target_url");
//        dump($result);
        $ua=$_SERVER["HTTP_USER_AGENT"];
        if(strpos($ua, 'MicroMessenger') == false ){
            echo "来自普通浏览器访问";
        }else{
            echo "来自微信浏览器访问";
        }
        dump($ua);
        dump(strpos($ua, 'MicroMessenger'));
//    dump(parse_url("www.baidu.com"));
    }

}