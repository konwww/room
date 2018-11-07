<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/8/17
 * Time: 16:21
 */

namespace app\index\model;


use think\Db;
use think\Model;

class Section extends Model
{
    protected $pk="cid";
    protected $table="18_19_1";
    public $address;
    public $cid;
    public $population;
    public $category;
    public $area;
    public $weekNum;
    public $week;
    public $availability;
    public function __construct($data = [])
    {
        parent::__construct($data);
    }
    public function history(){
        $this->hasMany("SectionHistory","cid");
    }
}