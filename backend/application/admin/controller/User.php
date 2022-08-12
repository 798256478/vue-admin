<?php

namespace app\admin\controller;

use app\admin\model\User as UserModel;
use app\admin\validate\User as UserValidator;
use app\common\controller\Backend;
use think\exception\ValidateException;
use think\facade\Request;


/**
 * 用户管理
 *
 */
class User extends Backend
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * (登录)
     * @ApiTitle    (登录)
     * @ApiSummary  (登录)
     * @ApiMethod   (POST)
     *
     * @ApiParams   (name="username", type="string", required=true, description="用户名")
     * @ApiParams   (name="password", type="string", required=true, description="密码")
     */
    public function login()
    {
        $params = Request::post();

        $validate = validate(UserValidator::class);
        if(!$validate->scene('login')->check($params)){
            $this->error($validate->getError());
        }

        $user = UserModel::where('username',$params['username'])->find();
        if(!$user){
            $this->error("用户名不存在");
        }

        if($user->password != $this->getEncryptPassword($params['password'], $user->salt)){
            $this->error("密码错误");
        }

        //生成token
        $token = uuid();
        $user->logintime = time();
        $user->loginip = Request::ip();
        $user->token = $token;
        $user->save();

        $this->success('登录成功', [
            'token' => $token
        ]);
    }

    /**
     * (退出登录)
     * @ApiTitle    (退出登录)
     * @ApiSummary  (退出登录)
     * @ApiMethod   (POST)
     */
    public function logout()
    {
        $this->success('登出成功');
    }

    /**
     * (用户详情)
     * @ApiTitle    (用户详情)
     * @ApiSummary  (用户详情)
     * @ApiMethod   (GET)
     *
     * @ApiParams   (name="id", type="integer", required=false, description="用户id")
     */
    public function userInfo()
    {
        $params = Request::get();
        $uid = !empty($params['id']) ? $params['id'] : Request::session('uid');
        $user = UserModel::with(['groups'])->find($uid);
        $this->success('获取成功', [
            'name' => $user->username,
            'roles' =>  $user->groups ? [$user->groups->name] : [''],
        ]);
    }

    /**
     * (用户列表)
     * @ApiTitle    (用户列表)
     * @ApiSummary  (用户列表)
     * @ApiMethod   (GET)
     *
     * @ApiParams   (name="page", type="integer", required=true, description="页码")
     * @ApiParams   (name="limit", type="integer", required=true, description="数量")
     */
    public function lists()
    {
        $params = Request::get();
        $list = UserModel::order('create_time',$params['sort']=='asc'?'asc':'desc')->with(['groups' => function($query){
            $query->withField([
                'id',
                'name'
            ]);
        }])->paginate($params['limit']);
        foreach ($list as &$val){
            $val['logintimedesc'] = $val['logintime']?date("Y-m-d H:i", $val['logintime']):'';
            $val['is_super_admin'] = $val['groups']['name'] == config('extra.super_admin_name');
        }
        $this->success('获取成功', [
            'list' => $list->items(),
            'total' => $list->total(),
        ]);
    }

    /**
     * (添加用户)
     * @ApiTitle    (添加用户)
     * @ApiSummary  (添加用户)
     * @ApiMethod   (POST)
     *
     * @ApiParams   (name="username", type="string", required=true, description="用户名")
     * @ApiParams   (name="password", type="string", required=true, description="密码")
     * @ApiParams   (name="group_id", type="integer", required=true, description="所属组")
     */
    public function add()
    {
        $params = Request::post();

        $validate = validate(UserValidator::class);
        if(!$validate->scene('add')->check($params)){
            $this->error($validate->getError());
        }

        //检查用户名是否存在
        $exists = UserModel::where('username',$params['username'])->value('id');
        if($exists){
            $this->error("用户名已存在");
        }
        //插入数据库
        $user = new UserModel();
        $user->group_id = $params['group_id'];
        $user->salt = alnum();
        $user->username = $params['username'];
        $user->password = $this->getEncryptPassword($params['password'], $user->salt);
        $user->save();
        $this->success("添加成功");
    }

    /**
     * (编辑用户)
     * @ApiTitle    (编辑用户)
     * @ApiSummary  (编辑用户)
     * @ApiMethod   (POST)
     *
     * @ApiParams   (name="password", type="string", required=false, description="密码")
     * @ApiParams   (name="group_id", type="integer", required=true, description="所属组")
     */
    public function edit()
    {
        $params = Request::post();

        $validate = validate(UserValidator::class);
        if(!$validate->scene('edit')->check($params)){
            $this->error($validate->getError());
        }

        //检查用户是否存在
        $user = UserModel::find($params['id']);
        if(!$user){
            $this->error("用户不存在");
        }
        //更新数据库
        !empty($params['password']) && $user->password= $this->getEncryptPassword($params['password'], $user->salt);
        $user->group_id = $params['group_id'];
        $user->save();
        $this->success("修改成功");
    }

    /**
     * (删除用户)
     * @ApiTitle    (删除用户)
     * @ApiSummary  (删除用户)
     * @ApiMethod   (POST)
     *
     * @ApiParams   (name="id", type="integer", required=true, description="ID")
     */
    public function delete()
    {
        $params = Request::post();

        $validate = validate(UserValidator::class);
        if(!$validate->scene('delete')->check($params)){
            $this->error($validate->getError());
        }

        UserModel::destroy($params['id']);
        $this->success("已删除");
    }

}
