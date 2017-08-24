<?php
namespace app\wap\controller;

use think\Config;
use think\Loader;
use think\Request;
use think\Url;
use think\Session;

class User extends Base
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
    }
    public function login(){
        return $this->fetch('login');
    }
    public function dologin()
    {
        if( !Request::instance()->isAjax() ) {
            return $this->success('Request 类型错误！');
        }
        $request = Request::instance();
        $postData = $request->param();
        if (!$postData) {
            return $this->error('数据错误！');
        }

        $loginData = array(
            'mobile'=>$postData['mobile'],
            'password'=>$postData['password']
        );
        $user = Loader::model('User','service');
        $result =  $user->login($loginData);
        unset($result['password']);
        Session::set('userinfo', $result, 'admin');
        Loader::model('Log','service')->record('登录成功');

        return $this->success('登录成功', url('index/userInfo', ['id' => $result['id']]));
    }
    public function sigout()
    {
        session::clear('admin');
        $this->success('退出成功');
    }
}
