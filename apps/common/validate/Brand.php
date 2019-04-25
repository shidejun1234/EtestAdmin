<?php
namespace app\common\validate;
use think\Validate;
class Brand extends Validate
{
    protected $rule = [
        'cName'  =>  'require|max:25|unique:brand',
        'appid'  =>  'number|require|max:8|min:8|unique:brand',
    ];

    protected $message  =   [
        'cName.require' => '名称必须填写',
        'cName.max' => '名称长度不得大于25位',
        'cName.unique' => '名称不得重复',
        'appid.require' => 'appid必须填写',
        'appid.max' => 'appid长度必须是8位',
        'appid.min' => 'appid长度必须是8位',
        'appid.unique' => 'appid不得重复',

    ];

    protected $scene = [
        'add'  =>  ['cName'],
        'edit'  =>  ['cName'],
        'appid'  =>  ['appid'],
    ];




}
