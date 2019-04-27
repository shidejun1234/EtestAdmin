<?php


namespace app\index\controller;

use app\index\controller\Base;


class Question extends Base {

    /**
     * @return mixed
     */
    public function lst() {
        return $this->fetch();
    }

    public function add() {
        if (request()->isPost()) {
            $param = request()->param()['data'];
            $title = $param['title'];
            $subject = $param["subject"];
            $type = $param["type"];
            $answer = $param["answer"];
            $analysis = $param["analysis"];
            $create_time = date("Y-m-d H:i:s");
            $optionList=$param['options[op'];
            $options = [];
            foreach ($optionList as $key=>$val){
                $options[] = ['key' => $key, 'val' => $val];
            }
            $options = json_encode($options,320);
            $data = [
                'title' => $title,
                'subject' => $subject,
                'type' => $type,
                'options' => $options,
                'answer' => $answer,
                'analysis'=>$analysis,
                'create_time' => $create_time];
            $res = db('question')
                ->insert($data);
            if ($res){
                echo '添加成功';
            }else{
                echo '添加失败';
            }
            return;
        }
        $subject = db('subject')
            ->field(['id', 'name'])
            ->select();
        $this->assign(['subject' => $subject]);
        return $this->fetch();
    }

    /**
     * 修改科目
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function edit() {
        if (request()->isPost()) {
            $param = request()->param()['data'];
            $id = $param['id'];
            $title = $param['title'];
            $subject = $param["subject"];
            $type = $param["type"];
            $answer = $param["answer"];
            $analysis = $param["analysis"];
            $create_time = date("Y-m-d H:i:s");
            $optionList=$param['options[op'];
            $options = [];
            foreach ($optionList as $key=>$val){
                $options[] = ['key' => $key, 'val' => $val];
            }
            $options = json_encode($options,320);
            $data = [
                'title' => $title,
                'subject' => $subject,
                'type' => $type,
                'options' => $options,
                'answer' => $answer,
                'analysis'=>$analysis,
                'create_time' => $create_time];
            $res = db('question')
                ->where('id', $id)
                ->update($data);
            if ($res){
                echo '修改成功';
            }else{
                echo '修改失败';
            }
            return;
        }
        $id=input('get.id');
        $list=db('question')
            ->where('id',$id)
            ->find();
        $subject = db('subject')
            ->field(['id', 'name'])
            ->select();
        $options_json=json_decode($list['options']);
        $options=[];
        foreach ($options_json as $item){
            $options[$item->key]=$item->val;
        }
        $optionsLength=count($options);
        $this->assign(['subject' => $subject,'list'=>$list,'options'=>$options,'optionsLength'=>$optionsLength]);
        return $this->fetch();
    }

    /**
     * 删除科目
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del() {
        if (request()->isPost()) {
            $ids = input('post.ids');
            $res = db('question')
                ->where("id in($ids)")
                ->delete();
            print_r($this->toJson(0, 'success', [$res]));
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
    public function getQuestionList() {
        $param['order_name'] = empty($this->request->get('field')) ? 'create_time' : $this->request->get('field');
        $param['order_type'] = empty($this->request->get('order')) ? 'desc' : $this->request->get('order');
        if (empty($this->request->get('order'))) {
            $param['order_name'] = 'create_time';  //改成  最初始 默认的排序字段
            $param['order_type'] = 'desc';  //改成  最初始 默认的排序字段
        }
        $list = db('question')
            ->alias('a')
            ->field([
                'a.id',
                'a.title',
                'a.type',
                'a.options',
                'a.answer',
                'a.analysis',
                'a.subject',
                'a.create_time',
                'b.name'])
            ->order($param['order_name'], $param['order_type'])
            ->join('subject b', 'a.subject = b.id', 'left')
            ->select();
        $count = db('question')
            ->count('id');
        echo $this->json2lay(0, '题目列表', $list, $count);
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