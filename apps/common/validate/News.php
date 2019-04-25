<?php
namespace app\common\validate;
use think\Validate;
class News extends Validate
{
    protected $rule = [
        'title'  =>  'require|max:25|unique:news',
    ];

    protected $message  =   [
        'title.require' => '名称必须填写',
        'title.max' => '名称长度不得大于25位',
        'title.unique' => '名称不得重复',

    ];

    protected $scene = [
        'add'  =>  ['title'],
        'edit'  =>  ['title'],
    ];




}
