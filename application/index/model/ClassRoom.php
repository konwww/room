<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/5/10
 * Time: 15:25
 */

namespace app\index\model;


use think\Config;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Model;

class ClassRoom extends Model
{

    protected $classroomTable="18_19_1";
    protected $borrowTable="borrow_room";

    public function borrow($cid, $date, $uid, $code, $groupName, $purpose, $usageTime)
    {
        if (empty($date)){
            $date=date("Y-m-d H:i:s");
        }
        //教室借用
        //获取cid字段
        $result = Db::table($this->borrowTable)->insert([
            "uid" => $uid,
            "cid" => $cid,
            "date" => $date,
            "groupName" => $groupName,
            "code" => $code,
            "purpose" => $purpose,
            "usageTime" => $usageTime,
            "enabled" => 1
        ]);
        $id = Db::table($this->borrowTable)->getLastInsID();
        return $result == 1 ? $id : false;
    }

    /**
     * @param $cid
     * @return bool
     */
    public function checkBorrow($cid)
    {
        //检查教室是否已经被借出
        $result = Db::table("{$this->borrowTable} a ,{$this->classroomTable} b")->where("a.cid=b.cid")->where("a.cid=$cid")->where("enabled=1")->where("b.week>=".date("N"))->where("b.weekNum>={$this->isOdd()[1]}")->where("usageTime >= Now()")->find();
        //查询教室使用时间在未来的记录,因为所有每一节课都有一个cid,如果未来没有发现这个cid,那就意味着这间教室还没有被借用
        return empty($result);
    }

    public function getRoomInfo($cid, $weekNum, $page)
    {
        $arr = [];
        //2018-5-28 修改emptyRoom传入参数page--删除page传入, 改为参数0(即返回全部记录)  修改history查询,添加分页page
        $roomInfo = Db::table($this->classroomTable)->field("area,category,address,population")->where("cid", $cid)->find();

        $emptyRoom = $this->emptyRoom($roomInfo["area"], $roomInfo["address"], null, null, $weekNum, 0);

        $history = Db::table("{$this->borrowTable} a")->where("a.cid", "in", function ($query) use ($roomInfo) {
            $query->table($this->classroomTable)->where("address", $roomInfo["address"])->where("area", $roomInfo["area"])->field("cid");
        })->field("a.*")->order("usageTime", "desc")->page($page, 10)->select();
        foreach ($emptyRoom[0] as $key => $value) {
            $arr[$key] = [$value["week"], $value["section"], $value["cid"]];
        }
        $result = [
            "history" => $history,
            "roomInfo" => $roomInfo,
            "emptyTime" => $arr
        ];
        return $result;
        //返回所有的历史借用数据和教室信息
    }

    /**
     * todo 无课时间段查询
     * @param $area
     * @param $address
     * @param string $week
     * @param string $section
     * @param $weekNum
     * @param $page
     * @return array
     */
    public function emptyRoom($area, $address, $week='all', $section='all', $weekNum, $page)
    {
        //构建查询条件
        $expression["area"] = $area;
//        $expression["category"] = '多媒体';
        if (!empty($weekNum)){
            $expression["weekNum"]=$weekNum;
        }else{
            $weekNum=$this->isOdd();
            $weekNum=$weekNum[1];
            $nextWeekNum=$weekNum+1;
            $expression["weekNum"]=["between",[$weekNum,$nextWeekNum]];
        }
        if (!empty($address)) {
            $expression["address"] = ["like", "%$address%"];
        }
        if (!empty($week) && $week!="all") {
            $expression["week"] = $week;
        }
        //增加查询全部section(删除查询条件)
        if (!empty($section) && $section!="all") {
            $expression["section"] = $section;
        }

//        修改原来的weekNum查询条件为直接匹配周数
        $result = Db::table($this->classroomTable)->where($expression)->where("cid", "not in", function ($query) {
            $query->table($this->borrowTable)->where("usageTime>=Now()")->field("cid")->where("enabled=1");
        });

        if ($page != 0) {
            //如果page等于零,则代表返回所有的记录,不做分页处理
            $result = $result->paginate(30);
            $count = $result->total();
            $result = $result->items();
            //获取记录总数
            //以后需要把每页显示数量放在配置文件中
        } else {
            $result = $result->select();
            $count = count($result);
        }
        //添加计算记录数的方法
        return [$result, $count];
    }

    /**
     * @param null $date
     * @return array
     */
    public function isOdd($date = null)
    {
        //判断本周是否是单周
        $config=Config::get("sys_base_conf");
        $startTime = $config["startDate"];
        if ($date != null) {
            $now = date_create($date);
            //如果传入时间不为空,则使用指定时间参与运算,返回值是指定时间所在周
        } else {
            $now = date_create(date("Y-m-d", strtotime("+1 days")));
            //因为是向上取整,但是只有处于周一,一个周才算过完,避免出现本周是单周,但是下周周一但是还是单周的情况出现,故加一天计算
        }
        $data = (array)date_diff(date_create($startTime), $now);
        $data = ceil(($data["days"] / 7));
        //对周数向上取整,假如本周是第十个周,但是实际上我们只度过了九个周零几天而已,所以向上取整
        return $data % 2 != 0 ? [true, $data] : [false, $data];

    }
}