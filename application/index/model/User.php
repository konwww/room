<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/8/18
 * Time: 11:45
 */

namespace app\index\model;


use think\Model;

class User extends Model
{
    public $uid;
    public $username;
    public $level = 1;
    public $phone;
    public $email;
    public $code;//学号
    public $openid;
    protected $availability=1;
    protected $autoWriteTimestamp = "datetime";
    protected $createTime="createTime";
    protected $updateTime="updateTime";
    protected $pk = "uid";
    protected $table = "user";

    public function enable(){
        return $this->able(true);
    }
    public function disable(){
        return $this->able(false);
    }
    protected function able($enable=true){
        if (empty($this->uid) && !is_bool($enable))return null;
        return $this->save(["availability" => (int)$enable], ["uid" => $this->uid]);
    }
}