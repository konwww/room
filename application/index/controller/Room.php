<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/11/14
 * Time: 20:32
 */

namespace app\index\controller;


use app\index\model\ClassRoom;
use app\index\model\SectionHistory;
use think\Controller;
use think\Request;
use think\Response;
use think\Session;

class Room extends Controller
{
    private $classroom;

    public function __construct(Request $request = null)
    {
        $this->classroom = new ClassRoom();
        parent::__construct($request);
    }

    public function detail($cid)
    {
        return $this->fetch("Index/detail");
    }

    public function getSectionInfo($cid, $week)
    {
        $result = $this->classroom->getRoomInfo($cid, $week, 0);
        $section1 = [];
        $section2 = [];
        $section3 = [];
        $section4 = [];
        $section5 = [];
        $section6 = [];
        $section7 = [];
        $section8 = [];
        foreach ($result["emptyTime"] as $key) {
            //对"天"进行分类
            switch ($key[1]) {
                case 1:
                    array_push($section1, $key);
                    break;
                case 2:
                    array_push($section2, $key);
                    break;
                case 3:
                    array_push($section3, $key);
                    break;
                case 4:
                    array_push($section4, $key);
                    break;
                case 5:
                    array_push($section5, $key);
                    break;
                case 6:
                    array_push($section6, $key);
                    break;
                case 7:
                    array_push($section7, $key);
                    break;
                case 8:
                    array_push($section8, $key);
            }
        }
        //把一整周的记录压入数组
        $emptyTime = array($section1, $section2, $section3, $section4, $section5, $section6, $section7, $section7);
        return Response::create([
            "errorMsg" => "",
            "replyContent" => $emptyTime
        ], "JSON", 200);
    }

    public function index()
    {
        return $this->fetch("Index/index");
    }

    public function mark($cid, $week, $weekNum, $section, $date = null, $groupName = null, $purpose = null)
    {
        //教室借用
        $uid = Session::get("uid");
        if (empty($uid)) {
            return Response::create(["errorMsg" => "请登录后操作", "replyContent" => ""], "JSON", 200);
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
        //注册借用前检查教室是否已经被借用
        if (!$this->classroom->checkBorrow($cid)) {
            return Response::create(["errorMsg" => "使用权已经被借走", "replyContent" => ""], "JSON", 400);
        }
        $code = Session::get("code");
        //转换时间为一般时间格式
        $usageTime = $this->getSectionTime($weekNum, $week, $section);
//                判断usageTime是不是过去的时间,如果是就拒绝借用
        if (strtotime($usageTime) < time()) {
            return Response::create(["errorMsg" => "不能借用这个时间段的教室", "replyContent" => ""], "JSON", 400);
        };

        $result = $this->classroom->borrow($cid, $date, $uid, $code, $groupName, $purpose, $usageTime);
        return Response::create(["errorMsg" => "", "replyContent" => $result], "JSON", 200);
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

    public function getSectionHistory($cid, $page = 1, $listRows = 20)
    {
        $section = new SectionHistory();
        $express["cid"] = $cid;
        $result = $section->where(["cid" => $cid])->order("usageTime", "asc")->page($page, $listRows)->select();
        return Response::create(["errorMsg" => "", "replyContent" => $result], "JSON", 200);
    }

    public function readRoomList($week = null, $section = null, $address = null, $area = "雅安", $page = 1, $weekNum = null)
    {
        //todo 输入校验
        $result = $this->classroom->emptyRoom($area, $address, $week, $section, $weekNum, $page);
        return Response::create(["errorMsg" => "", "replyContent" => ["data" => $result[0], "total" => $result[1]]], "JSON", 200);
    }

    public function getRoomBaseInfo($cid)
    {
        $section = \app\index\model\Section::get($cid);
        if (is_null($section)) {
            return Response::create(["errorMsg" => "没有找到这个section", "replyContent" => ""], "JSON", 404);
        }
        return Response::create(["errorMsg" => "", "replyContent" => $section->getData()], "JSON");
    }
}