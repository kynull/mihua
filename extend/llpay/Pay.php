<?php
namespace llpay;

class Pay extends Base
{
    /**
     * 签约授权＋支付
     * @param array $params
     * @return string
     */
    public function Installment(array $params)
    {
        $llpay_installment_url = 'https://wap.lianlianpay.com/installment.htm';

        $app_request = '3';     // 请求应用标识 1:Android 2:ios 3:WAP
        $platform = '';     // 平台来源标示
        $bg_color = 'ff5001';   // 背景颜色
        $font_color = '333333'; // 字体颜色
        $syschnotify_flag = 0;  // 是否主动同步通知标识 默认0:点击通知 1:主动通知
        $busi_partner = '101001'; // 商户业务类型 101001:虚拟商品销售 109001:实物商品销售
        $id_type = '0';   // 证件类型 默认 0:身份证
        $pay_type = 'D';  // 默认 D:认证支付渠道
        $risk_item = array(
            'frms_ware_category' => 2010,
            'user_info_mercht_userno' => $params['user_id'],  // 用户在商户系统中的标识
            'user_info_mercht_userlogin' => $params['user_phone'],  // 用户在商户系统中的登陆名
            'user_info_bind_phone' => $params['user_phone'],        // 绑定的手机号
            'user_info_mail' => $params['user_mail'],       // 绑定的手机号
            'user_info_dt_register' => date('YmdHis',$params['created_time']),    // 注册时间
            'user_info_mercht_usertype' => '1',        // 商户用户分类 默认 1:普通用户 2:白名单用户
            'user_info_register_ip' => '',             // 注册IP
            'user_info_full_name' => $params['acct_name'],       // 用户注册姓名
            'user_info_id_type' => '0',           // 用户注册证件类型 默认 0:身份证或企业经营证件 1:户口簿, 2:护照 3:军官证, 4:士兵证 5: 港澳居民来往内地通行证 6:台湾同胞来往内地通行证 7: 临时身份证 8: 外国人居留证 9: 警官证 X:其他证件
            'user_info_id_no' => $params['id_no'],          // 用户注册证件号码
            'user_info_identify_state' => '1', // 商户是否对用户信息进行实名认证 默认 0:否 1:是
            'user_info_identify_type' => '1'   // 实名认证方式 1:银行卡认证 2:现场认证 3:身份证远程认证 4:其它认证
        );
        $sms_param = array( // 短信参数
            'contract_type' => '米花闪借',
            'contact_way' => '023-60336820'
        );
        $repayment_no = '';     // 还款计划编号
        $repayment_plan = array(
//            'repaymentPlan' => array(
//                array(
//                    'date' => '',
//                    'amount' => 1000
//                )
//            )
        );

        // 构造要请求的参数数组，无需改动
        $parameter = array (
            "version" => trim($this->llpay_config['version']),          // *版本号
            "oid_partner" => trim($this->llpay_config['oid_partner']),  // *商户编号
            "sign_type" => trim($this->llpay_config['sign_type']),      // *签名方式

            "app_request" => $app_request,   // *
            "platform" => $platform,         // -
            "bg_color" => $bg_color,         // -
            "font_color" => $font_color,     // -
            "syschnotify_flag" => $syschnotify_flag,    // -
            "busi_partner" => $busi_partner,            // *

            "user_id" => $params['user_id'],        // * 商户用户唯一编号
            "id_type" => $id_type,                  // * 证件类型 默认 0:身份证
            "no_order" => $params['no_order'],      // * 商户唯一订单号
            "dt_order" => $params['dt_order'],      // * 商户订单时间 YYYYMMDDH24MISS 14位数字
            "name_goods" => $params['name_goods'],  // * 商品名称
            "info_order" => $params['info_order'],  // - 订单描述
            "money_order" => $params['money_order'],  // * 交易金额
            "no_agree" => $params['no_agree'],        // - 签约协议号
            "valid_order" => $params['valid_order'],  // - 订单有效时间
            "id_no" => $params['id_no'],              // * 证件号码
            "acct_name" => $params['acct_name'],      // * 银行账号姓名
            "card_no" => $params['card_no'],          // - 银行卡号

            "pay_type" => $pay_type,                  // * 支付方式
            "risk_item" => json_encode($risk_item),   // * 风险控制参数
            "notify_url" => $params['notify_url'],    // 服务器异步通知地址
            "url_return" => $params['return_url']     // 支付结束回显url
        );

        if ($repayment_no != '' && count($repayment_plan) > 0 && count($sms_param) > 0) {
            $parameter['repayment_no'] = $repayment_no;
            $parameter['repayment_plan'] = json_encode($repayment_plan);
            $parameter['sms_param'] = json_encode($sms_param);
        }

        $form_html = $this->Form_Post($llpay_installment_url, $parameter);
        return $form_html;
    }
    public function InstallmentNotify()
    {
        $result = array('status' => 0, 'message' => '未知错误', 'data' => array(), 'timestamp' => time());
        $llpayNotify = new Notify($this->llpay_config);
        $llpayNotify->verifyNotify();

        if($llpayNotify->result) {//验证成功
            //获取连连支付的通知返回参数，可参考技术文档中服务器异步通知参数列表
            $params = array(
                'no_order' => $llpayNotify->notifyResp['no_order'],        // 商户订单号
                'oid_paybill' => $llpayNotify->notifyResp['oid_paybill'],  // 连连支付单号
                'result_pay' => $llpayNotify->notifyResp['result_pay'],    // 支付结果，SUCCESS：为支付成功
                'money_order' => $llpayNotify->notifyResp['money_order'],  // 支付金额
                'oid_partner' => $llpayNotify->notifyResp['oid_partner'],
                'sign_type' => $llpayNotify->notifyResp['sign_type'],
                'dt_order' => $llpayNotify->notifyResp['dt_order'],
                'settle_date' => $llpayNotify->notifyResp['settle_date'],
                'info_order' => $llpayNotify->notifyResp['info_order'],
                'pay_type' => $llpayNotify->notifyResp['pay_type'],
                'bank_code' => $llpayNotify->notifyResp['bank_code'],
                'no_agree' => $llpayNotify->notifyResp['no_agree'],
                'id_type' => $llpayNotify->notifyResp['id_type'],
                'id_no' => $llpayNotify->notifyResp['id_no'],
                'acct_name' => $llpayNotify->notifyResp['acct_name']
            );
            $result['status'] = 200;
            $result['message'] = "success";
            $result['data'] = $params;
            file_put_contents(LOG_PATH."log.txt", "异步通知 验证成功\n", FILE_APPEND);

        } else {
            // 验证失败
            file_put_contents(LOG_PATH."log.txt", "异步通知 验证失败\n", FILE_APPEND);

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            $result['status'] = 301;
            $result['message'] = "验签失败";
        }
        return $result;
    }
    public function InstallmentReturn()
    {
        $result = array('status' => 0, 'message' => '未知错误', 'data' => array(), 'timestamp' => time());
        // 计算得出通知验证结果
        $llpayNotify = new Notify($this->llpay_config);
        $verify_result = $llpayNotify->verifyReturn();

        if($verify_result) {// 验证成功
            // 请在这里加上商户的业务逻辑程序代码

            // ——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            // 获取连连支付的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
            $json = new JSON;
            $res_data = $_POST["res_data"];
            $obj = $json->decode($res_data);

            $oid_partner = $obj->{'oid_partner'}; // 商户编号
            $no_order = $obj->{'no_order'};       // 商户订单号
            $dt_order = $obj->{'dt_order'};       // 商户订单时间
            $oid_paybill = $obj->{'oid_paybill'}; // 连连支付支付单号
            $money_order = $obj->{'money_order'}; // 交易金额
            $result_pay = $obj->{'result_pay'};   // 支付结果

            $params = array(
                'oid_partner' => $oid_partner,
                'no_order' => $no_order,
                'dt_order' => $dt_order,
                'oid_paybill' => $oid_paybill,
                'money_order' => $money_order,
                'result_pay' => $result_pay,
            );
            if ($result_pay == 'SUCCESS') {
                // 判断该笔订单是否在商户网站中已经做过处理
                // 如果没有做过处理，根据订单号（no_order）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                // 如果有做过处理，不执行商户的业务程序

                $result['status'] = 200;
                $result['message'] = "支付成功";
                $result['data'] = $params;
            } else {
                echo "支付结果: " . $result_pay .'<br/>\r\n';

                $result['status'] = 302;
                $result['message'] = "支付失败";
                $result['data'] = $params;
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        } else {
            //验证失败
            //如要调试，请看llpay_notify.php页面的verifyReturn函数
            $result['status'] = 301;
            $result['message'] = "验证失败";
        }
        return $result;
    }

    /**
     * 签约授权
     * @param array $params
     * @return string
     */
    public function Apply(array $params)
    {
        $llpay_sign_apply_url = 'https://wap.lianlianpay.com/signApply.htm';
        $risk_item = array(
            'frms_ware_category' => 2010,
            'user_info_mercht_userno' => $params['user_id'],  // 用户在商户系统中的标识
            'user_info_mercht_userlogin' => $params['user_phone'],  // 用户在商户系统中的登陆名
            'user_info_bind_phone' => $params['user_phone'],        // 绑定的手机号
            'user_info_mail' => $params['user_mail'],       // 绑定的手机号
            'user_info_dt_register' => date('YmdHis',$params['created_time']),    // 注册时间
            'user_info_mercht_usertype' => '1',        // 商户用户分类 默认 1:普通用户 2:白名单用户
            'user_info_register_ip' => '',             // 注册IP
            'user_info_full_name' => $params['acct_name'],       // 用户注册姓名
            'user_info_id_type' => '0',           // 用户注册证件类型 默认 0:身份证或企业经营证件 1:户口簿, 2:护照 3:军官证, 4:士兵证 5: 港澳居民来往内地通行证 6:台湾同胞来往内地通行证 7: 临时身份证 8: 外国人居留证 9: 警官证 X:其他证件
            'user_info_id_no' => $params['id_no'],          // 用户注册证件号码
            'user_info_identify_state' => '1', // 商户是否对用户信息进行实名认证 默认 0:否 1:是
            'user_info_identify_type' => '1'   // 实名认证方式 1:银行卡认证 2:现场认证 3:身份证远程认证 4:其它认证
        );
        $app_request = '3';
        $id_type = '0';  // 证件类型 默认0:身份证
        $pay_type = 'I';

        $sms_param = array( // 短信参数
            'contract_type' => '米花闪借',
            'contact_way' => '023-60336820'
        );
        $repayment_no = '';     // 还款计划编号
        $repayment_plan = array(
//            'repaymentPlan' => array(
//                array(
//                    'date' => '',
//                    'amount' => 1000
//                )
//            )
        );

        // 构造要请求的参数数组，无需改动
        $parameter = array (
            "version" => trim('1.1'),
            "oid_partner" => trim($this->llpay_config['oid_partner']),
            "sign_type" => trim($this->llpay_config['sign_type']),

            "app_request" => $app_request,
            "user_id" => $params['user_id'],
            "id_type" => $id_type,
            "id_no" => $params['id_no'],
            "acct_name" => $params['acct_name'],
            "card_no" => $params['card_no'],
            'pay_type' => $pay_type,
            "risk_item" => json_encode($risk_item),
            "url_return" => $params['return_url']
        );
        if ($repayment_no != '' && count($repayment_plan) > 0 && count($sms_param) > 0) {
            $parameter['repayment_no'] = $repayment_no;
            $parameter['repayment_plan'] = json_encode($repayment_plan);
            $parameter['sms_param'] = json_encode($sms_param);
        }
        file_put_contents(LOG_PATH."bind.txt", "绑定银行卡:\r\n".json_encode($parameter)."\r\n\r\n", FILE_APPEND);

        $form_html = $this->Form_Post($llpay_sign_apply_url, $parameter);
        return $form_html;
    }
    /**
     * 签约授权回调数据处理
     * @return array
     */
    public function getApplyReturn() {
        $result = array('status' => 0, 'message' => '未知错误', 'data' => array(), 'timestamp' => time());

        // 计算得出通知验证结果
        $llpayNotify = new Notify($this->llpay_config);
        $verifySuccess = $llpayNotify->verifyApplyReturn();

        //echo '签名验证:'. ($verifySuccess ? '成cc功' : '失败') .'<br />';
        $verifySuccess = true;

        if($verifySuccess) { // 验证成功
            // 请在这里加上商户的业务逻辑程序代码

            // ——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            // 获取连连支付的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
            $returnStatus = $_GET["status"];
            $ReturnData = $_GET["result"];
            file_put_contents(LOG_PATH."bind.txt", "绑定银行卡回调:\r\n".$returnStatus."=". $ReturnData ."\r\n\r\n", FILE_APPEND);
            if($returnStatus == '0000') {
                $json = new JSON;
                $sign_type = $json->decode($ReturnData)-> {'sign_type'};        // 签名方式
                $sign = $json->decode($ReturnData)-> {'sign'};                  // 签名
                $oid_partner =  $json->decode($ReturnData)-> {'oid_partner'};   // 商户编号
                $user_id =  $json->decode($ReturnData)-> {'user_id'};           // 商户用户唯一编号
                $agreeno =  $json->decode($ReturnData)-> {'agreeno'};           // 签约协议号
                // 判断该笔订单是否在商户网站中已经做过处理
                // 如果没有做过处理，根据订单号（no_order）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                // 如果有做过处理，不执行商户的业务程序

                $result['status'] = 200;
                $result['message'] = '代码:' . $returnStatus . '描述:' . $ReturnData;
                $result['data'] = array (
                    'sign_type' => $sign_type,
                    'sign' => $sign,
                    'oid_partner' => $oid_partner,
                    'user_id' => $user_id,
                    'agreeno' => $agreeno,
                );
            } else {
                $result['status'] = 302;
                $result['message'] = '代码:' . $returnStatus . '描述:' . $ReturnData;
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        } else {
            //验证失败
            //如要调试，请看llpay_notify.php页面的verifyReturn函数
            $result['status'] = 301;
            $result['message'] = "验证失败";
        }

        return $result;
    }

    // --    下面通过API的请求方式调用
    /**
     * 授权申请,给已经签约过的用户进行单独授权
     * @param array $params
     * @return array
     */
    public function oauthApply(array $params)
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $llpay_oauth_apply_url = 'https://repaymentapi.lianlianpay.com/agreenoauthapply.htm';

        $platform = '';     // 平台来源标示
        $pay_type = 'D';  // 默认 D:认证支付渠道
        $repayment_plan = array(
            'repaymentPlan' => array(
                array(
                    'date' => $params['repayment_date'],
                    'amount' => $params['repayment_amount']
                )
            )
        );
        $sms_param = array( // 短信参数
            'contract_type' => '米花闪借',
            'contact_way' => '023-60336820'
        );

        // 构造要请求的参数数组，无需改动
        $parameter = array (
            'platform' => $platform,  // -
            'api_version' => trim($this->llpay_config['version']),    // *版本号 ?1.0
            'oid_partner' => trim($this->llpay_config['oid_partner']),// *商户编号
            'sign_type' => trim($this->llpay_config['sign_type']),    // *签名方式

            'user_id' => $params['user_id'],             // * 商户用户唯一编号
            'pay_type' => $pay_type,                     // * 支付方式
            'repayment_plan' => json_encode($repayment_plan),   // * 还款计划
            'repayment_no' => $params['repayment_no'],   // * 还款计划编号
            'sms_param' => json_encode($sms_param),      // * 短信参数
            'no_agree' => $params['no_agree']            // * 签约协议号
        );

        $html_text = $this->Https_Post($llpay_oauth_apply_url, $parameter);
        $obj_result = json_decode($html_text);

        if ($obj_result->ret_code != '0000') {
            $result['status'] = 301;
            $result['message'] = $obj_result->ret_code . ':'. $obj_result->ret_msg;
        } else {
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = array(
                'ret_code' => $obj_result->ret_code,
                'ret_msg' => $obj_result->ret_msg,
                'oid_partner' => $obj_result->oid_partner,
                'correlationID' => $obj_result->correlationID,
                'no_agree' => $obj_result->no_agree,
                'repayment_no' => $obj_result->repayment_no,
            );
        }
        return $result;
    }

    /**
     * 银行卡还款扣款接口
     * @param array $params
     * @return array
     */
    public function repayment(array $params)
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $llpay_repayment_url = 'https://repaymentapi.lianlianpay.com/bankcardrepayment.htm';

        $platform = '';     // 平台来源标示
        $busi_partner = '101001'; // 商户业务类型 101001:虚拟商品销售 109001:实物商品销售
        $pay_type = 'D';
        $risk_item = array(
            'frms_ware_category' => 2010,
            'user_info_mercht_userno' => $params['user_id'],  // 用户在商户系统中的标识
            'user_info_mercht_userlogin' => $params['user_phone'],  // 用户在商户系统中的登陆名
            'user_info_bind_phone' => $params['user_phone'],        // 绑定的手机号
            'user_info_mail' => $params['user_mail'],       // 绑定的邮件
            'user_info_dt_register' => date('YmdHis',$params['created_time']),    // 注册时间
            'user_info_mercht_usertype' => '1',        // 商户用户分类 默认 1:普通用户 2:白名单用户
            'user_info_register_ip' => '',             // 注册IP
            'user_info_full_name' => $params['acct_name'],       // 用户注册姓名
            'user_info_id_type' => '0',           // 用户注册证件类型 默认 0:身份证或企业经营证件 1:户口簿, 2:护照 3:军官证, 4:士兵证 5: 港澳居民来往内地通行证 6:台湾同胞来往内地通行证 7: 临时身份证 8: 外国人居留证 9: 警官证 X:其他证件
            'user_info_id_no' => $params['id_no'],          // 用户注册证件号码
            'user_info_identify_state' => '1', // 商户是否对用户信息进行实名认证 默认 0:否 1:是
            'user_info_identify_type' => '1'   // 实名认证方式 1:银行卡认证 2:现场认证 3:身份证远程认证 4:其它认证
        );

        // 构造要请求的参数数组，无需改动
        $parameter = array (
            'platform' => $platform,  // -
            'api_version' => trim($this->llpay_config['version']),    // *版本号 ?1.0
            'oid_partner' => trim($this->llpay_config['oid_partner']),// *商户编号
            'sign_type' => trim($this->llpay_config['sign_type']),    // *签名方式
            'busi_partner' => $busi_partner,    // * 商户业务类型

            'user_id' => $params['user_id'],    // * 商户用户唯一编号
            'no_order' => $params['no_order'],  // * 商户唯一订单号
            'dt_order' => $params['dt_order'],  // * 商户订单时间 YYYYMMDDH24MISS 14位数字
            'name_goods' => $params['name_goods'],    // * 商品名称
            'info_order' => $params['info_order'],    // - 订单描述
            'money_order' => $params['money_order'],  // * 还款金额
            'valid_order' => $params['valid_order'],  // - 订单有效时间
            'risk_item' => json_encode($risk_item),   // * 风险控制参数
            'schedule_repayment_date' => $params['repayment_date'], // * 计划还款日期 2010-07-06
            'repayment_no' => $params['repayment_no'],    // * 还款计划编号
            'pay_type' => $pay_type,                      // * 支付方式
            'no_agree' => $params['no_agree'],            // * 签约协议号
            'notify_url' => $params['notify_url'],        // * 服务器异步通知地址
        );

        $html_text = $this->Https_Post($llpay_repayment_url, $parameter);

        $obj_result = json_decode($html_text);
        if ($obj_result->ret_code !== '0000') {
            $result['status'] = 301;
            $result['message'] = $obj_result->ret_code . ':'. $obj_result->ret_msg;
        } else {
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = array(
                'ret_code' => $obj_result->ret_code,        // 交易结果代码
                'ret_msg' => $obj_result->ret_msg,          // 交易结果描述
                'sign_type' => $obj_result->sign_type,      // 签名方式
                'sign' => $obj_result->sign,                // 签名
                'oid_partner' => $obj_result->oid_partner,  // 商户编号
                'no_order' => $obj_result->no_order,        // 商户唯一订单号
                'dt_order' => $obj_result->dt_order,        // 商户订单时间
                'money_order' => $obj_result->money_order,  // 交易金额
                'oid_paybill' => $obj_result->oid_paybill,  // 连连支付支付单号 订单创建成功返回 String(18)
                //'info_order' => $obj_result->info_order,    // 订单描述
                'settle_date' => $obj_result->settle_date   // 清算日期
            );
        }
        return $result;
    }
    /**
     * 银行还款扣款异步通知数据
     * @return array
     */
    public function repaymentNotify() {
        $result = array(
            'status' => 0,
            'message' => '未知错误',
            'data' => array(),
            'timestamp' => time()
        );

        // 计算得出通知验证结果
        $llpayNotify = new Notify($this->llpay_config);
        $verifySuccess = $llpayNotify->verifyRepaymentNotify();
        if ($verifySuccess) { // 验证成功
            $json = new JSON;
            $str = file_get_contents("php://input");
            $val = $json->decode($str);
            $params = array(
                //'oid_partner' => getJsonVal($val,'oid_partner'),
                //'sign_type' => getJsonVal($val,'sign_type'),
                //'sign' => getJsonVal($val,'sign'),
                'dt_order' => getJsonVal($val,'dt_order'),
                'no_order' => getJsonVal($val,'no_order'),
                'oid_paybill' => getJsonVal($val,'oid_paybill'),
                'money_order' => getJsonVal($val,'money_order'),
                'result_pay' => getJsonVal($val,'result_pay'),
                'settle_date' => getJsonVal($val,'settle_date'),
                'info_order' => getJsonVal($val,'info_order'),
                'pay_type' => getJsonVal($val,'pay_type'),
                'bank_code' => getJsonVal($val,'bank_code'),
                'no_agree' => getJsonVal($val,'no_agree'),
                'id_type' => getJsonVal($val,'id_type'),
                'id_no' => getJsonVal($val,'id_no'),
                'acct_name' => getJsonVal($val,'acct_name'),
                'card_no' => getJsonVal($val,'card_no')
            );

            $result['status'] = 200;
            $result['message'] = "数据获取成功";
            $result['data'] = $params;
        } else {
            //验证失败
            //如要调试，请看llpay_notify.php页面的verifyReturn函数
            $result['status'] = 301;
            $result['message'] = "签名验证失败";
        }

        return $result;
    }

