<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2019/2/2
 * Time: 22:52
 */

namespace app\index\controller;


use think\Config;
use think\Controller;
use think\Request;
use think\Session;
use think\Url;

class Visitor extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        Url::root("/index.php");
        $user_agent = $this->request->header("User-Agent");
        if (strpos($user_agent, "MicroMessenger") !== false) {
            //微信内置浏览器环境中,使用静默授权
            $this->isWeChat();
        };
        $conf = Config::get("website_base_conf");
        $result = Session::get("level");
//        if (!$result > 0) {
//            $this->redirect($conf["login_req_url"] . "?redirect_uri=" . \url("User/login", [], ".html", request()->domain()));
//        }
    }

    private function isWeChat()
    {
        $openid = Session::get("openid");
        if (empty($openid)) {
            $this->goToOauth();
        }

    }

    private function goToOauth()
    {

    }

}