<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/8/17
 * Time: 16:20
 */

namespace app\index\controller;


use think\Config;
use think\Controller;
use think\Request;
use think\Session;
use think\Url;

class Admin extends Visitor
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->assign("request", request());
    }

    /**
     *
     */
    public function index()
    {
        //渲染用户界面
        $this->assign("config", [
            "title" => "后台管理"
        ]);
        $this->assign("request", request());
        return $this->fetch("Admin/index");
    }

    public function welcome()
    {
        //系统消息
        $this->assign("message", [
            [
                "title" => "后台设计中",
                "url" => url("index/Index/index", ["mid" => 123], ".html", request()->domain()),
                "time" => "2017-9-10 12:12:12"
            ], [
                "title" => "不日开放",
                "url" => url("index/Index/index", ["mid" => 123], ".html", request()->domain()),
                "time" => "2017-9-10 12:12:12"
            ]
        ]);
        //系统配置信息
        $this->assign("config", [
            "version" => "1.0 bate",
            "title" => "welcome",
            "adminName" => "test",
            ""
        ]);
        $this->assign("request", request());
        return $this->fetch("Admin/welcome");
    }

    public function siteSetting()
    {
        return $this->fetch("Admin/site-setting");
    }

//user 管理
    public function userAdd()
    {
        $this->assign("request", request());
        return $this->fetch("Admin/member-add");
    }

    public function userList()
    {
        return $this->fetch("Admin/member-list");
    }

//section 管理
    public function sectionList()
    {
        return $this->fetch("Admin/section-list");
    }

    public function borrowHistory()
    {
        return $this->fetch("Admin/borrow-history");

    }


}