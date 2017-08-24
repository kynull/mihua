<?php
namespace app\api\controller;

use think\Loader;
use think\Request;

use llpay\Pay;

class Order extends Base
{
    /**
     * 获取站点信息
     * @return \think\response\Json
     */
    public function info()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $token =input('post.token');

            $userInfo = parent::decode($token);
            if (!$userInfo) {
                $result['status'] = 301;
                $result['message'] = '用户资料错误';
                $result['data'] = '';
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $order = Loader::model('OrderService', 'service');
                $data = $order->getSiteInfo();
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $data;
            }
        }
        // 指定json数据输出
        return json($result);
    }
    /**
     * 获取用户订单列表
     * @return \think\response\Json
     */
    public function index()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $oid =input('post.oid');
            $token =input('post.token');

            $userInfo = parent::decode($token);
            if (!$userInfo) {
                $result['status'] = 301;
                $result['message'] = '用户资料错误';
                $result['data'] = '';
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = [
                    'uid' => $userInfo->iss,
                    'id' => $oid
                ];
                $order = Loader::model('OrderService', 'service');
                $data = $order->getInfo($params);
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $data;
            }
        }
        // 指定json数据输出
        return json($result);
    }
    /**
     * 用户新增订单
     * @return \think\response\Json
     */
    public function add()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $amount =input('post.amount');
            $day = input('post.day');
            $token =input('post.token');

            $userInfo = parent::decode($token);
            if (!$userInfo) {
                $result['status'] = 301;
                $result['message'] = '用户资料错误';
                $result['data'] = '';
            } else {
                $user = Loader::model('UserService','service');
                $userResult =  $user->getUserInfo($userInfo->iss);
                if ($userResult['status'] !== 200) {
                    $result['status'] = 302;
                    $result['message'] = '用户信息获取失败';
                    $result['data'] = '';
                } else {
                    $data = $userResult['data'];
                    if ($data['idcard'] < 2) {
                        $result['status'] = 401;
                        $result['message'] = '请先完善实名认证信息';
                        $result['data'] = url('Wap/personal/idcard');
                    } else if ($data['contacts'] < 2) {
                        $result['status'] = 402;
                        $result['message'] = '请先完善紧急联系人信息';
                        $result['data'] = url('Wap/personal/contacts');
                    } else if ($data['work'] < 2) {
                        $result['status'] = 403;
                        $result['message'] = '请先完善社会身份信息';
                        $result['data'] = url('Wap/personal/work');
                    } else if ($data['bankcard'] < 2) {
                        $result['status'] = 404;
                        $result['message'] = '请先绑定收款银行卡';
                        $result['data'] = url('Wap/personal/bank');
                    } else if ($data['phonestatus'] < 2) {
                        $result['status'] = 405;
                        $result['message'] = '请完成手机运营商认证';
                        $result['data'] = url('Wap/personal/phone');
                    }
                }
            }



            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = [
                    'uid' => $userInfo->iss,
                    'amount' => $amount,
                    'day' => $day
                ];
                $order = Loader::model('OrderService','service');
                $data =  $order->add($params);
                if ($data['status'] == 200) {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                } else {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                }
            }
        }

        // 指定json数据输出
        return json($result);
    }
    /**
     * 用户确认订单信息
     * @return \think\response\Json
     */
    public function confirm()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $id = input('post.id');
            $token =input('post.token');

            $userInfo = parent::decode($token);
            if (!$userInfo) {
                $result['status'] = 301;
                $result['message'] = '用户资料错误';
                $result['data'] = '';
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = [
                    'uid' => $userInfo->iss,
                    'id' => $id
                ];
                $order = Loader::model('OrderService','service');
                $data =  $order->confirm($params);
                if ($data['status'] == 200) {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                    $user = Loader::model('UserService','service');
                    $userDetail = $user->getUserInfo($userInfo->iss);
                    $sms = new \SYS\SMS();
                    $sms->send($userDetail['data']['phone'],'您的借款申请['. $data['data']['limit'] .'元,使用'. $data['data']['period'] .'天]提交成功，等待系统审核,请知晓!');
                    $sms->send('18602396267','用户['. $userDetail['data']['username'] .':'. $userDetail['data']['phone'] .']申请了一笔['. $data['data']['limit'] .'元,使用'. $data['data']['period'] .'天]的借款,需要您尽快审核!');
                } else {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                }
            }
        }

        // 指定json数据输出
        return json($result);
    }

    /**
     * 用户取消订单
     * @return \think\response\Json
     */
    public function cancel()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $id = input('post.id');
            $token = input('post.token');

            $userInfo = parent::decode($token);
            if (!$userInfo) {
                $result['status'] = 301;
                $result['message'] = '用户资料错误';
                $result['data'] = '';
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = [
                    'uid' => $userInfo->iss,
                    'id' => $id
                ];
                $order = Loader::model('OrderService', 'service');
                $data = $order->cancel($params);
                if ($data['status'] == 200) {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                    $user = Loader::model('UserService','service');
                    $userDetail = $user->getUserInfo($userInfo->iss);
                    $sms = new \SYS\SMS();
                    $sms->send($userDetail['data']['phone'],'您的借款申请['. $data['data']['limit'] .'元,使用'. $data['data']['period'] .'天]取消成功,请知晓!');
                    $sms->send('18602396267','用户['. $userDetail['data']['username'] .':'. $userDetail['data']['phone'] .']申请的['. $data['data']['limit'] .'元,使用'. $data['data']['period'] .'天]的借款刚刚取消,请知晓!');
                } else {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }

    /**
     * 已通过Wap/Order/delay处理
     * 返回延期扣款信息
     * @return \think\response\Json
     */
    public function delay()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $id = input('post.id');
            $token = input('post.token');

            $userInfo = parent::decode($token);
            if (!$userInfo) {
                $result['status'] = 301;
                $result['message'] = '用户资料错误';
                $result['data'] = '';
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = [
                    'uid' => $userInfo->iss,
                    'id' => $id
                ];
                $order = Loader::model('OrderService', 'service');
                $data = $order->delay($params);
                if ($data['status'] == 200) {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                } else {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }

    /**
     * 用户还款申请
     * @return \think\response\Json
     */
    public function repay()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];

        if (Request::instance()->isPost()) {
            $token = input('post.token');
            $id = input('post.id');
            if ($token) {
                $userToken = parent::decode($token);
                if (!$userToken) {
                    $result['status'] = 301;
                    $result['message'] = '用户资料错误';
                    $result['data'] = '';
                }
            } else {
                $result['status'] = 300;
                $result['message'] = 'Token认证失败';
                $result['data'] = '';
            }

            $user = Loader::model('UserService','service');
            $userResult =  $user->getUserDetail($userToken->iss);
            if ($userResult['status'] !== 200) {
                $result['status'] = 303;
                $result['message'] = '用户信息获取失败';
                $result['data'] = '';
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $userInfo = $userResult['data'];
                $order = Loader::model('OrderService', 'service');
                $params = array (
                    'uid' => $userInfo['id'],
                    'id' => $id
                );

                $resultOrder = $order->getRepayInfo($params);

                if ($resultOrder['status'] >= 300) {
                    $result['status'] = $resultOrder['status'];
                    $result['message'] = $resultOrder['message'];
                } else {
                    $RepayInfo = $resultOrder['data'];
                    // TODO: 创建还款订单
                    $params = array(
                        'user_id' => $userInfo['id'],
                        'user_phone' => $userInfo['phone'],
                        'user_mail' => $userInfo['email'],
                        'created_time' => $userInfo['created_time'],
                        'acct_name' => $userInfo['bankcardDetail']['username'],
                        'id_no' => $userInfo['bankcardDetail']['idcard'],
                        'card_no' => $userInfo['bankcardDetail']['no'],
                        'no_order' => $RepayInfo['created_time'],  //
                        'dt_order' => date('YmdHis',$RepayInfo['created_time']),  //
                        'name_goods' => $RepayInfo['orderInfo']['period'].'天'.$RepayInfo['orderInfo']['limit'].'元产品还款',  //
                        'info_order' => '',      //
                        'money_order' => $RepayInfo['orderInfo']['repay_cost'],    // 银行扣款金额 0.02,
                        'valid_order' => 120,    //
                        'no_agree' => $userInfo['bankcardDetail']['agreeno'],  // 签约协议号
                        'repayment_date' => date('Y-m-d',$RepayInfo['orderInfo']['expire_time']), // 计划还款日期
                        'repayment_no' => $RepayInfo['orderInfo']['created_time'],     // 还款计划编号
                        'notify_url' => url('Api/Order/repayNotify',[],true,true)
                    );
                    $pay = new Pay();
                    $resultRepay = $pay->repayment($params);
                    if ($resultRepay['status'] != 200) {
                        $result['status'] = 302;
                        $result['message'] = $resultRepay['message'];
                        $result['params'] = $params;
                    } else {
                        $order->saveRepay($resultRepay['data']);
                        $result['status'] = 200;
                        $result['message'] = '订单扣款调用成功';
                        $result['data'] = $resultRepay['data'];
                    }
                }
            }
        }
        return json($result);
    }
    /**
     * 用户提交还款申请后的通知处理
     * @return \think\response\Json
     */
    public function repayNotify()
    {
        $result = array('ret_code'=> '1001', 'ret_msg'=>'error');
        $pay = new Pay();
        $resultRepay = $pay->repaymentNotify();

        file_put_contents(LOG_PATH."repay.txt", "用户还款返回结果:\r\n".json_encode($resultRepay)."\r\n\r\n", FILE_APPEND);
        if ($resultRepay['status'] == 200) {
            $data = $resultRepay['data'];
            $params = array(
                'no_order' => $data['no_order'],
                'dt_order' => $data['dt_order'],
                'paybill' => $data['oid_paybill'],
                'money_order' => $data['money_order'],
                'result_pay' => $data['result_pay'],       // 支付结果 SUCCESS 成功
                //'info' => $data['info_order'],       // 订单描述
                'settleDate' => $data['settle_date'],   // 清算日期
                'pay_type' => $data['pay_type'],     // 支付方式 D:认证支付(借记卡)
                'bank_code' => $data['bank_code'],   // 银行编号
                'no_agree' => $data['no_agree'],     // 签约协议号
                'id_type' => $data['id_type'],       // 证件类型 默认0:身份证
                'id_no' => $data['id_no'],           // 证件号码
                'acct_name' => $data['acct_name'],   // 银行账号姓名
                'card_no' => $data['card_no'],       // 银行卡号 622208*********0000
            );


            $order = Loader::model('OrderService', 'service');
            $orderResult = $order->repay($params);
            file_put_contents(LOG_PATH."repay.txt", "订单时间".$data['no_order'].":用户还款保存结果:\r\n".json_encode($orderResult)."\r\n\r\n", FILE_APPEND);

            if ($orderResult['status'] == 200) {
                $orderInfo = $orderResult['data'];
                $uid = $orderInfo['uid'];
                $no = $orderInfo['created_time'];
                $date = date('Y-m-d',$orderInfo['expire_time']);
                $amount = $orderInfo['repay_cost'];
                $state = 1;
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
                    file_put_contents(LOG_PATH."repay.txt", "订单时间".$data['no_order'].":还款计划修改失败:\r\n\r\n", FILE_APPEND);
                } else {
                    // 纪录日志 还款计划修改成功
                    file_put_contents(LOG_PATH."repay.txt", "订单时间".$data['no_order'].":还款计划修改成功:\r\n\r\n", FILE_APPEND);
                }
                $user = Loader::model('UserService','service');
                $userDetail = $user->getUserInfo($orderInfo['uid']);
                $sms = new \SYS\SMS();
                $sms->send($userDetail['data']['phone'],'您的借款申请['. $orderInfo['limit'] .'元,使用'. $orderInfo['period'] .'天]还款成功,请知晓!');
                $result['ret_code'] = '0000';
                $result['ret_msg'] = '交易成功';
            }
        }

        return json($result);
    }


    /**
     * 管理员审核订单
     * @return \think\response\Json
     */
    public function doAudit() {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $id = input('post.id');
            $message = input('post.message');
            $token =input('post.token');

            $userInfo = parent::decode($token);
            if (!$userInfo) {
                $result['status'] = 301;
                $result['message'] = '用户资料错误';
                $result['data'] = '';
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = [
                    'id' => $id,
                    'message' => $message
                ];
                $order = Loader::model('OrderService','service');
                $data =  $order->doAudit($params);
                if ($data['status'] == 200) {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];

                    $user = Loader::model('UserService','service');
                    $userDetail = $user->getUserInfo($data['data']['uid']);
                    $sms = new \SYS\SMS();
                    $sms->send($userDetail['data']['phone'],'您的借款申请['. $data['data']['limit'] .'元,使用'. $data['data']['period'] .'天]审核未通过,请登录米花闪借查看详情!');
                } else {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }
    /**
     * 财务确认支付订单
     * @return \think\response\Json
     */
    public function doPay()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $userToken = null;
        if (Request::instance()->isPost()) {
            $token = input('post.token');
            $id = input('post.id');
            if ($token) {
                $userToken = parent::decode($token);
                if (!$userToken) {
                    $result['status'] = 301;
                    $result['message'] = '用户资料错误';
                    $result['data'] = '';
                } else {
                    if ($userToken->role < 800) {
                        $result['status'] = 304;
                        $result['message'] = '没有权限';
                        $result['data'] = '';
                    }
                }
            } else {
                $result['status'] = 300;
                $result['message'] = 'Token认证失败';
                $result['data'] = '';
            }
            // TODO: 验证参数


            if ($result['status'] == 0) {

                $order = Loader::model('OrderService','service');
                $user = Loader::model('UserService','service');
                $findParams = array(
                    'id' => $id
                );
                $orderResult =  $order->doPay($findParams);

                if ($orderResult['status'] !== 200) {
                    $result['status'] = 302;
                    $result['message'] = '订单信息获取失败';
                } else {
                    $orderInfo = $orderResult['data'];
                    $userResult =  $user->getUserDetail($orderInfo['uid']);
                    if ($userResult['status'] !== 200) {
                        $result['status'] = 302;
                        $result['message'] = '用户信息获取失败';
                        $result['data'] = '';
                    } else {

                        $userInfo = $userResult['data'];
                        $params = array(
                            'user_id' => $userInfo['id'],                              // 用户在商户系统中的标识
                            'no_agree' => $userInfo['bankcardDetail']['agreeno'],      // 签约协议号

                            'repayment_no' => $orderInfo['created_time'],              // * 还款计划编号
                            'repayment_date' => date('Y-m-d', $orderInfo['expire_time']),    // * 计划还款日期 2010-07-06
                            'repayment_amount' => $orderInfo['repay_cost'],                // * 计划还款金额
                        );
                        $pay = new Pay();
                        $resultApply = $pay->oauthApply($params);

                        if ($resultApply['status'] != 200) {
                            $result['status'] = $resultApply['status'];
                            $result['message'] = $resultApply['message'];
                        } else {
                            $saveResult =  $order->savePay($resultApply['data']);
                            if ($saveResult['status'] != 200) {
                                $result['status'] = 303;
                                $result['message'] = '保存签约协议错误';
                                $result['data'] = $resultApply['data'];
                            } else {
                                $result['status'] = 200;
                                $result['message'] = 'success';
                                $result['data'] = $resultApply['data'];

                                $sms = new \SYS\SMS();
                                $sms->send($userInfo['phone'],'您的借款已到账,请留意查收!');
                            }

                        }
                    }

                }

            }

        }
        // 指定json数据输出
        return json($result);
    }
    /**
     * 支付订单查询
     * @return \think\response\Json
     */
    public function query()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $id = input('post.id');
            $token = input('post.token');

            $userInfo = parent::decode($token);
            if (!$userInfo) {
                $result['status'] = 301;
                $result['message'] = '用户资料错误';
                $result['data'] = '';
            }
            // TODO: 加管理员权限
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = [
                    'uid' => $userInfo->iss,
                    'id' => $id
                ];
                $order = Loader::model('OrderService', 'service');
                $orderResult = $order->getInfo($params);

                if ($orderResult['status'] != 200) {
                    $result['status'] = $orderResult['status'];
                    $result['message'] = $orderResult['message'];
                } else {
                    $orderInfo = $orderResult['data'];
                    $params = array(
                        'no_order' => $orderInfo['id'],
                        'dt_order' => date('YmdHis',$orderInfo['created_time']),
                        'oid_paybill' => $orderInfo['paybill'], // 连连支付订单好
                    );
                    $pay = new Pay();
                    $queryResult = $pay->orderquery($params);
                    if ($queryResult['status'] != 200) {
                        $result['status'] = $queryResult['status'];
                        $result['message'] = $queryResult['message'];
                    } else {
                        $result['status'] = 200;
                        $result['message'] = 'success';
                        $result['data'] = $queryResult['data'];
                    }
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }
}