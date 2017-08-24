<?php
namespace app\www\controller;

use app\common\service\UserService;
use app\common\service\LogService;

use think\Config;
use think\Loader;
use think\Request;
use think\Url;
use think\Session;
use think\Cookie;

class Login extends Base
{
    /**
     * 需要调用的前置方法列表
     **/
    protected $beforeActionList = [
        'init'
    ];
    public function init()
    {
        // 登录类初始化
        parent::init();
    }
    public function index(){
        return $this->fetch('index');
    }

    public function sigout()
    {
        Session::clear('userinfo');
        Cookie::clear('token');
        $this->success('退出成功');
    }
}
