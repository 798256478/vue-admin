<?php

namespace app\http\middleware;

use app\admin\model\Api;
use app\admin\model\User;
use think\exception\HttpResponseException;
use think\facade\Cache;
use think\facade\Request;
use think\Response;

class ApiAuth
{
    /**
     * Api权限验证中间件
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        //查询用户信息
        $user = User::where('id',$request->session('uid'))->with('groups')->find();
        //超级管理角色直接放行
        if($user->groups && $user->groups->name=="超级管理员"){
            return $next($request);
        }
        //所有已添加的api
        $allApiPaths = Cache::get(config("extra.all_api_path_redis_key"));
        if(!$allApiPaths){//缓存中没有从数据库中查找
            $allApiPaths = Api::where([])->column('path');
            Cache::set(config("extra.all_api_path_redis_key"),$allApiPaths);
        }
        //用户有权限的api
        $roleApiPathKey = config("extra.role_api_path_redis_key") . $user->groups->id;
        $roleApiPaths = Cache::get($roleApiPathKey);
        $roleApiPaths = false;
        if(!$roleApiPaths){//缓存中没有从数据库中查找
            $roleApiPaths = Api::whereIn('id',json_decode($user->groups->api_ids, true))->column('path');
            Cache::set($roleApiPathKey,$roleApiPaths);
        }
        $routePath = trim($request->baseUrl(), '/');
        //如果访问的接口在数据库中已存在，但用户的权限中没有该接口则拦截
        if(in_array($routePath,$allApiPaths) && !in_array($routePath,$roleApiPaths)){
            $response = Response::create([
                'code' => 403,
                'msg'  => "没有权限",
                'time' => Request::instance()->server('REQUEST_TIME'),
                'data' => [],
            ], 'json', 0);
            throw new HttpResponseException($response);
        }
        return $next($request);
    }
}
