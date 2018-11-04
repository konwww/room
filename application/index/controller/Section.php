<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/8/19
 * Time: 17:01
 */

namespace app\index\controller;


use app\index\model\SectionHistory;
use think\Controller;
use think\Session;

class Section extends Controller
{
    public function sectionList($cid = null, $time = null, $category = null, $address = null, $area = null, $weekNum = null, $week = null,$page=1,$limit=20)
    {
        $this->checkLevel();
        $expression = [];
        if (!empty($cid)) {
            $expression["cid"] = $cid;
        }
        if (!empty($time)) {
            $expression["section"] = $time;
        }
        if (!empty($category)) {
            $expression["category"] = $category;
        }
        if (!empty($address)) {
            $expression["address"] = $address;
        }
        if (!empty($area)) {
            $expression["area"] = $area;
        }
        if (!empty($weekNum)) {
            $expression["weekNum"] = $weekNum;
        }
        if (!empty($week)) {
            $expression["week"] = $week;
        }
        $section=new \app\index\model\Section();
        $result=$section->getSectionList($expression,["page"=>$page,"limit"=>$limit]);
        return json_encode($result===false?false:$result);
    }

    /**
     * 增加section数据
     * @param $time
     * @param $category
     * @param $population
     * @param $address
     * @param $area
     * @param $weekNum
     * @param $week
     * @param $availability
     * @return string
     */
    public function addSection($time, $category, $population, $address, $area, $weekNum, $week, $availability){
        $this->checkLevel();
        $expression = [];
        if (!empty($time)) {
            $expression["section"] = $time;
        }
        if (!empty($category)) {
            $expression["category"] = $category;
        }
        if (!empty($address)) {
            $expression["address"] = $address;
        }
        if (!empty($area)) {
            $expression["area"] = $area;
        }
        if (!empty($weekNum)) {
            $expression["weekNum"] = $weekNum;
        }
        if (!empty($week)) {
            $expression["week"] = $week;
        }
        if (!empty($population)) {
            $expression["population"] = $population;
        }
        if (!empty($availability)) {
            $expression["availability"] = $availability;
        }
        $section=new \app\index\model\Section();
        $cid=$section->add($expression);
        return $cid===false?json_encode([
            "code"=>-1,
            "status"=>"Failed",
            "msg"=>"添加失败"
        ]):json_encode([
            "code"=>1,
            "status"=>"success",
            "msg"=>"添加成功",
            "data"=>array_merge(["cid"=>$cid],$expression)
        ]);
    }
    public function delSection($cid){
        $this->checkLevel();
        $section=new \app\index\model\Section();
        $section->cid=$cid;
        $result=$section->del();
        return $result?json_encode([
            "code"=>1,
            "status"=>"success",
            "msg"=>"删除成功"
        ]):json_encode([
           "code"=>-1,
            "status"=>"Failed",
            "msg"=>"删除失败"
        ]);
    }
    public function disabledSection($cid)
    {
        //todo 关闭某一节
        $this->checkLevel();
        $section=new \app\index\model\Section();
        $section->cid=$cid;
        $result=$section->disabled();
        return $result===false?json_encode([
            "code"=>-1,
            "status"=>"Failed",
            "msg"=>"关闭失败"
        ]):json_encode([
            "code"=>1,
            "status"=>"success",
            "msg"=>"关闭成功"
        ]);
    }

    public function enabledSection($cid)
    {
        //todo 打开某一节
        $this->checkLevel();
        $section=new \app\index\model\Section();
        $section->cid=$cid;
        $result=$section->enabled();
        return $result===false?json_encode([
            "code"=>-1,
            "status"=>"Failed",
            "msg"=>"关闭失败"
        ]):json_encode([
            "code"=>1,
            "status"=>"success",
            "msg"=>"关闭成功"
        ]);
    }

    /**
     * 更新section数据
     * @param $cid
     * @param $time
     * @param $category
     * @param $population
     * @param $address
     * @param $area
     * @param $weekNum
     * @param $week
     * @param $availability
     * @return string
     */
    public function updateSection($cid, $time, $category, $population, $address, $area, $weekNum, $week, $availability){
        $this->checkLevel();
        $section=new \app\index\model\Section();
        $result=$section->getSectionList(["cid"=>$cid]);
        if ($result){
            return json_encode([
                "code"=>-1,
                "status"=>"error",
                "msg"=>"没有找到与cid匹配的数据"
            ]);
        }
        $expression = [];
        if (!empty($cid)) {
            $expression["cid"] = $cid;
        }
        if (!empty($time)) {
            $expression["section"] = $time;
        }
        if (!empty($category)) {
            $expression["category"] = $category;
        }
        if (!empty($address)) {
            $expression["address"] = $address;
        }
        if (!empty($area)) {
            $expression["area"] = $area;
        }
        if (!empty($weekNum)) {
            $expression["weekNum"] = $weekNum;
        }
        if (!empty($week)) {
            $expression["week"] = $week;
        }
        if (!empty($population)) {
            $expression["population"] = $population;
        }
        if (!empty($availability)) {
            $expression["availability"] = $availability;
        }
        $result=$section->setAttribute($expression);
        return $result===false?json_encode([
            "code"=>-1,
            "status"=>"Failed",
            "msg"=>"更新失败"
        ]):json_encode([
            "code"=>1,
            "status"=>"success",
            "msg"=>"更新成功",
            "data"=>array_merge(["cid"=>$cid],$expression)
        ]);
    }
    public function usageHistory($startDate,$endDate){
        $user=new User();
        $user->checkLogin();
        $uid=Session::get("uid");
        $class=new SectionHistory();
        $result=$class->getHistoryData([
            "usageTime"=>"between {$startDate} and {$endDate}",
            "uid"=>$uid,
        ]);
        return $result?json_encode([
            "code"=>-1,
            "status"=>"Failed",
            "msg"=>"没找到记录"
        ]):json_encode([
            "code"=>1,
            "status"=>"success",
            "msg"=>"数据获取成功",
            "data"=>$result
        ]);
    }
    /**
     *检查管理员权限,如果权限不够直接返回404
     */
    protected function checkLevel(){
        $level=Session::get("level");
        if ($level<3){
//            abort(404,"页面不存在");
        }
    }
}