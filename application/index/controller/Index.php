<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/5/10
 * Time: 15:26
 */

namespace app\index\controller;

use think\Config;
use think\Controller;
use think\Session;

class Index extends Controller
{
    protected $classroom;

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
        $this->classroom = new \app\index\model\ClassRoom();
    }

    public function index()
    {

        $this->redirect(request()->domain() . "/index.php/index/index/emptyRoom");
    }

    public function detail($cid,$weekNum)
    {
        $result = $this->classroom->getRoomInfo($cid, $weekNum, 0);
        if (request()->isAjax()) {
            if (empty($result)) {
                $response = [
                    "status" => "Error",
                    "code" => 404,
                    "msg" => "没有找到教室数据,请检查参数cid"
                ];
            } else {
                $response = [
                    "status" => "Success",
                    "code" => 200,
                    "Data" => $result
                ];
            }
            return json_encode($response);
        } else {
            $this->assign("request", request());
            $this->assign("history", $result["history"]);
            $this->assign("roomInfo", $result["roomInfo"]);
            $mon = [];
            $tues = [];
            $wed = [];
            $thur = [];
            $fri = [];
            $sat = [];
            $sun = [];
            foreach ($result["emptyTime"] as $key) {
                //对"天"进行分类
                switch ($key[0]) {
                    case 1:
                        array_push($mon, $key);
                        break;
                    case 2:
                        array_push($tues, $key);
                        break;
                    case 3:
                        array_push($wed, $key);
                        break;
                    case 4:
                        array_push($thur, $key);
                        break;
                    case 5:
                        array_push($fri, $key);
                        break;
                    case 6:
                        array_push($sat, $key);
                        break;
                    case 7:
                        array_push($sun, $key);
                }
            }
            //把一整周的记录压入数组
            $emptyTime = array($mon, $tues, $wed, $thur, $fri, $sat, $sun);
            $website = Config::get("website_base_conf");
            $this->assign("emptyTime", $emptyTime);
            $this->assign("weekNum",$this->classroom->isOdd()[1]);
            $this->assign("config", $website);
            return $this->fetch("Index/detail");
        }
    }

    /**
     * @param $cid
     * @param $week
     * @param $weekNum
     * @param $section
     * @param null $date
     * @param null $uid
     * @param null $groupName
     * @param null $purpose
     * @return false|string
     */
    public function borrow($cid, $week, $weekNum, $section, $date = null, $uid = null, $groupName = null, $purpose = null)
    {
        //教室借用
        $uid = Session::get("uid");
        if (empty($uid)) {
            return json_encode([
                "code" => -2,
                "msg" => "请登陆后进行操作",
                "status" => "error"
            ]);
        }
        //添加一个检测实名认证的过程
        $realname = Session::get("realname");
        /*if (empty($realname)){
            return json_encode([
                "code"=>-2,
                "msg"=>"请先进行实名认证",
                "status"=>"error"
            ]);
        }*/
        if (request()->isAjax()) {
            //注册借用前检查教室是否已经被借用
            if ($this->classroom->checkBorrow($cid)) {
                $code = Session::get("code");
                //转换时间为一般时间格式
                $usageTime = $this->getSectionTime($weekNum, $week, $section);
//                判断usageTime是不是过去的时间,如果是就拒绝借用
                if (strtotime($usageTime)<time()){
                    return json_encode([
                        "code"=>-1,
                        "msg"=>"你不能在过去的时间段借用该教室",
                        "status"=>"Failed"
                    ]);
                };
                $result = $this->classroom->borrow($cid, $date, $uid, $code, $groupName, $purpose, $usageTime);
                if ($result) {
                    $response = json_encode([
                        "status" => "success",
                        "code" => 1,
                        "data" => $result
                    ]);
                } else {
                    $response = json_encode([
                        "status" => "error",
                        "code" => -1,
                        "msg" => "未知错误,教室借用失败"
                    ]);
                }
                return $response;
            } else {
                //教室不可以使用,一个周内的使用权已经被别人借走
                return json_encode([
                    "status" => "failed",
                    "code" => -1,
                    "msg" => '该时间段教室使用权已经被借走'
                ]);
            }
        }else{
            return json_encode([
                "status" => "failed",
                "code" => -2,
                "msg" => '非法请求'
            ]);
        }
    }

    public function getSectionTime($weekNum, $week, $section)
    {
        $sectionStartTime = [
            1 => "8:00:00",
            2 => "10:10:00",
            3 => "14:10:00",
            4 => "16:20:00",
            5 => "19:30:00",
            6 => "12:00:00",
            7 => "18:00:00",
            8 => "21:10:00"
        ];
        $time = $sectionStartTime[(int)$section];
        $currWeek = $this->classroom->isOdd();
        $currWeek = $currWeek[1];
        $weekDiff = (int)$weekNum - $currWeek;
        $dayDiff = $weekDiff * 7 - date("w") + (int)$week;
        return date("Y-m-d", strtotime("+" . $dayDiff . " days")) . " " . $time;
    }

    public function _empty()
    {
        return (Date("Y-m-d") - "2017/02/02") % 7 % 2;
    }

    /**
     * @param null $week
     * @param null $section
     * @param null $address
     * @param string $area
     * @param int $page
     * @param null $weekNum
     * @return false|mixed|string
     */
    public function emptyRoom($week = null, $section = null, $address = null, $area = "雅安", $page = 1, $weekNum = null)
    {
        /*page等于0时为全部数据*/
        //todo 校验输入数据

        //获取空余教室信息
        $result = $this->classroom->emptyRoom($area, $address, $week, $section, $weekNum, $page);
        if (request()->isAjax()) {
            return empty($result) ? json_encode([
                "code" => 404,
                "status" => "Error",
                "msg" => "没有找到相关数据"
            ]) : json_encode([
                "status" => "Success",
                "Data" => $result,
                "code" => 200
            ]);
        } else {
            //如果不是ajax请求,则返回html页面
            $website = Config::get("website_base_conf");
            $this->assign("config", $website);
            $this->assign("request", request());
            $this->assign("data", $result[0]);
            $this->assign("total", $result[1]);
            return $this->fetch("Index/emptyRoom");
        }

    }

}