<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2018/11/6
 * Time: 15:48
 */

namespace app\index\controller;


use think\Controller;

class Test extends Controller
{
    function userTest(){
        return \app\index\model\Section::get("");
    }

}