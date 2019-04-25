<?php
namespace app\common\validate;
use think\Validate;
class User extends Validate
{
    protected $rule = [
        'username'  =>  'require|max:25|unique:user',
    ];

    protected $message  =   [
        'username.require' => '用户名必须填写',
        'username.max' => '用户名长度不得大于25位',
        'username.unique' => '用户名不得重复',

    ];

    protected $scene = [
        'add'  =>  ['username'],
        'edit'  =>  ['username'],
    ];




}