    /**
     * 还款计划变更
     * @param array $params
     * @return 更改结果Json
     */
    public function changeRepay(array $params)
    {
        $llpay_change_repay_url = 'https://repaymentapi.lianlianpay.com/repaymentplanchange.htm';

        $repayment_plan = array(
            'repaymentPlan' => array(
                array(
                    'date' => $params['repayment_date'],
                    'amount' => $params['repayment_amount']
                )
            )
        );
        $sms_param = array( // 短信参数
            'contract_type' => '米花闪借',
            'contact_way' => '023-60336820'
        );

        // 构造要请求的参数数组，无需改动
        $parameter = array (
            'oid_partner' => trim($this->llpay_config['oid_partner']),// *商户编号
            'sign_type' => trim($this->llpay_config['sign_type']),    // *签名方式

            'user_id' => $params['user_id'],                   // * 商户用户唯一编号
            'repayment_plan' => json_encode($repayment_plan),  // * 还款计划

            'repayment_no' => $params['repayment_no'],         // * 还款计划编号
            'sms_param' => json_encode($sms_param)             // * 短信参数
        );

        if (array_key_exists('state',$params)) {
            $parameter['repayment_state'] = $params['state']; // - 还款状态 1:终止还款
        }

        $html_text = $this->Https_Post($llpay_change_repay_url, $parameter);
        $obj_result = json_decode($html_text);
        if ($obj_result->ret_code !== '0000') {
            $result['status'] = 301;
            $result['message'] = $obj_result->ret_code . ':'. $obj_result->ret_msg;
        } else {
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $obj_result;
        }
        return $result;
    }
    /**
     * 银行卡BIN查询
     * @param array $params
     * @return 连连支付处理结果
     */
    public function bin(array $params)
    {
        $llpay_card_bin_url = 'https://queryapi.lianlianpay.com/bankcardbin.htm';

        // 构造要请求的参数数组，无需改动
        $parameter = array (
            'oid_partner' => trim($this->llpay_config['oid_partner']),// *商户编号
            'sign_type' => trim($this->llpay_config['sign_type']),    // *签名方式

            'card_no' => $params['card_no']                   // * 银行卡号
        );


        $html_text = $this->Http_Post($llpay_card_bin_url, $parameter);
        $obj_result = json_decode($html_text);
        if ($obj_result->ret_code !== '0000') {
            $result['status'] = 301;
            $result['message'] = $obj_result->ret_code . ':'. $obj_result->ret_msg;
        } else {
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $obj_result;
        }
        return $result;
    }

