<?php
namespace app\www\controller;

use think\Config;
use think\Controller;
use think\Loader;
use think\Request;
use think\Url;
use think\Session;

class Index extends Base
{
    /**
     * 需要调用的前置方法列表
     **/
    protected $beforeActionList = [
        'init'
    ];
    protected function init()
    {
        parent::init();
    }
    public function index()
    {
        $userInfo = parent::isAdmin();
        if (!$userInfo) {
            // 权限为空
        }
        // 模板输出
        return $this->fetch('index');
    }
    public function hello($name)
    {
        $userInfo = parent::isLogin();
        if (!$userInfo) {
            // 权限为空
        }
        $this->assign('keywords','站点关键字');
        $this->assign('title','首页');
        return $this->fetch('hello',['name'=>$name]);
    }
    public function static()
    {
        $this->assign('keywords','站点关键字');
        $this->assign('title','首页');
        return $this->fetch('static');
    }
}
