<?php

namespace app\index\controller;

use app\index\controller\Base;
use think\Exception;

class Index extends Base {

    public function index() {
        return $this->fetch();
    }

    public function wellcome() {
        return '欢迎使用易考试后台';
    }

}
