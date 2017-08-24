<?php
namespace app\common\controller;

use think\Config;
use think\Controller;
use think\Loader;
use think\Request;
use think\Url;
use think\Session;
use think\Lang;
/**
 * APP级控制器基类
 * @package app\common\controller
 * @author Cjky <cjky@qq.com>
 * @version 1.0
 */
class CommonController extends Controller
{
    public function _initialize()
    {
        $this->assign('keywords', '站点关键字');
        $this->assign('title', '首页');
        $this->assign('siteName', '系统名称');
        
        $this->assign([
            'list' => [
                'name'  => 'ThinkPHP',
                'email' => 'thinkphp@qq.com'
            ],
            'news' => []
        ]);
        // TODO:全局的初始化
    }
}