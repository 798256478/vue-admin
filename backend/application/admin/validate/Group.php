<?php

namespace app\admin\validate;

use think\Validate;

class Group extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id'  => 'require',
        'name'  => 'require',
        'api_ids'  => 'array',

    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];

    protected $scene = [

        'add'  =>  ['name','api_ids'],
        'edit'  =>  ['id','name','api_ids'],
        'delete'  =>  ['id'],
    ];
}