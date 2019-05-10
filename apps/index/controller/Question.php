<?php


namespace app\index\controller;

use app\index\controller\Base;
use lib\MyExcel as myExcel;


class Question extends Base {

    /**
     * @return mixed
     */
    public function lst() {
        return $this->fetch();
    }

    public function readWord() {
        $file = request()->file('excel');
        if (empty($file)) {
            print_r($this->toJson(1, 'error'));
        } else {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            $filename = str_replace('\\', '/', $info->getSaveName());
            $url = './public/uploads/' . $filename;
            $data=myExcel::importExecl($url);
            $list=[];
            date_default_timezone_set("Asia/Shanghai");
            foreach ($data as $key => $value) {
                if ($key%6==1) {
                    $options=[
                        ['key' => 'A', 'val' => $data[$key+1]['A']],
                        ['key' => 'B', 'val' => $data[$key+2]['A']],
                        ['key' => 'C', 'val' => $data[$key+3]['A']],
                        ['key' => 'D', 'val' => $data[$key+4]['A']]];
                    $options = json_encode($options, 320);
                    $list[]=[
                        'title'=>$value['A'],
                        'type'=>'选择题',
                        'options'=>$options,
                        'answer'=>$value['B'],
                        'analysis'=>'该题暂无解析',
                        'subject'=>4,
                        'create_time'=>date("Y-m-d H:i:s",time())
                    ];
                }
            }
            $data = db('question')
                ->insertAll($list);
            if ($data) {
                print_r($this->toJson(0, 'success'));
            } else {
                print_r($this->toJson(1, 'error'));
            }
        }
//        $file = request()->file('word');
//        if (empty($file)) {
//            print_r($this->toJson(1, 'error'));
//        } else {
//            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
//            $filename = str_replace('\\', '/', $info->getSaveName());
//            $url = SITE_URL . '/public/uploads/' . $filename;
//            $word = new \COM("Word.application") or die("Can't start Word!");
//            $word->Documents->OPen($url);
//            $test = $word->ActiveDocument->content->text;
//            $test = mb_convert_encoding($test, "UTF-8", "GBK");
//            $pieces = explode("\r", $test);
//            $list = [];
//            date_default_timezone_set("Asia/Shanghai");
//            foreach ($pieces as $key => $value) {
//                if ($key % 6 == 0) {
//                    if ($key + 1 >= count($pieces)) {
//                        continue;
//                    }
//                    $title2 = explode("[", $value);
//                    $answer = preg_replace('/(\s+)|(])/', '', $title2[1]);
//                    $options=[
//                        ['key' => 'A', 'val' => $pieces[$key + 1]],
//                        ['key' => 'B', 'val' => $pieces[$key + 2]],
//                        ['key' => 'C', 'val' => $pieces[$key + 3]],
//                        ['key' => 'D', 'val' => $pieces[$key + 4]]];
//                    $options = json_encode($options, 320);
//                    $list[] = [
//                        'title' => $title2[0],
//                        'type' => '选择题',
//                        'options'=>$options,
//                        'answer' => $answer,
//                        'subject' => 3,
//                        'create_time' => date("Y-m-d H:i:s", time())
//                    ];
//                }
//            }
//            $data = db('question')
//                ->insertAll($list);
//            if ($data) {
//                print_r($this->toJson(0, 'success'));
//            } else {
//                print_r($this->toJson(1, 'error'));
//            }
//            $word->Quit();
//            $word = null;
//            unset($word);
//        }
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
            $optionList = $param['options[op'];
            $options = [];
            foreach ($optionList as $key => $val) {
                $options[] = ['key' => $key, 'val' => $val];
            }
            $options = json_encode($options, 320);
            $data = [
                'title' => $title,
                'subject' => $subject,
                'type' => $type,
                'options' => $options,
                'answer' => $answer,
                'analysis' => $analysis,
                'create_time' => $create_time];
            $res = db('question')
                ->insert($data);
            if ($res) {
                session('subject', $subject);
                echo '添加成功';
            } else {
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
            $optionList = $param['options[op'];
            $options = [];
            foreach ($optionList as $key => $val) {
                $options[] = ['key' => $key, 'val' => $val];
            }
            $options = json_encode($options, 320);
            $data = [
                'title' => $title,
                'subject' => $subject,
                'type' => $type,
                'options' => $options,
                'answer' => $answer,
                'analysis' => $analysis,
                'create_time' => $create_time];
            $res = db('question')
                ->where('id', $id)
                ->update($data);
            if ($res) {
                session('subject', $subject);
                echo '修改成功';
            } else {
                echo '修改失败';
            }
            return;
        }
        $id = input('get.id');
        $list = db('question')
            ->where('id', $id)
            ->find();
        $subject = db('subject')
            ->field(['id', 'name'])
            ->select();
        $options_json = json_decode($list['options']);
        $options = [];
        foreach ($options_json as $item) {
            $options[$item->key] = $item->val;
        }
        $answers='';
        if ($list['answer']!=']'){
            $answersList=json_decode($list['answer']);
            foreach ($answersList as $item) {
                $answers.=$item.',';
            }
        }
        $optionsLength = count($options);
        $this->assign([
            'subject' => $subject,
            'list' => $list,
            'options' => $options,
            'optionsLength' => $optionsLength,
            'answers'=>$answers]);
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
        $param['search_key'] = empty($this->request->get('key')) ? '' : $this->request->get('key');
        $search=[];
        if ($param['search_key']!=''){
            foreach (json_decode($param['search_key']) as $key=>$val){
                if ($val!=''){
                    $search['a.'.$key]=$val;
                }
            };
        }
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
            ->where($search)
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