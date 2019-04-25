<?php


namespace app\index\controller;

use app\index\controller\Base;
use think\Exception;


class Subject extends Base {

    /**
     * @return mixed
     */
    public function lst() {
        return $this->fetch();
    }

    public function add() {
        $name=input('post.name');
        $create_time = date("Y-m-d H:i:s");
        $data=[
            'name'=>$name,
            'create_time'=>$create_time];
        $res = db('subject')
            ->insert($data);
        print_r($this->toJson(0,'success',[$res]));
    }

    /**
     * 修改科目
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function edit() {
        if (request()->isPost()) {
            $id = input('post.id');
            $name = input('post.name');
            $data = ['name' => $name];
            $res = db('subject')
                ->where('id', $id)
                ->update($data);
            print_r($this->toJson(0,'success',[$res]));
        }
    }

    /**
     * 删除科目
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del() {
        if (request()->isPost()) {
            $ids = input('post.ids');
            try{
                $res = db('subject')
                    ->where("id in($ids)")
                    ->delete();
                print_r($this->toJson(0,'success',[$res]));
            }catch (Exception $e){
                print_r($this->toJson(1,'error','删除失败'));
            }
        }
    }

    /**
     * @param int $code
     * @param string $message
     * @param array $data
     * @return false|string
     */
    private function toJson($code = 200, $message = 'success', $data = []) {
        $arr = [];
        $arr['code'] = $code;
        $arr['message'] = $message;
        $arr['data'] = $data;
        return json_encode($arr, 320);
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSubjectList() {
        $param['order_name'] = empty($this->request->get('field')) ?'create_time':$this->request->get('field');
        $param['order_type'] = empty($this->request->get('order')) ?'desc':$this->request->get('order');
        if(empty($this->request->get('order'))){
            $param['order_name']  ='create_time';  //改成  最初始 默认的排序字段
            $param['order_type']  ='desc';  //改成  最初始 默认的排序字段
        }
        $list = db('subject')
            ->order($param['order_name'], $param['order_type'])
            ->select();
        $count = db('subject')
            ->count('id');
        echo $this->json2lay(0, '科目', $list, $count);
    }

    /**
     * @param int $code
     * @param string $message
     * @param array $data
     * @param $count
     * @return false|string
     */
    private function json2lay($code = 200, $message = 'success', $data = [], $count) {
        $arr = [];
        $arr['code'] = $code;
        $arr['message'] = $message;
        $arr['count'] = $count;
        $arr['data'] = $data;
        return json_encode($arr, 320);
    }

}