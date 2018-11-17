<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/11/14
 * Time: 23:03
 */

namespace app\index\controller;


use app\index\model\Config;
use app\index\model\HttpRequest;
use app\index\model\User;
use think\Controller;
use think\Log;
use think\Session;

class Oauth extends Controller
{
    public function login($accessToken, $openid, $timestamp, $signature)
    {
        $uid = Session::get("uid");
        $oauth_url = Config::get("Oauth.target_url");
        $redirect_url = Config::get("Oauth.redirect_url");
        if (empty($uid)) $this->redirect($oauth_url, ["redirectUri" => $redirect_url]);
        $this->getUserInfo($accessToken, $openid);
    }

    protected function reg($data)
    {

    }

    /**
     * @param $accessToken
     * @param $openid
     * @return bool|mixed
     */
    private function getUserInfo($accessToken, $openid)
    {
        $appid = Config::get("Oauth.appid");
        $data = ["accessToken" => $accessToken, "openid" => $openid, "appid" => $appid];
        $data["signature"] = $this->sign($data);
        $oauth_user_info = Config::get("Oauth.user_info_url");
        HttpRequest::create($oauth_user_info, $data, "GET");
        HttpRequest::exec();
        HttpRequest::error();
        if (empty(HttpRequest::$error)) {
            Log::notice("oauth_request_error:\r\n" . HttpRequest::$error);
            return false;
        }
        HttpRequest::close();
        $result = HttpRequest::$response;
        return json_decode($result, true);
    }

    private function sign($data)
    {
        $data["secret"] = Config::get("Oauth.send_secret");
        ksort($data);
        $data_json = json_encode($data);
        return sha1($data_json);
    }

    /**
     * 设置session
     * @param $openid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function setSession($openid)
    {
        $user = new User();
        $user = $user->where(["openid" => $openid])->find();
        $user_data = $user->getData();
        array_walk($user_data, function ($value, $key) {
            Session::set($key,$value);
        });
        return true;
    }

}