<?php

namespace app\http\middleware;

use app\admin\model\User;
use app\common\library\Token;
use think\exception\HttpResponseException;
use think\facade\Request;
use think\facade\Session;
use think\Response;

class AdminLoginAuth
{
    /**
     * 登录验证中间件
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        //get token from header
        $tokenStr = $request->header('Authorization');
        if(empty($tokenStr)){
            $response = Response::create([
                'code' => 401,
                'msg'  => "登录态失效，请重新登录",
                'time' => Request::instance()->server('REQUEST_TIME'),
                'data' => [],
            ], 'json', 0);
            throw new HttpResponseException($response);
        }
        $parts = explode(' ',$tokenStr);
        if(count($parts)!=2 || empty($parts[1])){
            $response = Response::create([
                'code' => 401,
                'msg'  => "登录态失效，请重新登录",
                'time' => Request::instance()->server('REQUEST_TIME'),
                'data' => [],
            ], 'json', 0);
            throw new HttpResponseException($response);
        }
        $token = $parts[1];
        //verify token

        $user = User::where("token", $token)->find();
        if (!$user) {
            $response = Response::create([
                'code' => 401,
                'msg'  => "登录态失效，请重新登录",
                'time' => Request::instance()->server('REQUEST_TIME'),
                'data' => [],
            ], 'json', 0);
            throw new HttpResponseException($response);
        }

        Session::set('uid', $user->id);
        Session::set('username', $user->username);
        return $next($request);
    }
}
