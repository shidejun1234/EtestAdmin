<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/19 0019
 * Time: 下午 5:12
 */

namespace app\index\controller;

use think\Controller;
use think\Exception;
use tp_tools\Curl as Curl;


/**
 * Class Api
 * @package app\index\controller
 */
class Api extends Controller {

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSubject() {
        $list = db('subject')
            ->order('id', 'asc')
            ->select();
        echo $this->toJson(0, '科目', $list);
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
     * getOpenId
     */
    public function getOpenId() {
        if (request()->isPost()) {
            $js_code = input('post.code');
            if ($js_code != '') {
                $url = 'https://api.weixin.qq.com/sns/jscode2session';
                $params = [
                    'appid' => 'wx70461f0b0fe0ace0',
                    'secret' => '4455a34581e0fbb0636973d6355903b6',
                    'grant_type' => 'authorization_code',
                    'js_code' => $js_code
                ];
                $res = json_decode(Curl::post($url, $params));
                echo $this->toJson(200, '获取openid成功', $res);

            } else {
                echo $this->toJson(400, 'js_code为空');
            }
        } else {
            echo $this->toJson(300, '非post请求');
        }
    }

    /**
     * login
     */
    public function login() {
        if (request()->isPost()) {
            $openid = input('post.openid');
            $nickName = input('post.nickName');
            $avatarUrl = input('post.avatarUrl');
            $create_time = date("Y-m-d H:i:s");
            $data = [
                'openid' => $openid,
                'nickName' => $nickName,
                'avatarUrl' => $avatarUrl,
                'create_time' => $create_time
            ];
            try {
                $list = db('user')
                    ->insert($data);
                if ($list) {
                    $list = db('user')
                        ->field(['cur_subject'])
                        ->where('openid', $openid)
                        ->find();
                    echo $this->toJson(200, '登录成功', $list);
                } else {
                    $list = db('user')
                        ->field(['cur_subject'])
                        ->where('openid', $openid)
                        ->find();
                    echo $this->toJson(200, '登录成功', $list);
                }
            } catch (Exception $e) {
                $list = db('user')
                    ->field(['id', 'cur_subject'])
                    ->where('openid', $openid)
                    ->find();
                echo $this->toJson(200, '登录成功', $list);
            }
        }
    }

    /**
     * setCurSubject
     */
    public function setCurSubject() {
        if (request()->isPost()) {
            $cur_subject = input('post.cur_subject');
            $id = input('post.id');
            $data = ['cur_subject' => $cur_subject];
            $list = db('user')
                ->where('id', $id)
                ->update($data);
            print_r($list);
        }
    }

    /**
     * getQuestion
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getQuestion() {
        $subject = input('post.subject');
        $num = input('post.num');
        $key = input('post.key');
        switch ($key) {
            case 'rand':
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
                    ->where('subject', $subject)
                    ->limit($num)
                    ->order('RAND()')
                    ->join('subject b', 'a.subject = b.id', 'left')
                    ->select();
                echo $this->toJson(0, '题目列表', $list);
                break;
            case 'order':
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
                    ->where('subject', $subject)
                    ->order('id', 'asc')
                    ->join('subject b', 'a.subject = b.id', 'left')
                    ->select();
                echo $this->toJson(0, '题目列表', $list);
                break;
            case 'wrong':
                $user = input('post.user');
                $where['user']=['=',$user];
                $where['subject']=['=',$subject];
                $where['type']=[['=','order'],['=','wrong'],'or'];
                $list = db('test')
                    ->field(['question'])
                    ->where($where)
                    ->order('create_time', 'desc')
                    ->find();
                if ($list) {
                    $question = json_decode($list['question']);
                    $questionList = [];
                    foreach ($question as $val) {
                        if ($val->answer !== $val->checkAnswer) {
                            array_push($questionList, $val);
                        }
                    }
                    echo $this->toJson(0, 'success', $questionList);
                } else {
                    echo $this->toJson(1, 'error',['user'=>$user,'subject'=>$subject]);
                }
        }
    }

    /**
     * getTest
     */
    public function getTest() {
        $num = 20;
        $time = 60;
        $total = 100;
        $qualified = 80;
        $subject = input('post.subject');
        $type = input('post.type');
        switch ($type) {
            case 'rand':
                $count = db('question')
                    ->where('subject', $subject)
                    ->count('id');
                if ($count < $num) {
                    $num = $count;
                }
                break;
            case 'order':
                $count = db('question')
                    ->where('subject', $subject)
                    ->count('id');
                $num = $count;
                break;
            case 'wrong':
                $user = input('post.user');
                $where['user']=['=',$user];
                $where['subject']=['=',$subject];
                $where['type']=[['=','order'],['=','wrong'],'or'];
                $list = db('test')
                    ->field(['question'])
                    ->where($where)
                    ->order('create_time', 'desc')
                    ->find();
                if ($list) {
                    $question = json_decode($list['question']);
                    $questionList = [];
                    foreach ($question as $val) {
                        if ($val->answer !== $val->checkAnswer) {
                            array_push($questionList, $val);
                        }
                    }
                    $num = count($questionList);
                } else {
                    $num = 0;
                }
                break;
        }
        $list = [
            'num' => $num,
            'time' => $time,
            'total' => $total,
            'qualified' => $qualified
        ];
        echo $this->toJson(0, '试卷详情', $list);
    }

    /**
     * setTest
     */
    public function setTest() {
        if (request()->isPost()) {
            $list = json_decode(input('post.list'));
            $data = [
                'user' => $list->user,
                'subject' => $list->subject,
                'question' => json_encode($list->question, 320),
                'time' => $list->time,
                'num' => $list->num,
                'total' => $list->total,
                'qualified' => $list->qualified,
                'use_time' => $list->use_time,
                'score' => $list->score,
                'type' => $list->type,
                'create_time' => $list->create_time
            ];
            $res = db('test')
                ->insert($data);
            if ($res) {
                echo $this->toJson(0, 'success', ['添加成功']);
            } else {
                echo $this->toJson(1, 'error', ['添加失败']);
            }
        }
    }

    /**
     * getMyText
     */
    public function getMyText() {
        if (request()->isPost()) {
            $user = input('post.user');
            $subject = input('post.subject');
            $type = input('post.type');
            $list = db('test')
                ->field(['id', 'score', 'use_time', 'create_time'])
                ->where('user', $user)
                ->where('subject', $subject)
                ->where('type', $type)
                ->order('create_time', 'desc')
                ->select();
            if ($list) {
                echo $this->toJson(0, 'success', $list);
            } else {
                echo $this->toJson(1, 'error');
            }
        }
    }

    /**
     * getRewinding
     */
    public function getRewinding() {
        if (request()->isPost()) {
            $id = input('post.id');
            $type = input('type');
            switch ($type) {
                case 'all':
                    $list = db('test')
                        ->field(['question'])
                        ->where('id', $id)
                        ->find();
                    if ($list) {
                        echo $this->toJson(0, 'success', json_decode($list['question']));
                    } else {
                        echo $this->toJson(1, 'error');
                    }
                    break;
                case 'wrong':
                    $list = db('test')
                        ->field(['question'])
                        ->where('id', $id)
                        ->find();
                    if ($list) {
                        $question = json_decode($list['question']);
                        $questionList = [];
                        foreach ($question as $val) {
                            if ($val->answer !== $val->checkAnswer) {
                                array_push($questionList, $val);
                            }
                        }
                        echo $this->toJson(0, 'success', $questionList);
                    } else {
                        echo $this->toJson(1, 'error');
                    }
                    break;
                case 'orderwrong':
                    $list = db('test')
                        ->field(['question'])
                        ->where('id', $id)
                        ->where('type', 'order')
                        ->order('create_time', 'desc')
                        ->find();
                    if ($list) {
                        $question = json_decode($list['question']);
                        $questionList = [];
                        foreach ($question as $val) {
                            if ($val->answer !== $val->checkAnswer) {
                                array_push($questionList, $val);
                            }
                        }
                        echo $this->toJson(0, 'success', $questionList);
                    } else {
                        echo $this->toJson(1, 'error');
                    }
                    break;
            }
        }
    }

    /**
     * getOrderWrong
     */
    public function getOrderWrong() {
        if (request()->isPost()) {
            $id = input('post.id');
            $list = db('test')
                ->field(['question'])
                ->where('id', $id)
                ->where('type', 'order')
                ->order('create_time', 'desc')
                ->find();
            if ($list) {
                $question = json_decode($list['question']);
                $questionList = [];
                foreach ($question as $val) {
                    if ($val->answer !== $val->checkAnswer) {
                        array_push($questionList, $val);
                    }
                }
                echo $this->toJson(0, 'success', $questionList);
            } else {
                echo $this->toJson(1, 'error');
            }
        }
    }

    /**
     * searchQuestion
     */
    public function searchQuestion() {
        $key = input('get.key');
        $subject = input('get.subject');
        $list = db('question')
            ->where('title', 'like', '%' . $key . '%')
            ->where('subject', $subject)
            ->order('create_time', 'desc')
            ->select();
        if ($list) {
            echo $this->toJson(0, 'success', $list);
        } else {
            echo $this->toJson(1, 'error');
        }
    }

    /**
     * feedback
     */
    public function feedback() {
        if (request()->isPost()) {
            $user = input('post.user');
            $question = input('post.question');
            $feedback = input('post.feedback');
            $create_time = date("Y-m-d H:i:s");
            if ($question == 0) {
                $data = [
                    'user' => $user,
                    'feedback' => $feedback,
                    'create_time' => $create_time
                ];
                $res = db('feedback_me')
                    ->insert($data);
            } else {
                $data = [
                    'user' => $user,
                    'question' => $question,
                    'feedback' => $feedback,
                    'create_time' => $create_time
                ];
                $res = db('feedback')
                    ->insert($data);
            }
            if ($res) {
                echo $this->toJson(0, 'success', ['反馈成功']);
            } else {
                echo $this->toJson(1, 'error', ['反馈失败']);
            }
        }
    }

}