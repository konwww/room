<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/8/25
 * Time: 11:13
 */

namespace app\index\model;


use think\Db;

class SectionHistory
{
    public $id;
    public $uid;
    public $cid;
    public $date;
    public $usageTime;
    public $purpose;
    public $enabled;
    public $groupName;
    protected $table;
    public function __construct()
    {
    }

    public function add($data){
        $data=$this->set($data);
        $id=Db::table($this->table)->insertGetId($data);
        if (!empty($id)){
            $this->id=$id;
            return $id;
        }
        return false;
    }
    public function getHistoryData($expression){
        $result=Db::table($this->table)->where($expression)->select();
        return $result;
    }
    public function disabled($id){
        $result=Db::table($this->table)->where("id",$id)->update(["enabled"=>0]);
        return !empty($result);
    }
    public function enabled($id){
        $result=Db::table($this->table)->where("id",$id)->update(["enabled"=>1]);
        return !empty($result);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function set($data){
        if (!is_array($data)){
            exception("data 参数不是数组",-2);
            abort(500);
        }
        $result=[];
        array_walk($data,function ($value,$key) use (&$result){
            if (property_exists($this,$key)){
                $this->$key=$value;
                $result[$key]=$value;
            }
        });
        return $result;
    }
}