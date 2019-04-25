<?php
namespace app\common\validate;
use think\Validate;
class Cate extends Validate
{
    protected $rule = [
        'cName'  =>  'require|max:25|unique:category',
    ];

    protected $message  =   [
        'cName.require' => '名称必须填写',
        'cName.max' => '名称长度不得大于25位',
        'cName.unique' => '名称不得重复',

    ];

    protected $scene = [
        'add'  =>  ['cName'],
        'edit'  =>  ['cName'],
    ];




}
