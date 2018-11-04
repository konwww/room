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
    public $address;
    public $cid = null;
    public $population;
    public $category;
    public $area;
    public $weekNum;
    public $week;
    public $availability;
    protected $table = "classroom";


    /**
     *
     * Section constructor.
     * @param array $attr
     */
    public function __construct($attr = [])
    {
        parent::__construct();
        if (!empty($attr)) {
            $result = Db::table($this->table)->where($attr)->find();
            if (!empty($result)) {
                $this->set($result);
            } else {
                exception("Section.__construct  无效属性,没有找到对应的记录");
            }
        }
        return $this;
    }

    /**
     *设置且更新属性
     * @param $data
     * @return bool|int|string
     */
    public function setAttribute($data = [])
    {
        if (empty($this->cid)) {
            exception("Section 对象的cid属性值不能为空", -1);
        } else {
            //设置属性值
            $this->set($data);
            //更新数据库记录
            $result = Db::table($this->table)->where("cid", $this->cid)->update($data);
            return $result;
        }
        return false;
    }

    /**
     * @param array $attrArray
     * @return $this
     */
    protected function set($attrArray)
    {
        if (!is_array($attrArray)) {
            exception("Section 中 set 的 attrArray 参数必须是array类型", -2);
            abort(500);
    }
        array_walk($attrArray, function ($value, $key) {
            //检测属性
            if (!isset($this->$key)) {
                exception("section对象没有 {$key} 属性", -2);
                abort(500);
            }
            $this->$key = $value;
        });
        return $this;
    }

    /**
     * @param array $data
     * @return $this|bool
     */
    public function add($data)
    {
        //设置属性值
        $this->set($data);
        //更新数据库记录
        $result = Db::table($this->table)->where("cid", $this->cid)->insertGetId($data);
        if (empty($result)) {
            $this->cid = $result;
            return $this;
        }
        return false;
    }

    /**
     * 设置指定section可用
     */
    public function enabled()
    {
        if (empty($this->cid)) {
            exception("section 对象 enabled 方法 cid不可为空", -2);
        } else {
            $result = Db::table($this->table)->where("cid", $this->cid)->update(["availability" => 1]);
            return $result;
        }
        return false;
    }

    /**
     * 设置指定section 不可用
     */
    public function disabled()
    {
        if (empty($this->cid)) {
            exception("section 对象 enabled 方法 cid不可为空", -2);
        } else {
            $result = Db::table($this->table)->where("cid", $this->cid)->update(["availability" => 0]);
            return $result;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function del()
    {
        if (empty($this->cid)) {
            exception("section 对象 del 方法 cid不可为空", -2);
        } else {
            $result = Db::table($this->table)->where("cid", $this->cid)->delete();
            return !empty($result);
        }
        return false;
    }

    /**
     * @param array $expression
     * @return array|false|mixed|\PDOStatement|string|\think\Collection
     */
    public function getSectionList($expression,$page)
    {
        if (!is_array($expression)) {
            exception("Section 中 query 的 expression 参数必须是array类型", -2);
            abort(500);
        }
        $result = Db::table($this->table)->where($expression)->page($page["page"],$page["limit"])->select();
        return empty($result) ? false : $result;
    }

}