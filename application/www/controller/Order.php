<?php
namespace app\www\controller;

use think\Config;
use think\Controller;
use think\Loader;
use think\Request;
use think\Url;
use think\Session;

class Order extends Base
{
    private $userInfo = '';
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
        $this->userInfo = $userInfo;
    }
    public function index()
    {
        $order = Loader::model('OrderService', 'service');

        $key = input('post.key');
        $type = input('param.type');
        if (!$type) {
            $type = '';
        }
        $params = array(
            'count'=> 12,
            'type'=> $type,
            'key' => $key
        );
        $resultOrder = $order->getList($params);
        if ($resultOrder['status'] != 200) {
            $this->error('获取订单信息失败');
        }

        $this->assign('OrderList', $resultOrder['data']);
        $this->assign('total', $resultOrder['total']);
        $this->assign('page', $resultOrder['page']);
        $this->assign('key', $key);
        // 模板输出
        return $this->fetch('index');
    }

    public function detail()
    {
        $orderDetail = array();
        $userDetail = array();
        $oid = input('param.id');
        $order = Loader::model('OrderService', 'service');
        $params = array(
            'no' => $oid
        );
        $resultOrder = $order->getInfo($params);
        if ($resultOrder['status'] != 200) {
            $this->error('获取订单信息失败');
        }
        $orderDetail = $resultOrder['data'];

        $params = array(
            'id' => $orderDetail['id']
        );
        $resultRisk = $order->getAudit($params);
        if ($resultOrder['status'] != 200) {
            $this->error('获取订单信息失败');
        }
        $orderRisk = $resultRisk['data'];

        $user = Loader::model('UserService', 'service');
        $resultUser = $user->getUserDetail($orderDetail['uid']);
        if ($resultUser['status'] != 200) {
            $this->error('获取用户信息失败');
        }
        $userDetail = $resultUser['data'];


        $endDay = time();
        if ($orderDetail['repay_time'] > 0) {
            $endDay = $orderDetail['repay_time'];
        }
        $orderDetail['expireDay'] = round(($endDay - $orderDetail['expire_time'])/3600/24);

        $this->assign('OrderInfo', $orderDetail);
        $this->assign('OrderRisk', $orderRisk);
        $this->assign('UserInfo', $userDetail);
        return $this->fetch('detail');
    }
}