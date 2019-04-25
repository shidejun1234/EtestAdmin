<?php

namespace app\index\controller;

use think\Controller;
use think\Exception;

class Login extends Controller {

    /**
     * @return mixed
     */
    public function reg() {
        if (request()->session('username')&&request()->session('uid')){
            $this->success('已登录','Index/index');
        }
        if (request()->isPost()) {
            $code = request()->param('code');
            if (!captcha_check($code)) {
                $this->error('验证码错误');
            } else {
                $data=[
                    'username'=>input('post.user'),
                    'password'=>md5(input('pwd')),
                    'stats'=>1,
                    'create_time'=>date("Y-m-d H:i:s")
                ];
                try{
                    $res = db('admin')
                        ->insert($data);
                    if ($res){
                        $this->success('注册成功，前往登录', 'Login/login');
                    }else{
                        $this->error('注册失败');
                    }
                }catch (Exception $e){
                    $this->error('注册失败，该用户名已存在');
                }
            }
        }
        return $this->fetch();
    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login() {
        if (request()->session('username')&&request()->session('uid')){
            $this->success('已登录','Index/index');
        }
        if (request()->isPost()) {
            $code = request()->param('code');
            if (!captcha_check($code)) {
                $this->error('验证码错误');
            } else {
                $user = db('admin')
                    ->where('username', input('post.user'))
                    ->find();
                if ($user) {
                    if ($user['password'] == md5(input('post.pwd'))) {
                        if ($user['stats'] == '1') {
                            $this->error('用户未激活');
                        } else {
                            session('username', $user['username']);
                            session('uid', $user['id']);
                            session('stats', $user['stats']);
                            $this->success('登录成功，正在为您跳转...', 'Index/index');
                        }
                    } else {
                        $this->error('用户名或者密码错误');
                    }
                } else {
                    $this->error('用户名或者密码错误');
                }
            }
        }
        return $this->fetch();
    }

    /**
     * loginout
     */
    public function loginout(){
        session(null);
        $this->success('退出成功','Login/login');
    }

    /**
     * @param string $code
     * @return bool
     */
    public function check($code = '') {
        if (!captcha_check($code)) {
            $this->error('验证码错误');
        } else {
            return true;
        }
    }

}
