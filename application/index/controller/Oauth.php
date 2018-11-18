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
    public function index(){
        $url=Config::get('Oauth.target_url');
        $redirect_url=Config::get('Oauth.redirect_url');
        $this->redirect($url."?redirectUri={$redirect_url}");
    }
    public function login($access_token="", $openid="", $timestamp="", $signature="")
    {
        $uid = Session::get("uid");
        $oauth_url = Config::get("Oauth.target_url");
        $redirect_url = Config::get("Oauth.redirect_url");
        if (empty($uid)) $this->redirect($oauth_url, ["redirectUri" => $redirect_url]);
        $oauth_user_info = $this->getUserInfo($access_token, $openid);
        //如果oauth信息获取失败
        if (!$oauth_user_info) $this->error("登陆失败", "http://" . $this->request->domain());
        $user = new User();
        $user = $user->where(["openid" => $openid])->find();
        if (is_null($user)) {
            $this->reg($oauth_user_info);
        };
        $user_data = $user->getData();
        $this->setSession($user_data);
        $this->success("登陆成功", "http://" . $this->request->domain());
    }

    /**
     * @param $data
     * @return User
     */
    protected function reg($data)
    {
        return User::create(["openid" => $data["openid"], "username" => $data["username"], "level" => 1, "code" => $data["code"], "phone" => $data["phone"], "email" => $data["email"]]);
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
        $headers=["Referer"=>"http://room.test.ga"];
        HttpRequest::create($oauth_user_info, $data, "POST",$headers);
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
        $data["secret"] = Config::get("Oauth.issue_secret");
        ksort($data);
        $data_json = json_encode($data);
        return sha1($data_json);
    }

    /**
     * 设置session
     * @param $data
     * @return bool
     */
    private function setSession($data)
    {
        array_walk($data, function ($value, $key) {
            Session::set($key, $value);
        });
        return true;
    }

}