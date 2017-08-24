<?php
namespace app\www\controller;

use app\common\controller\CommonController;
use think\Loader;
use think\Session;
use think\Cookie;
use app\common\tools\Token;
/**
 * 模块级控制器基类
 * @package app\www\controller
 */
class Base extends CommonController
{
    public function _initialize()
    {
        parent::_initialize();
        // TODO:自定义的初始化
    }
    /**
     * 模块级初始化
     */
    protected function init()
    {
        // TODO:自定义的初始化

        $order = Loader::model('OrderService','service');
        $siteInfo =  $order->getSiteInfo();
        $this->assign('siteTitle', $siteInfo->name);
        $this->assign('siteKey', $siteInfo->key);
        $this->assign('loanRate', $siteInfo->rate);  // 未使用
        $this->assign('loanCost', $siteInfo->cost);
        $this->assign('loanManage', $siteInfo->manage);
        $this->assign('loanPeriod', $siteInfo->period);
        $this->assign('loanBankroll', $siteInfo->bankroll);
        $userInfo = array(
            'role' => 0,
        );
        $this->assign('userInfo', $userInfo);
    }
    /**
     * 判断是否登录
     */
    protected function isLogin()
    {
        // TODO:自定义的登录判断条件
        $token = Cookie::get('token');
        if ($token) {
            $userInfo = Token::decode($token);
            Session::set('userInfo', $userInfo);
        } else {
            $userInfo = Session::get('userInfo');
        }

        if (!$userInfo) {
            $this->error('请先登录', url('Www/Login/index'));
        }
        $arrUserInfo = array(
            'id' => $userInfo->iss,
            'phone' => $userInfo->phone,
            'username' => $userInfo->username,
            'role' => $userInfo->role,
            'expires' => $userInfo->expires
        );
        $this->assign('userInfo', $arrUserInfo);

        return $userInfo;
    }
    /**
     * 判断是否是管理员
     */
    protected function isAdmin()
    {
        // TODO:自定义的权限判断条件
        $userInfo = $this->isLogin();
        if ($userInfo->role < 50) {
            $this->error('没有权限访问', url('Www/Login/index'));
        }
        return $userInfo;
    }

    /**
     * 全局空操作函数
     */
    public function _empty($name)
    {
        $request = request();
        echo "当前模块名称是" . $request->module().'<br/>';
        echo "当前控制器名称是" . $request->controller().'<br/>';
        echo "当前操作名称是" . $request->action().'<br/>';
        //把所有城市的操作解析到city方法
        return $name.'方法没有找到！'.'<br/>';
    }
}