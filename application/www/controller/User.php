<?php
namespace app\www\controller;

use think\Config;
use think\Controller;
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
        parent::init();
        $userInfo = parent::isAdmin();
        if (!$userInfo) {
            // 权限为空
        }
    }
    public function index()
    {
        $userInfo = parent::isAdmin();
        if (!$userInfo) {
            // 权限为空
        }
        $key = input('post.key');
        $type = input('param.type');
        if (!$type) {
            $type = 0;
        }

        $user = Loader::model('UserService', 'service');
        $params = array(
            'count'=> 12,
            'type'=> $type,
            'key' => $key
        );
        $result = $user->getList($params);
        if ($result['status'] != 200) {
            $this->error('获取用户信息失败');
        }
        $this->assign('List', $result['data']);
        $this->assign('total', $result['total']);
        $this->assign('page', $result['page']);
        $this->assign('key', $key);
        return $this->fetch('index');
    }
    public function idcard()
    {
        $userInfo = parent::isAdmin();
        if (!$userInfo) {
            // 权限为空
        }
        $id = input('param.id');
        if ($id == '' || $id == 'undefined') {
            $this->error('参数错误','www/User/index');
            return;
        }
        $user = Loader::model('UserService', 'service');
        $result = $user->getUserDetail($id);
        if ($result['status'] != 200) {
            $this->error('获取用户信息失败');
        }
        $this->assign('Info', $result['data']);
        return $this->fetch('idcard');
    }
    public function contacts()
    {
        $userInfo = parent::isAdmin();
        if (!$userInfo) {
            // 权限为空
        }
        $id = input('param.id');
        if ($id == '' || $id == 'undefined') {
            $this->error('参数错误','www/User/index');
            return;
        }
        $user = Loader::model('UserService', 'service');
        $result = $user->getUserDetail($id);
        if ($result['status'] != 200) {
            $this->error('获取用户信息失败');
        }
        $this->assign('Info', $result['data']);
        return $this->fetch('contacts');
    }
    public function bankcard()
    {
        $userInfo = parent::isAdmin();
        if (!$userInfo) {
            // 权限为空
        }
        $id = input('param.id');
        if ($id == '' || $id == 'undefined') {
            $this->error('参数错误','www/User/index');
            return;
        }
        $user = Loader::model('UserService', 'service');
        $result = $user->getUserDetail($id);
        if ($result['status'] != 200) {
            $this->error('获取用户信息失败');
        }
        $this->assign('Info', $result['data']);
        return $this->fetch('bankcard');
    }
    public function work()
    {
        $userInfo = parent::isAdmin();
        if (!$userInfo) {
            // 权限为空
        }
        $id = input('param.id');
        if ($id == '' || $id == 'undefined') {
            $this->error('参数错误','www/User/index');
            return;
        }
        $user = Loader::model('UserService', 'service');
        $result = $user->getUserDetail($id);
        if ($result['status'] != 200) {
            $this->error('获取用户信息失败');
        }
        $this->assign('Info', $result['data']);
        return $this->fetch('work');
    }
}