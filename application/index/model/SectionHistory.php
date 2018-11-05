<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/8/25
 * Time: 11:13
 */

namespace app\index\model;


use think\Db;
use think\Model;

class SectionHistory extends Model
{
    public $id;
    public $uid;
    public $cid;
    public $date;
    public $usageTime;
    public $purpose;
    public $enabled;
    public $groupName;
    public $createTime="date";
    public $update_time;
    protected $pk="id";
    protected $autoWriteTimestamp="datetime";
    protected $table="borrow_room";

}