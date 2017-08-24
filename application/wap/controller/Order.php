<?php
namespace app\wap\controller;

use app\common\service\UserService;
use app\common\service\LogService;

use think\Config;
use think\Controller;
use think\Loader;
use think\Request;
use think\Url;
use think\Session;
use llpay\Pay;

class Order extends Base
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
        $userKey = parent::getUserInfo();
        $userInfo = array(
            'id' => 1,
            'username' => '',
            'phone' => '',
            'phonestatus' => 0,
            'idcard' => 0,
            'bankcard' => 0,
            'work' => 0,
            'contacts' => 0,
            'province' => 0,
            'city' => 0,
            'area' => 0,
            'address' => '',
            'period' => '',
            'invite_code' => '',
            'email' => '',
            'qq' => '',
            'degrees' => '',
            'marriage' => '',
            'role' => -1,
            'created_time' => 0,
        );
        if ($userKey) {
            $user = Loader::model('UserService', 'service');
            $userResult = $user->getUserInfo($userKey->iss);
            if ($userResult['status'] == 200) {
                $userInfo = $userResult['data'];
            }
        }
        $verifyStatus = 4;
        if ($userInfo['idcard'] == 0) {
            $verifyStatus -= 1;
        }
        if ($userInfo['bankcard'] == 0) {
            $verifyStatus -= 1;
        }
        if ($userInfo['work'] == 0) {
            $verifyStatus -= 1;
        }
        if ($userInfo['contacts'] == 0) {
            $verifyStatus -= 1;
        }
        $this->assign('UserInfo', $userInfo);
        $this->assign('VerifyStatus', $verifyStatus);
        // 模板输出
        return $this->fetch('index');
    }
    public function confirm()
    {
        $userInfo = parent::isLogin();
        $no = input('param.id');
        if ($no == '' || $no == 'undefined') {
            $this->error('参数错误','Wap/Order/index');
            return;
        }

        $order = Loader::model('OrderService', 'service');
        $params = array(
            'uid' => $userInfo->iss,
            'no' => $no
        );
        $resultOrder = $order->getInfo($params);
        if ($resultOrder['status'] != 200) {
            $this->error($resultOrder['message'],'Wap/Order/index');
            return;
        }

        $OrderInfo = $resultOrder['data'];
        $OrderCost = $order->getCost(array(
            'limit' => $OrderInfo['limit'],
            'day' => $OrderInfo['period'],
        ));
        $this->assign('no', $no);
        $this->assign('OrderInfo', $OrderInfo);
        $this->assign('OrderCost', $OrderCost);
        return $this->fetch('confirm');
    }

    /**
     * 用户借款协议
     */
    public function agreement() {
        return $this->fetch('agreement');
    }
    /**
     * 平台服务协议
     */
    public function service()
    {
        return $this->fetch('agreement_service');
    }
    /**
     * 授权扣款委托书
     */
    public function pay()
    {
        return $this->fetch('agreement_pay');
    }

    public function getList()
    {
        $userInfo = parent::isLogin();
        $data = array();
        $order = Loader::model('OrderService', 'service');
        $params = array(
            'uid' => $userInfo->iss,
        );
        $resultOrder = $order->getList($params);
        if ($resultOrder['status'] != 200) {

        }
        $this->assign('orderList', $resultOrder['data']);
        $this->assign('total', $resultOrder['total']);
        $this->assign('page', $resultOrder['page']);
        return $this->fetch('list');
    }
    public function detail()
    {
        $userInfo = parent::isLogin();
        $oid = input('param.id');
        if ($oid == '' || $oid == 'undefined') {
            $this->error('参数错误','Wap/Order/index');
            return;
        }
        $order = Loader::model('OrderService', 'service');
        $params = array(
            'uid' => $userInfo->iss,
            'no' => $oid
        );
        $resultOrder = $order->getInfo($params);
        if ($resultOrder['status'] != 200) {
            $this->error($resultOrder['message']);
            return;
        }
        $data = $resultOrder['data'];
        $endDay = time();
        if ($data['repay_time'] > 0) {
            $endDay = $data['repay_time'];
        }
        $data['expireDay'] = round(($endDay - $data['expire_time'])/3600/24);
        $this->assign('OrderInfo', $data);
        return $this->fetch('detail');
    }
    /**
     * 订单延期申请
     */
    public function delay()
    {
        $userSign = parent::isLogin();
        $oid = input('post.id');
        $isRepay = 0; // 是否为重新支付订单

        $order = Loader::model('OrderService', 'service');
        $params = array (
            'uid' => $userSign->iss,
            'id' => $oid
        );
        $resultOrder = $order->getInfo($params);

        if ($resultOrder['status'] != 200) {
            $this->error($resultOrder['message']);
            return;
        }
        $orderInfo = $resultOrder['data'];

        $resultDeduct = $order->delay($params);

        if ($resultDeduct['status'] >= 300) {
            $this->error($resultDeduct['message']);
            return;
        }
        if ($resultDeduct['status'] == 201) {
            $isRepay = 1;
        }

        $this->assign('IsRepay', $isRepay);
        $this->assign('OrderInfo', $orderInfo);
        $this->assign('DecuctInfo', $resultDeduct['data']);
        return $this->fetch('delay');
    }

    /**
     * 提交续期支付
     * @return string
     */
    public function delayPay()
    {
        if (!Request::instance()->isPost()) {
            $this->error('提交参数错误');
        }
        $userSign = parent::isLogin();

        $oid = input('post.oid');
        $id = input('post.id');
        $no = input('post.no');
        $token = input('post.token');

        $user = Loader::model('UserService','service');
        $userResult =  $user->getUserDetail($userSign->iss);

        if ($userResult['status'] != 200) {
            $this->error($userResult['message']);
            return;
        }
        $userInfo = $userResult['data'];

        $order = Loader::model('OrderService', 'service');
        $params = array (
            'uid' => $userSign->iss,
            'id' => $oid
        );
        $resultOrder = $order->getInfo($params);

        if ($resultOrder['status'] != 200) {
            $this->error($resultOrder['message']);
            return;
        }
        $orderInfo = $resultOrder['data'];


        $resultDeduct = $order->getDeductInfo(array('id'=> $id, 'no'=> $no));
        if ($resultDeduct['status'] != 200) {
            $this->error($resultDeduct['message']);
            return;
        }
        $DeductInfo = $resultDeduct['data'];

        $params = array(
            'user_id' => $userInfo['id'],
            'user_phone' => $userInfo['phone'],
            'user_mail' => $userInfo['email'],
            'created_time' => $userInfo['created_time'],
            'acct_name' => $userInfo['bankcardDetail']['username'],
            'id_no' => $userInfo['bankcardDetail']['idcard'],
            'card_no' => $userInfo['bankcardDetail']['no'],
            'no_order' => $DeductInfo['created_time'],
            'dt_order' => date('YmdHis',$DeductInfo['created_time']),
            'name_goods' => $orderInfo['period'].'天'.$orderInfo['limit'].'元产品续期',
            'info_order' => '第'.$DeductInfo['pay_type'].'次续期费用',    // 订单描述
            'money_order' => $DeductInfo['amount'], // 续期金额 银行扣款金额
            'no_agree' => $userInfo['bankcardDetail']['agreeno'],      // 签约协议号
            'valid_order' => 120,  // 订单有效时间 默认120分钟

            'notify_url' => url('Wap/Order/notifyDelay',[],true,true),
            'return_url' => url('Wap/Order/resultDelay',[],true,true)
        );

        $pay = new Pay();
        $htmlFormInstallment = $pay->Installment($params);

        return $htmlFormInstallment;
    }

    /**
     * 支付回调
     */
    public function notifyDelay()
    {
        $pay = new Pay();
        $result = $pay->InstallmentNotify();
        if ($result['status'] == 301) {
            die("{'ret_code':'9999','ret_msg':'验签失败'}");
        }
        if($result['status'] == 200) {
            $data = $result['data'];
            file_put_contents(LOG_PATH."delay.txt", "订单时间".$data['no_order'].":用户续期支付返回结果:\r\n".json_encode($data)."\r\n\r\n", FILE_APPEND);
            if($data['result_pay'] == "SUCCESS"){
                //请在这里加上商户的业务逻辑程序代(更新订单状态、入账业务)
                //——请根据您的业务逻辑来编写程序——
                //payAfter($llpayNotify->notifyResp);
                // TODO: 纪录支付成功日志
                $order = Loader::model('OrderService', 'service');
                $orderResult = $order->updateDeductOrderInfo($data);
                file_put_contents(LOG_PATH."delay.txt", "订单时间".$data['no_order'].":用户续期保存结果:\r\n".json_encode($orderResult)."\r\n\r\n", FILE_APPEND);
                if ($orderResult['status'] != 200) {
                    // 纪录日志还款纪录和订单修改失败
                } else {
                    $orderInfo = $orderResult['data'];
                    $uid = $orderInfo['uid'];
                    $no = $orderInfo['created_time'];
                    $date = date('Y-m-d',$orderInfo['expire_time']);
                    $amount = $orderInfo['repay_cost'];
                    $state = 0;
                    $params = array(
                        'user_id' => $uid,            // 用户在商户系统中的标识

                        'repayment_no' => $no,        // * 还款计划编号
                        'repayment_date' => $date,    // * 计划还款日期 2010-07-06
                        'repayment_amount' => $amount,// * 计划还款金额
                    );
                    if($state == 1) {
                        $params['state'] = 1;     // - 还款状态 1:终止还款
                    }
                    $pay = new Pay();
                    $data = $pay->changeRepay($params);
                    if ($data['status'] !== 200) {
                        // 纪录日志 还款计划修改失败
                        file_put_contents(LOG_PATH."delay.txt", "订单时间".$orderInfo['created_time'].":还款计划修改失败:\r\n\r\n", FILE_APPEND);
                    } else {
                        // 纪录日志 还款计划修改成功
                        file_put_contents(LOG_PATH."delay.txt", "订单时间".$orderInfo['created_time'].":还款计划修改成功:\r\n\r\n", FILE_APPEND);
                    }
                }


            }
            die("{'ret_code':'0000','ret_msg':'交易成功'}"); // 请不要修改或删除
        }

    }
    /**
     * 支付回显 支付结果页面
     * @return \think\response\Json
     */
    public function resultDelay()
    {
        if (!Request::instance()->isPost()) {

        }
        $pay = new Pay();
        $payResult = $pay->InstallmentReturn();

        $order = Loader::model('OrderService', 'service');
        $order->saveDeductOrderInfo($payResult['data']);

        $this->assign('PayResult', $payResult['data']);
        $this->assign('Result', $payResult);
        return $this->fetch('delay_result');
    }
}
