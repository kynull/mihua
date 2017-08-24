<?php
namespace app\common\model;


class Orders extends Base
{
    // 设置当前模型对应的完整数据表名称
    // protected $table = 'mihua_orders';
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }

    public function UserDetail()
    {
        return $this->hasOne('User','id', 'uid')->field('id,username,phone,idcard,bankcard,work,contacts,status,created_time');
    }
}