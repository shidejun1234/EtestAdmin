<?php

namespace app\index\controller;

use app\index\controller\Base;
use think\Db;

class User extends Base {

    /**
     * @return mixed
     */
    public function lst() {
        return $this->fetch();
    }

    /**
     * 删除用户
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del() {
        if (request()->isPost()) {
            $ids = input('post.ids');
            $res = db('user')
                ->where("id in($ids)")
                ->delete();
            print_r($this->toJson(0,'success',[$res]));
        }
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserList() {
        $param['order_name'] = empty($this->request->get('field')) ?'create_time':$this->request->get('field');
        $param['order_type'] = empty($this->request->get('order')) ?'desc':$this->request->get('order');
        if(empty($this->request->get('order'))){
            $param['order_name']  ='create_time';  //改成  最初始 默认的排序字段
            $param['order_type']  ='desc';  //改成  最初始 默认的排序字段
        }
        $list = db('user')
            ->alias('a')
            ->field(['a.id','a.nickName','a.avatarUrl','a.create_time','b.name'])
            ->order($param['order_name'], $param['order_type'])
            ->join('subject b','a.cur_subject = b.id','left')
            ->select();
        $count = db('user')
            ->count('id');
        echo $this->json2lay(0, '用户列表', $list, $count);
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
     * @param int $code
     * @param string $message
     * @param array $data
     * @param $count
     * @return false|string
     */
    private function json2lay($code = 0, $message = 'success', $data = [], $count) {
        $arr = [];
        $arr['code'] = $code;
        $arr['message'] = $message;
        $arr['count'] = $count;
        $arr['data'] = $data;
        return json_encode($arr, 320);
    }

}