    /**
     * 用户签约信息查询
     * @param array $params
     * @return 连连支付处理结果
     */
    public function bindlist(array $params)
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $llpay_bind_list_url = 'https://queryapi.lianlianpay.com/bankcardbindlist.htm';

        $platform = '';     // 平台来源标示
        $pay_type = 'D';  // 默认 D:认证支付渠道

        // 构造要请求的参数数组，无需改动
        $parameter = array (
            'oid_partner' => trim($this->llpay_config['oid_partner']),// * 商户编号
            'sign_type' => trim($this->llpay_config['sign_type']),    // * 签名方式

            'user_id' => $params['user_id'],    // * 商户用户唯一编号
            'platform' => $platform,            // - 平台来源标示
            'pay_type' => $pay_type,            // * 支付方式 D:认证支付
            'offset' => $params['offset'],      // * 查询偏移量 0:条开始查
        );
        if (array_key_exists('no_agree', $params)) {
            $parameter['no_agree'] = $params['no_agree'];  // -签约协议号
        }
        if (array_key_exists('card_no', $params)) {
            $parameter['card_no'] = $params['card_no'];  // -签约银行卡号
        }

        $html_text = $this->Http_Post($llpay_bind_list_url, $parameter);
        $obj_result = json_decode($html_text);
        if ($obj_result->ret_code !== '0000') {
            $result['status'] = 301;
            $result['message'] = $obj_result->ret_code . ':'. $obj_result->ret_msg;
        } else {
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $obj_result;
        }
        return $result;
    }
    /**
     * 商户支付结果查询
     * @param array $params
     * @return 连连支付处理结果
     */
    public function orderquery(array $params)
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $llpay_bind_list_url = 'https://queryapi.lianlianpay.com/orderquery.htm.htm';

        $query_version = '1.1';     // 查询版本号  默认1.0:老版本 1.1:新增memo字段、银行名称bank_name字段

        // 构造要请求的参数数组，无需改动
        $parameter = array (
            'oid_partner' => trim($this->llpay_config['oid_partner']),// *商户编号
            'sign_type' => trim($this->llpay_config['sign_type']),    // *签名方式

            'no_order' => $params['no_order'],        // * 商户唯一订单号
            'dt_order' => $params['dt_order'],        // * 商户订单时间
            'oid_paybill' => $params['oid_paybill'],  // - 连连支付支付单号
            'query_version' => $query_version,        // - 查询版本号
        );

        $html_text = $this->Https_Post($llpay_bind_list_url, $parameter);
        $obj_result = json_decode($html_text);
        if ($obj_result->ret_code !== '0000') {
            $result['status'] = 301;
            $result['message'] = $obj_result->ret_code . ':'. $obj_result->ret_msg;
        } else {
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $obj_result;
        }
        return $result;
    }

    /**
     * 银行卡解约
     * @param array $params
     * @return 连连支付处理结果
     */
    public function unbind(array $params)
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $llpay_unbind_url = 'https://queryapi.lianlianpay.com/bankcardunbind.htm';

        $platform = '';     // 平台来源标示
        $pay_type = 'D';        // 支付方式 D:认证支付

        // 构造要请求的参数数组，无需改动
        $parameter = array (
            'oid_partner' => trim($this->llpay_config['oid_partner']),// *商户编号
            'sign_type' => trim($this->llpay_config['sign_type']),    // *签名方式
            'pay_type' => $pay_type, // * 支付方式
            'platform' => $platform, // * 平台来源标示

            'no_agree' => $params['no_agree']  // * 银行卡签约的唯一编号
        );
        $html_text = $this->Http_Post($llpay_unbind_url, $parameter);
        $obj_result = json_decode($html_text);
        if ($obj_result->ret_code !== '0000') {
            $result['status'] = 301;
            $result['message'] = $obj_result->ret_code . ':'. $obj_result->ret_msg;
        } else {
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $obj_result;
        }
        return $result;
    }

}