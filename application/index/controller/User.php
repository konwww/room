<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/8/18
 * Time: 19:38
 */

namespace app\index\controller;


use app\index\model\SectionHistory;
use think\Config;
use think\Response;
use think\Session;
use think\Url;

class User extends Visitor
{
    public $uid;
    public $username;
    public $password;
    public $availability;
    public $level;
    protected $user;

    /**
     * User constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        parent::__construct();
        $this->uid = Session::get("uid");
        if (is_array($data) && !empty($data)) {
            array_walk($data, function ($value, $key) {
                if (isset($this->$key)) {
                    $this->$key = $value;
                } else {
                    exception("User {$key} 属性未定义");
                    abort(500);
                }
            });
        }
        $this->user = new \app\index\model\User();
    }

    public function index()
    {
        $conf = Config::get("website_base_conf");
        $this->assign("request", request());
        $this->assign("config", $conf);
        return $this->fetch("User/index");
    }

    /**
     * 关进小黑屋
     * @param $uid
     * @return string
     */
    public function disableUser($uid)
    {
        $this->checkLevel();
        //todo 关闭user
        $response = [
            "code" => -1,
            "status" => "Failed",
            "msg" => "关闭失败"
        ];
        //管理权限鉴定
        $this->checkLevel();
        $this->user->uid = $uid;
        $result = $this->user->disabled();
        if ($result) {
            $response = [
                "code" => 1,
                "status" => "Successful",
                "msg" => "关闭成功"
            ];
        }

        return json_encode($response);
    }

    /**
     * 移出小黑屋
     * @param $uid
     * @return string
     */
    public function enableUser($uid)
    {
        $this->checkLevel();
        //todo 打开User
        $response = [
            "code" => -1,
            "status" => "Failed",
            "msg" => "打开失败"
        ];
        //管理权限鉴定
        $this->checkLevel();
        $this->user->uid = $uid;
        $result = $this->user->disabled();
        if ($result) {
            $response = [
                "code" => 1,
                "status" => "Successful",
                "msg" => "打开成功"
            ];
        }

        return json_encode($response);
    }

    public function getUserList($uid = null, $username = null, $availability = null, $level = null, $createTime = null, $page = 1, $limit = 20)
    {
//        $this->checkLevel();
        //构造查询条件
        $expression = [];
        if (!empty($uid) && is_string($uid)) {
            $expression["uid"] = $uid;
        }
        if (!empty($username) && is_bool($username)) {
            $expression["username"] = $username;
        }
        if (!empty($availability) && is_int($availability)) {
            $expression["availability"] = $availability;
        }
        if (!empty($level) && is_int($level)) {
            $expression["level"] = $level;
        }
        if (!empty($createTime) && is_array($createTime)) {
            $expression["createTime"] = $createTime;
        }
        $data = $this->user->where($expression)->page($page, $limit)->select();
        $count = $this->user->where($expression)->page($page, $limit)->count();

        return Response::create($data === false ? ["code" => 1, "msg" => "没有找到数据"]
            : ["msg" => "", "code" => "", "data" => $data, "count" => $count], "JSON");
    }

    /**
     * 登录
     * @param $pk_id
     * @param $pk_val
     * @param $uid
     * @return string
     */
    public function login($pk_id, $pk_val, $uid)
    {
        Url::root("/index.php");
        $conf = Config::get("website_base_conf");
        //发送验证请求
        $result = file_get_contents($conf["login_url_checkPK"] . ".html?uid=" . $uid . "&id=" . $pk_id . "&pk="
            . $pk_val . "&url=" . request()->domain(), false);
        $result = json_decode($result, true);
        if ($result["code"] != 1) {
            return json_encode([
                "code" => "-2",
                "status" => "Failed",
                "msg" => "response Error",
                "result" => $result
            ]);
        }
        $user_data = $this->user->checkOpenid($result["data"]["openid"]);
        if ($user_data) {
            $this->setSession($user_data);
        } else {
            $user = $this->reg($result["data"]);
            if ($user && is_object($user)) {
                //获取属性键值对
                $attrs = get_object_vars($user);
                dump($attrs);
                $this->setSession($attrs);
            } else {
                return json_encode([
                    "code" => -1,
                    "status" => "error",
                    "msg" => "未知原因导致的失败"
                ]);
            }
        }
        $this->success("登陆成功", "Index/emptyRoom");
    }

    /**
     * @return bool
     * @throws \think\Exception
     */
//    public function checkLogin()
//    {
//        //检测检测是否处于登陆状态
//        $conf = Config::get("website_base_conf");
//        $username = Session::get("username");
//        if (empty($username)) {
//            $this->redirect($conf["login_req_url"] . "?from=" . \url("User/login", [], ".html", request()->domain()));
//            return false;
//        } else {
//            return true;
//        }
//    }

    protected function setSession($data, $config = [])
    {
        if (!empty($config)) {
            Session::init($config);
        }
        array_walk($data, function ($value, $key) {
            Session::set($key, $value);
        });
    }

    public function update($uid)
    {

    }

    public function reg($data)
    {
        if (array_key_exists("uid", $data)) {
            unset($data["uid"]);
        }
        //todo 注册用户
        if (!empty($data)) {
            $user = $this->user->add($data);
            return $user === false ? false : $user;
        } else {
            exception("User reg method data 参数必须为数组", -1);
        }
        return false;
    }

    public function del($uid)
    {
        $this->checkLevel();
        $result = $this->user->del($uid);
        if ($result) {
            $response = [
                "code" => 1,
                "status" => "Successful",
                "msg" => "删除成功"
            ];
        } else {
            $response = [
                "code" => -1,
                "status" => "Failed",
                "msg" => "删除失败"
            ];
        }
        return json_encode($response);
    }

    protected function checkLevel()
    {
//        $this->checkLogin();
        $level = Session::get("level");
        if ($level < 3) {
            $this->error("权限不足");
        }
    }

    /**
     * 教室借用功能使用历史
     * @param string $cid
     * @param string $startTime
     * @param string $endTime
     * @return Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     * @throws \think\exception\DbException
     */
    public function usageHistoryForBorrowFunction($cid = "", $startTime = "", $endTime = "")
    {
        $uid = Session::get('uid');
        $expression = ['uid' => $uid];
        if (!empty($cid)) $expression["cid"] = $cid;
        if (empty($endTime)) {
            $expression['usageTime'] = "between {$endTime} and NOW()";
        } else {
            $expression['usageTime'] = "between {$endTime} and {$startTime}";
        }
        $his = SectionHistory::get($expression);
        if (is_null($his)) {
            return Response::create(['errorMsg' => '', 'replyContent' => ''], 'json');
        } else {
            return Response::create(['errorMsg' => '', 'replyContent' => $his->getData()], 'json');
        }
    }
}