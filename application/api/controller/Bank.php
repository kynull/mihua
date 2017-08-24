<?php
namespace app\api\controller;


use think\Loader;
use think\Request;

use llpay\Pay;
class Bank extends Base
{
    /**
     * 还款计划变更
     * @return \think\response\Json
     */
    public function changeRepay()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {

            $uid = input('post.no_order');
            $no = input('post.no');
            $date = input('post.date');
            $amount = input('post.amount');
            $state = input('post.state');

            // TODO: 验证参数
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
                $result['status'] = $data['status'];
                $result['message'] = $data['message'];
            } else {
                $result['status'] = 200;
                $result['message'] = '银行卡信息查询成功';
                $result['data'] = $data;
            }
        }
        return json($result);
    }

    /**
     * 用户绑卡查询
     * @return \think\response\Json
     */
    public function bindlist()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $uid = input('post.uid');
            $agree_no = input('post.agreeno');
            $card_no = input('post.no');
            $offset = input('post.offset');

            // TODO: 验证参数
            if(!$offset) {
                $offset = '0';
            }
            $agree_no = '';
            $params = array(
                'user_id' => $uid,
                'no_agree' => $agree_no,
                'card_no' => $card_no,
                'offset' => $offset,
            );
            //p($params);
            $pay = new Pay();
            $queryResult = $pay->bindlist($params);

            if ($queryResult['status'] !== 200) {
                $result['status'] = $queryResult['status'];
                $result['message'] = $queryResult['message'];
            } else {
                $result['status'] = 200;
                $result['message'] = '银行卡信息查询成功';
                $result['data'] = $queryResult['data'];
            }
        }
        return json($result);
    }
    /**
     * 银行卡BIN查询
     * @return \think\response\Json
     */
    public function getBIN()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $card_no = input('post.bankcardno');

            // TODO: 验证参数
            $pay = new Pay();
            $payResult = $pay->bin(array(
                'card_no' => $card_no
            ));

            if ($payResult['status'] !== 200) {
                $result['status'] = $payResult['status'];
                $result['message'] = $payResult['message'];
            } else {
                $result['status'] = 200;
                $result['message'] = '银行卡信息查询成功';
                $result['data'] = $payResult['data'];
            }
        }
        return json($result);
    }
    /**
     * 银行卡解约
     * @return \think\response\Json
     */
    public function unbind()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $no_agree = input('post.no_agree');

            // TODO: 验证参数
            $params = array(
                'no_agree' => $no_agree
            );
            $pay = new Pay();
            $unbindResult = $pay->unbind($params);

            if ($unbindResult['status'] !== 200) {
                $result['status'] = $unbindResult['status'];
                $result['message'] = $unbindResult['message'];
            } else {
                $result['status'] = 200;
                $result['message'] = '银行卡信息查询成功';
                $result['data'] = $unbindResult['data'];
            }
        }
        return json($result);
    }

    /**
     * 通过WAP/Order/delay实现
     */
    public function installment()
    {

    }
    /**
     * 通过API/Order/doPay实现
     * 授权申请
     * @return \think\response\Json
     */
    public function oauthApply()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {

            $uid = input('post.uid');
            $no = input('post.no');
            $date = input('post.date');
            $amount = input('post.amount');
            $no_agree = input('post.no_agree');

            // TODO: 验证参数
            $params = array(
                'user_id' => $uid,            // 用户在商户系统中的标识
                'no_agree' => $no_agree,      // 用户在商户系统中的标识

                'repayment_no' => $no,        // * 还款计划编号
                'repayment_date' => $date,    // * 计划还款日期 2010-07-06
                'repayment_amount' => $amount,// * 计划还款金额
            );
            $pay = new Pay();
            $data = $pay->oauthApply($params);

            if ($data['status'] !== 200) {
                $result['status'] = $data['status'];
                $result['message'] = $data['message'];
            } else {
                $result['status'] = 200;
                $result['message'] = '银行卡信息查询成功';
                $result['data'] = $data;
            }
        }
        return json($result);
    }
    /**
     * 通过API/Order/repay实现
     * 银行卡还款扣款接口
     * @return \think\response\Json
     */
    public function repayment() {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {

            $no_order = input('post.no_order');
            $dt_order = input('post.dt_order');
            $oid_paybill = input('post.oid_paybill');

            // TODO: 验证参数
            $params = array(
                'user_id' => $no_order,      // 用户在商户系统中的标识
                'acct_name' => $no_order,    // 用户注册姓名
                'id_no' => $no_order,        // 用户注册证件号码
                'user_phone' => $no_order,   // 用户在商户系统中的登陆名
                'user_mail' => $no_order,    // 绑定的邮件
                'created_time' => $no_order, // 注册时间

                'no_order' => $dt_order,     // * 商户唯一订单号
                'dt_order' => $dt_order,     // * 商户订单时间 YYYYMMDDH24MISS 14位数字
                'name_goods' => $dt_order,   // * 商品名称
                'info_order' => $dt_order,   // - 订单描述
                'money_order' => $dt_order,  // * 还款金额
                'valid_order' => $dt_order,  // - 订单有效时间
                'repayment_date' => $dt_order, // * 计划还款日期 2010-07-06
                'repayment_no' => $dt_order,   // * 还款计划编号
                'no_agree' => $dt_order,       // * 签约协议号
                'notify_url' => $dt_order,     // * 服务器异步通知地址
            );
            $pay = new Pay();
            $data = $pay->repayment($params);

            if ($data['status'] !== 200) {
                $result['status'] = $data['status'];
                $result['message'] = $data['message'];
            } else {
                $result['status'] = 200;
                $result['message'] = '银行卡信息查询成功';
                $result['data'] = $data;
            }
        }
        return json($result);
    }
    /**
     * 通过API/Order/query实现
     * 支付结果查询
     * @return \think\response\Json
     */
    public function orderQuery()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {

            $no_order = input('post.no_order');
            $dt_order = input('post.dt_order');
            $oid_paybill = input('post.oid_paybill');

            // TODO: 验证参数
            $params = array(
                'no_order' => $no_order,
                'dt_order' => $dt_order,
            );
            if ($oid_paybill) {
                $params['oid_paybill'] = $oid_paybill;
            }
            $pay = new Pay();
            $queryResult = $pay->orderquery($params);

            if ($queryResult['status'] !== 200) {
                $result['status'] = $queryResult['status'];
                $result['message'] = $queryResult['message'];
            } else {
                $result['status'] = 200;
                $result['message'] = '银行卡信息查询成功';
                $result['data'] = $queryResult['data'];
            }
        }
        return json($result);
    }
}