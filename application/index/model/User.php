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
    protected $pk = "uid";
    protected $table = "user";
    public $username;
    public $autoWriteTimestamp = "datetime";
    public $level = 1;
    protected $availability;
    public $phone;
    public $email;
    public $code;//学号
    public $openid;

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