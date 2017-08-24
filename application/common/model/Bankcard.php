<?php
/**
 * Created by PhpStorm.
 * User: carey
 * Date: 17/6/2
 * Time: 下午12:10
 */

namespace app\common\model;


class Bankcard extends Base
{
// 设置当前模型对应的完整数据表名称
    protected $table = 'mihua_bankcard';
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }
}