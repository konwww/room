<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/11/14
 * Time: 23:03
 */

namespace app\index\controller;


use app\index\model\Config;
use app\index\model\Curl;
use app\index\model\User;
use think\Controller;
use think\Cookie;
use think\Session;

class Oauth extends Controller
{
    public function index()
    {
        $url = Config::get('Oauth.target_url');
        $redirect_url = Config::get('Oauth.redirect_url');
        $nUrl = $url . "?redirect_uri={$redirect_url}";
//        判断请求头中有没有包含referer字段,如果有就加在参数中
        $referer = urlencode($this->request->header("referer"));
        //检测有没有跳转链接
        if (empty($referer)) {
           Cookie::set("referer",$referer);
        }
        $this->redirect($nUrl);
    }

    public function login($access_token, $openid, $timestamp, $signature)
    {
        //todo 签名验证,

        //时间验证 todo 时间验证bug,服务器和本地测试时间戳总有328左右的差值
//        dump((int)time());
//        dump((int)$timestamp);
//        dump((int)time() - (int)$timestamp);
//        if ((int)time() - (int)$timestamp >= 60 * 3) $this->error("拉取授权请求超时",null,'',100);
        $referer=Cookie::get("referer");
        $uid = Session::get("uid");
        if (empty($uid)) {
            $oauth_user_info = $this->getUserInfo($access_token, $openid);
            //如果oauth信息获取失败
            if (empty($oauth_user_info)) {
                if (parse_url($referer)["path"] != $this->request->url()) {
                    $this->error("登录失败", $referer, '', 100);
                } else {
                    $this->error("登录失败", $this->request->domain(), '', 100);
                }
            }
            $user = new User();
            $user = $user->where(["openid" => $openid])->find();
            //判断用户是否已经注册
            if (is_null($user)) {
                $this->reg($oauth_user_info);
            };
            //判断用户信息是否有更新
            if (date_diff(date_create($user->getData("updateTime")), date_create($oauth_user_info["update_time"]))) {
                $user->update(["phone"=>$oauth_user_info["phone"],"email"=>$oauth_user_info["email"],"username"=>$oauth_user_info["username"]],["uid"=>$user->getData("uid")]);
            }
        } else {
            $user = User::get($uid);
        }
        $user_data = $user->getData();
        $this->setSession($user_data);
        $redirect_url = $this->request->domain() . "/index.php/user/index";
        if (!empty($referer) && parse_url($referer)["path"] != $this->request->url()) {
            $redirect_url = $referer;
        }
        $this->success("登陆成功", $redirect_url);
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
        $timestamp = time();
        $appid = Config::get("Oauth.appid");
        $data = ["appid" => $appid];
        $reqData = ["access_token" => $accessToken, "openid" => $openid, "timestamp" => $timestamp];
        $data["signature"] = $this->sign($reqData);
        $data["data"] = json_encode($reqData);
        $oauth_user_info = Config::get("Oauth.user_info_url");
        $curl = new Curl();
        $result = $curl->setReferer($this->request->domain())->setParams($data)->post($oauth_user_info, "json");
        if (!is_array($result)) {
            $result = json_decode($result, true);
        }
        return $result["replyContent"];
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