<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/11/11
 * Time: 17:20
 */

namespace app\index\controller;


use think\Controller;
use think\Response;

class Config extends Controller
{
    public function read($key = "")
    {
        if (empty($key)) {
            $result = \app\index\model\Config::all();
        } else {
            $result = \app\index\model\Config::get($key);
        }
        return Response::create(["errorMsg" => "", "replyContent" => $result], "JSON", 200);
    }

    public function set($key, $value)
    {

    }

    public function enable($key)
    {

    }

    public function query()
    {

    }
}