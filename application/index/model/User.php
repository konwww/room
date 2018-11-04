<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/8/18
 * Time: 11:45
 */

namespace app\index\model;


use PDOStatement;
use think\Db;
use think\Model;
use think\Session;

class User extends Model
{
    public $uid;
    public $username;
    protected $password;
    public $createTime;
    public $updateTime;
    public $level = 1;
    public $availability;
    public $phone;
    public $email;
    public $code;//学号
    public $openid;
    protected $table = "user";

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
                exception("User -> __construct  无效属性,没有找到对应的记录");
            }
        }
        return $this;
    }


    /**
     *设置且更新属性
     * @param array $data
     * @return bool|int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function setAttribute($data = [])
    {
        if (empty($this->uid)) {
            exception("Section 对象的uid属性值不能为空", -1);
        } else {
            //设置属性值
            $data=$this->set($data);
            //更新数据库记录
            $result = Db::table($this->table)->where("uid", $this->uid)->update($data);
            return $result;
        }
        return false;
    }

    /**
     * @param array $attrArray
     * @return array
     */
    protected function set($attrArray)
    {
        if (!is_array($attrArray)) {
            exception("user 中 set 的 attrArray 参数必须是array类型", -2);
            abort(500);
        }
        $result=[];
        //改变所有key为小写
        $attrArray=array_change_key_case($attrArray);
        $keys=get_object_vars($this);
        array_walk($attrArray, function ($value, $key) use(&$result,$keys) {
            //检测属性
            if (array_key_exists($key,$keys)){
                $this->$key = $value;
                $result[$key]=$value;
            }
//            dump($result);
        });
        return $result;
    }

    /**
     * @param array $data
     * @return $this|bool
     */
    public function add($data = [])
    {
        //设置属性值
        $data=$this->set($data);
        dump($data);
        //更新数据库记录
        $result = Db::table($this->table)->where("uid", $this->uid)->insertGetId($data);
        if (!empty($result)) {
            $this->uid = $result;
            return $this;
        }
        return false;
    }

    /**
     * 设置指定user可用
     */
    public function enabled()
    {
        if (empty($this->uid)) {
            exception("section 对象 enabled 方法 uid不可为空", -2);
        } else {
            $result = Db::table($this->table)->where("uid", $this->uid)->update(["availability" => 1]);
            return $result;
        }
        return false;
    }

    /**
     * 设置指定User 不可用
     */
    public function disabled()
    {
        if (empty($this->uid)) {
            exception("section 对象 enabled 方法 uid不可为空", -2);
        } else {
            $result = Db::table($this->table)->where("uid", $this->uid)->update(["availability" => 0]);
            return $result;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function del()
    {
        if (empty($this->uid)) {
            exception("section 对象 del 方法 uid不可为空", -2);
        } else {
            $result = Db::table($this->table)->where("uid", $this->uid)->delete();
            return !empty($result);
        }
        return false;
    }

    /**
     * @param array $expression
     * @param $page
     * @return array|false|mixed|PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserData($expression,$page)
    {
        if (!is_array($expression)) {
            exception("Section 中 query 的 expression 参数必须是array类型", -2);
            abort(500);
        }
        $result = Db::table($this->table)->where($expression)->page($page["page"],$page["limit"])->select();
        return empty($result) ? false : $result;
    }
    public function checkOpenid($openid){
        $result=Db::table($this->table)->where("openid",$openid)->find();
        return $result;
    }


    public function verify($username, $password)
    {
        $result = Db::table($this->table)->where([
            "username" => $username,
            "password" => $password
        ])->find();
        if ($result) {
            $this->set($result);
            return $this;
        } else {
            return false;
        }
    }


}