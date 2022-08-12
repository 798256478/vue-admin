<?php

namespace app\admin\controller;

use app\admin\model\Group as GroupModel;
use app\admin\validate\Group as GroupValidator;
use app\common\controller\Backend;
use think\Db;
use think\Exception;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Request;

/**
 * 用户组
 *
 */
class Group extends Backend
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * (用户组集合)
     * @ApiTitle    (用户组集合)
     * @ApiSummary  (所有用户组集合)
     * @ApiMethod   (GET)
     */
    public function all()
    {
        $list = GroupModel::order('create_time','desc')->field(['id','name'])->select();
        $this->success("获取成功", [
            'list' => $list
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
        $list = GroupModel::order('create_time','desc')->append(['is_super'])->paginate($params['limit']);
        foreach ($list as &$val){
            $val['api_ids'] = $val['api_ids']?json_decode($val['api_ids'], true):[];
        }
        $this->success("获取成功", [
            'list' => $list->items(),
            'total' => $list->total()
        ]);
    }

    /**
     * (添加用户组)
     * @ApiTitle    (添加用户组)
     * @ApiSummary  (添加用户组)
     * @ApiMethod   (POST)
     *
     * @ApiParams   (name="name", type="string", required=true, description="组名")
     * @ApiParams   (name="desc", type="string", required=false, description="描述")
     * @ApiParams   (name="api_ids", type="string", required=true, description="权限ids")
     */
    public function add()
    {
        $params = Request::post();

        $validate = validate(GroupValidator::class);
        if(!$validate->scene('add')->check($params)){
            $this->error($validate->getError());
        }

        $exists = GroupModel::where('name',$params['name'])->value('id');
        if($exists){
            $this->error("角色名称已存在");
        }
        $role = new GroupModel();
        $role->name = $params['name'];
        $role->api_ids = !empty($params['api_ids'])?json_encode(array_unique($params['api_ids'])):'';
        $role->desc = !empty($params['desc']) ?$params['desc']:"";
        $role->save();
        $this->success("添加成功");
    }

    /**
     * (编辑用户组)
     * @ApiTitle    (编辑用户组)
     * @ApiSummary  (编辑用户组)
     * @ApiMethod   (POST)
     *
     * @ApiParams   (name="id", type="integer", required=true, description="ID")
     * @ApiParams   (name="name", type="string", required=true, description="组名")
     * @ApiParams   (name="desc", type="string", required=false, description="描述")
     * @ApiParams   (name="api_ids", type="string", required=true, description="权限ids")
     */
    public function edit($ids = null)
    {
        $params = Request::post();

        $validate = validate(GroupValidator::class);
        if(!$validate->scene('edit')->check($params)){
            $this->error($validate->getError());
        }

        $exists = GroupModel::where('name',$params['name'])->where('id','<>',$params['id'])->value('id');
        if($exists){
            $this->error("角色名称已存在");
        }

        $role = GroupModel::findOrFail($params['id']);
        $role->name = $params['name'];
        $role->api_ids = !empty($params['api_ids'])?json_encode(array_unique($params['api_ids'])):'';
        $role->desc = !empty($params['desc']) ?$params['desc']:"";
        $role->save();
        Cache::rm(config("extra.role_api_path_redis_key") . $role->id);//删除角色的权限缓存
        $this->success("修改成功");
    }

    /**
     * (删除用户组)
     * @ApiTitle    (删除用户组)
     * @ApiSummary  (删除用户组)
     * @ApiMethod   (POST)
     *
     * @ApiParams   (name="id", type="integer", required=true, description="ID")
     */
    public function delete()
    {
        $params = Request::post();

        $validate = validate(GroupValidator::class);
        if(!$validate->scene('delete')->check($params)){
            $this->error($validate->getError());
        }

        $hasRelated = \app\admin\model\User::where('group_id',$params['id'])->find();
        if($hasRelated){
            $this->error("角色有用户关联，不能删除");
        }
        GroupModel::destroy($params['id']);
        $this->success("删除成功");
    }

}
