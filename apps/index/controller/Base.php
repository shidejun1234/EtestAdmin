<?php

namespace app\index\controller;

use think\Controller;

class Base extends Controller {

    public function _initialize() {
        if (!(request()->session('username'))) {
            $this->error('请先登录', 'Login/login');
        }
    }

}
