<?php
/**
 * Created by PhpStorm.
 * User: 17715
 * Date: 2019/2/3
 * Time: 23:01
 */

namespace app\index\controller\Admin;


use app\index\controller\Visitor;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Request;

class Section extends Visitor
{
    private $model;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->model = new \app\index\model\Section();
    }

    public function del($cid)
    {
        $result = $this->model->where(["cid" => $cid])->delete();
        if ($result == 1) {
            return "";
        }
        return "";
    }

    public function update($cid, $updateData)
    {
        $this->model->where(["cid" => $cid])->save(json_decode($updateData, true));
    }

    public function read($cid)
    {
        try {
            $this->model->where(["cid" => $cid])->find();
        } catch (DataNotFoundException $e) {
        } catch (ModelNotFoundException $e) {
        } catch (DbException $e) {
        }
    }

    public function insert($data)
    {
        $this->model->insertGetId($data);
    }
}