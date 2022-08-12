<?php

namespace app\admin\controller;

use app\admin\model\Api as ApiModel;
use app\common\controller\Backend;

/**
 * api
 *
 */
class Api extends Backend
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * api集合(api集合)
     * @ApiTitle    (api集合)
     * @ApiSummary  (所有可配置api集合)
     * @ApiMethod   (GET)
     */
    public function all()
    {
        $list = ApiModel::select();
        $data = [];
        foreach ($list as $item){
            if(!isset($data[$item->group])){
                $data[$item->group]['group'] = $item->group;
            }
            $data[$item->group]['api'][] = [
                'id'=>$item->id,
                'name'=>$item->name,
            ];
        }
        $this->success("获取成功", [
            'list' => array_values($data)
        ]);
    }
}
