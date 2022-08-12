<?php

namespace app\admin\validate;

use think\Validate;

class User extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id'  => 'require',
        'username'  => 'require',
        'password'  => 'require|min:6',
        'group_id'  => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];

    protected $scene = [
        'login'  =>  ['username','password'],
        'add'  =>  ['username','password','group_id'],
        'edit'  =>  ['id','status','role_id'],
        'delete'  =>  ['id'],
    ];
}