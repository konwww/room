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
use think\Session;
use think\Url;

class Admin extends Controller
{
    /**
     *
     */
    public function index()
    {
        Url::root("/index.php");
        //用户信息验证
        $conf=Config::get("website_base_conf");
        $result=Session::get("level");
        if (!$result>0){
            $this->redirect($conf["login_req_url"]."?from=".\url("User/login",[],".html",request()->domain()));
        }
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
        $this->assign("request", request());
        return $this->fetch("Admin/member-list");
    }
//section 管理
    public function sectionList(){
        $this->assign("request", request());
        return $this->fetch("Admin/section-list");
    }
    public function sectionUpdateHandle(){

    }



}