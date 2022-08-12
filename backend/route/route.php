<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use app\http\middleware\AdminLoginAuth;
use app\http\middleware\ApiAuth;
use think\facade\Route;

Route::group('admin',function(){
    //非登录操作
    Route::post('login','admin/user/login');

    //登录后操作
    Route::group('', function(){

        Route::get('userInfo','admin/user/userInfo');
        Route::get('logout','admin/user/logout');

        //用户
        Route::get('user/list','admin/user/lists');
        Route::post('user/add','admin/user/add');
        Route::post('user/edit','admin/user/edit');
        Route::post('user/delete','admin/user/delete');

        //分组
        Route::get('group/list','admin/group/lists');
        Route::get('group/all','admin/group/all');
        Route::post('group/add','admin/group/add');
        Route::post('group/edit','admin/group/edit');
        Route::post('group/delete','admin/group/delete');

        //操作记录
        Route::get('api/all','admin/api/all');

    })->middleware([AdminLoginAuth::class, ApiAuth::class]);
})->allowCrossDomain();

