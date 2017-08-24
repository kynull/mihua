<?php

namespace app\api\controller;


use risk\model\AppForm;
use risk\model\base\Device;
use risk\model\base\ContactInfo;
use risk\model\base\PhotoInfo;

use think\Loader;
use think\Request;

use risk\DoApp;
class Risk extends Base
{
    public function do() {
        $userInfo = null;
        $user = Loader::model('UserService','service');
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $token = input('post.token');
            $params = array(
                'id_coop' => '78fb6q93d14cua0bbsa9aa49ebe601y3',    // * 商户编码   input("id_coop")
                'id_custc' => '', //input("post.id_custc"),          //
                'no_bus' => '', //input("post.no_bus"),              // 商户申请单号

                'code_bus' => '',          // 商户类型 0: 零售商 1: 合作商
                'no_busb' => input('post.no_busb'),            //

                'name_custc' => input("post.name"),             // * 姓名
                'id_type' => input("post.id_type"),             // 证件类型
                'id_card' => input("post.id_card"),             // * 证件号码
                'mobile' => input("post.mobile"),               // * 手机号

                'social_identity' => input("post.social_identity"), // 社会身份

                'email' => input("post.email"),                     // - 常用邮箱
                'marital_status' => input("post.marital_status"),   // - 婚姻状况
                'month_income' => '', //input('post.month_income'),       // -

                // 住址信息
                'abode_province_code' => input("post.abode_province_code"),  // * 现居地址（省）
                'abode_city_code' => input("post.abode_city_code"),  // * 现居地址（市）
                'abode_zone_code' => input("post.abode_zone_code"),  // * 现居地址（区/县）
                'abode_add' => input("post.abode_add"),              // * 现居地址（详细地址）
                'home_tel' => '', //input("post.home_tel"),                //

                // 单位信息
                'emp_province_code' => input("post.emp_province_code"), // 工作地址（省）
                'emp_city_code' => input("post.emp_city_code"),         // 工作地址（市）
                'emp_zone_code' => input("post.emp_zone_code"),         // 工作地址（区/县）
                'emp_add' => input("post.emp_add"),                     // 工作地址（详细地址）
                'emp_name' => input("post.emp_name"),                   // 工作单位名称
                'emp_type' => '', //input('post.emp_type'),       //
                'emp_dept' => '', //input('post.emp_dept'),       // 工作部门
                'emp_title' => '', //input('post.emp_title'),     //
                'emp_tel' => input('post.emp_tel'),         // 工作单位联系电话
                'emp_tel_ext' => '', //input('post.emp_tel_ext'), //

                // 学校信息
                'school_name' => '', //input('post.school_name'),         // 学校名称
                'education_level' => '', //input('post.education_level'), // 教育程度(学历层次)
                'education' => '', //input('post.education'),             // 教育程度(学历类)
                'graduate_date' => '', //input('post.graduate_date'),     // 毕业时间

                // 银行卡信息
                'bank_card_no' => input("post.bank_card_no"),             // 银行卡号
                'bank_code' => input("post.bank_code"),                   // 银行编码
                'bank_province_code' => input("post.bank_province_code"), // 银行地址编码（省）
                'bank_city_code' => input("post.bank_city_code"),         // 银行地址编码（市）

                // 产品信息
                'prod_type' => '', //input("post.prod_type"),    // 产品类型
                'prod_code' => '', //input("post.prod_code"),    // 产品代码
                'app_limit' => input("post.app_limit"),    // 申请额度
                'app_term' => input("post.app_term"),     // 申请期数
                'prod_child_code' => '', //input("post.prod_child_code"), // 子产品编码
                'prod_sub_code' => '', //input("post.prod_sub_code"),   // 这个是富贵日志占用
                'loan_purpose' => input("post.loan_purpose"),    // 贷款用途 LP01
                'apply_time' => input("post.apply_time"),      // 申请时间
                'repay_type' => input("post.repay_type"),      //
                'month_repayment' => input("post.month_repayment"), // 每期应还款
                'annual_rate' => '', //input("post.annual_rate"),     // 年利率
                'down_payment' => '', //input("post.down_payment"),    // 首付款
                'insurance_rate' => '', //input("post.insurance_rate"),  // 保险费率
                'insurance_amt' => '', //input("post.insurance_amt"),   //
                'riskrank_bus' => input("post.riskrank_bus"),    // 商户提示风险等级
                'channel_info' => '', //input('post.channel_info'),

                'is_create_sales' => '', //input("post.is_create_sales"), //

                // 联系人
                'lineal_relation' => input("post.lineal_relation"),
                'lineal_name' => input("post.lineal_name"),
                'lineal_mobile' => input("post.lineal_mobile"),
                'lineal_address' => input("post.lineal_address"),
                'other_relation' => input("post.other_relation"),
                'other_name' => input("post.other_name"),
                'other_mobile' => input("post.other_mobile"),
                'other_address' => input("post.other_address"),
            );
            if ($token) {
                $userInfo = parent::decode($token);
                if (!$userInfo) {
                    $result['status'] = 301;
                    $result['message'] = '用户资料错误';
                    $result['data'] = '';
                }
            } else {
                $result['status'] = 300;
                $result['message'] = 'Token认证失败';
                $result['data'] = '';
            }


            $findParam = array(
                'uid' => input('post.user_id'),
            );
            $userResult =  $user->getIDCard($findParam);
            if ($userResult['status'] != 200) {
                $result['status'] = 302;
                $result['message'] = '用户资料获取失败';
                $result['data'] = '';
            }
            $userIDCardData = $userResult['data'];
            $photos = array();
            $uploadPath = ROOT_PATH.'/public/uploads/';
            if ($userIDCardData['front']) {
                $uploadID = $this->upload($userIDCardData['front'], $uploadPath . $userIDCardData['front']);
                if ($uploadID != '0') {
                    array_push($photos, array(
                        'photo_type' => 'P02',
                        'photo_id' => $uploadID,
                        'photo_desc' => '身份证正面',
                    ));
                }
            }
            if ($userIDCardData['back']) {
                $uploadID = $this->upload($userIDCardData['back'], $uploadPath . $userIDCardData['back']);
                if ($uploadID != '0') {
                    array_push($photos, array(
                        'photo_type' => 'P03',
                        'photo_id' => $uploadID,
                        'photo_desc' => '身份证反面',
                    ));
                }
            }
            if ($userIDCardData['face']) {
                $uploadID = $this->upload($userIDCardData['face'], $uploadPath . $userIDCardData['face']);
                if ($uploadID != '0') {
                    array_push($photos, array(
                        'photo_type' => 'P01',
                        'photo_id' => $uploadID,
                        'photo_desc' => '申请人照片',
                    ));
                }
            }
            $params['photos'] = $photos;

            // TODO: 验证参数

            if ($result['status'] == 0) {

                $sendData = $this->sendData($params);
                if ($sendData['status'] == 200) {
                    $order = Loader::model('OrderService','service');
                    $vo = $sendData['data'];
                    $saveData = array(
                        'uid' =>  $userInfo-> iss,
                        'noBus' => $vo->noBus,
                        'noBusb' => $vo->noBusb,
                        'advice' => $vo->advice,
                        'creditLimit' => $vo->creditLimit,
                        'creditTerm' => $vo->creditTerm,
                        'reasonCode' => $vo->reasonCode,
                        'reason' => json_encode($vo->reason),
                        'amtDownpay' => $vo->amtDownpay,
                        'amtMonthrepay' => $vo->amtMonthrepay,
                        'dataProd' => json_encode($vo->dataProd),
                    );
                    $data =  $order->saveAudit($saveData);
                    $data['status'] = 200;
                    if ($data['status'] == 200) {
                        $result['status'] = 200;
                        $result['message'] = 'success';
                        $result['data'] = $sendData['data'];
                    } else {
                        $result['status'] = $data['status'];
                        $result['message'] = $data['message'];
                    }
                } else {
                    $result['status'] = 400;
                    $result['message'] = '风控审核失败:'. $sendData['message'];
                    $result['params'] = $params;
                }
            }

        }
        // 指定json数据输出
        return json($result);
    }
    private function sendData (array $params) {
        // 处理结果
        $result = array ('status' => -1, 'message'=> '未知错误', 'data' => '');

        $appForm = new AppForm(); // 格式化数据

        $appForm->id_coop = $params['id_coop'];
        $appForm->id_custc = array_key_exists('id_custc', $params) ? $params['id_custc'] : '';
        $appForm->no_bus = array_key_exists('no_bus', $params) ? $params['no_bus'] : '';
        $appForm->code_bus = array_key_exists('code_bus', $params) ? $params['code_bus'] : '';
        $appForm->no_busb = $params['no_busb'];

        $appForm->name_custc = $params['name_custc'];
        $appForm->id_type = $params['id_type'];
        $appForm->id_card = $params['id_card'];
        $appForm->mobile = $params['mobile'];

        $appForm->social_identity = $params['social_identity'];

        $appForm->email = $params['email'];
        $appForm->marital_status = $params['marital_status'];
        $appForm->month_income = array_key_exists('month_income', $params) ? $params['month_income'] : '';

        // 住址信息
        $appForm->abode_province_code = ''; //$params['abode_province_code'];
        $appForm->abode_city_code = ''; //$params['abode_city_code'];
        $appForm->abode_zone_code = ''; //$params['abode_zone_code'];
        $appForm->abode_add = $params['abode_add'];
        $appForm->home_tel = array_key_exists('home_tel', $params) ? $params['home_tel'] : '';

        // 单位信息
        $appForm->emp_province_code = ''; //$params['emp_province_code'];
        $appForm->emp_city_code = ''; //$params['emp_city_code'];
        $appForm->emp_zone_code = ''; //$params['emp_zone_code'];
        $appForm->emp_add = $params['emp_add'];
        $appForm->emp_name = $params['emp_name'];
        $appForm->emp_type = array_key_exists('emp_type', $params) ? $params['emp_type'] : '';
        $appForm->emp_dept = array_key_exists('emp_dept', $params) ? $params['emp_dept'] : '';
        $appForm->emp_title = array_key_exists('emp_title', $params) ? $params['emp_title'] : '';
        $appForm->emp_tel = $params['emp_tel'];
        $appForm->emp_tel_ext = array_key_exists('emp_tel_ext', $params) ? $params['emp_tel_ext'] : '';

        // 学校信息
        // 学校名称
        $appForm->school_name = array_key_exists('school_name', $params) ? $params['school_name'] : '';
        // 教育程度(学历层次)
        $appForm->education_level = array_key_exists('education_level', $params) ? $params['education_level'] : '';
        // 教育程度(学历类)
        $appForm->education = array_key_exists('education', $params) ? $params['education'] : '';
        // 毕业时间
        $appForm->graduate_date = array_key_exists('graduate_date', $params) ? $params['graduate_date'] : '';

        // 银行卡信息
        $appForm->bank_card_no = $params['bank_card_no'];       // 银行卡号
        $appForm->bank_code = ''; //$params['bank_code'];          // 银行编码
        $appForm->bank_province_code = ''; //$params['bank_province_code']; // 银行地址编码（省）
        $appForm->bank_city_code = ''; //$params['bank_city_code'];     // 银行地址编码（市）

        // 产品信息
        // 产品类型
        $appForm->prod_type = array_key_exists('prod_type', $params) ? $params['prod_type'] : '';
        // 产品代码
        $appForm->prod_code = array_key_exists('prod_code', $params) ? $params['prod_code'] : '';
        $appForm->app_limit = $params['app_limit'];    // 申请额度
        $appForm->app_term = $params['app_term'];     // 申请期数
        // 子产品编码
        $appForm->prod_child_code = array_key_exists('prod_child_code', $params) ? $params['prod_child_code'] : '';
        // 这个是富贵日志占用
        $appForm->prod_sub_code = array_key_exists('prod_sub_code', $params) ? $params['prod_sub_code'] : '';
        // 贷款用途
        $appForm->loan_purpose = array_key_exists('loan_purpose', $params) ? $params['loan_purpose'] : '';
        $appForm->apply_time = $params['apply_time'];      // 申请时间
        $appForm->repay_type = $params['repay_type'];      //
        $appForm->month_repayment = $params['month_repayment']; // 每期应还款
        // 年利率
        $appForm->annual_rate = array_key_exists('annual_rate', $params) ? $params['annual_rate'] : '';
        // 首付款
        $appForm->down_payment = array_key_exists('down_payment', $params) ? $params['down_payment'] : '';
        // 保险费率
        $appForm->insurance_rate = array_key_exists('insurance_rate', $params) ? $params['insurance_rate'] : '';
        //
        $appForm->insurance_amt = array_key_exists('insurance_amt', $params) ? $params['insurance_amt'] : '';
        // 商户提示风险等级
        $appForm->riskrank_bus = array_key_exists('riskrank_bus', $params) ? $params['riskrank_bus'] : '';
        //
        $appForm->channel_info = array_key_exists('channel_info', $params) ? $params['channel_info'] : '';
        //
        $appForm->is_create_sales = array_key_exists('is_create_sales', $params) ? $params['is_create_sales'] : '';

        // 联系人
        $contacts = array();
        $lineal_relation = $params['lineal_relation'];
        $lineal_name = $params['lineal_name'];
        $lineal_mobile = $params['lineal_mobile'];
        $lineal_address = $params['lineal_address'];
        $other_relation = $params['other_relation'];
        $other_name = $params['other_name'];
        $other_mobile = $params['other_mobile'];
        $other_address = $params['other_address'];
        if ($lineal_name && $lineal_mobile) {
            $c = new ContactInfo();
            $c->name_contact = $lineal_name;
            $c->relation = $lineal_relation;
            $c->mobile = $lineal_mobile;
            $c->address = $lineal_address;
            array_push($contacts, $c);
        }
        if ($other_name && $other_mobile) {
            $c = new ContactInfo();
            $c->name_contact = $other_name;
            $c->relation = $other_relation;
            $c->mobile = $other_mobile;
            $c->address = $other_address;
            array_push($contacts, $c);
        }
        $appForm->contact_info = $contacts;

        // 图像认证
        $photos = array();
        $photosArray = $params['photos'];
        foreach ($photosArray as $photo){
            $photoObj = new PhotoInfo();
            $photoObj->photo_type = $photo['photo_type'];
            $photoObj->photo_id = $photo['photo_id'];
            $photoObj->photo_desc = $photo['photo_desc'];
            array_push($photos, $photoObj);
        }
        $appForm->photo_info = $photos;

        // 社交账号
        $accounts = array();
        //foreach ($accountArray as $account) {
        //    $a = new AccountInfo();
        //    $a->acct_type = $account['acct_type'];
        //    $a->acct_id = $account['acct_id'];
        //    $a->password = $account['password'];
        //    $a->source = $account['source'];
        //    $a->key = $account['key'];
        //    $a->token = $account['token'];
        //    $a->auth_status = $account['auth_status'];
        //    $a->auth_type = $account['auth_type'];
        //    array_push($accounts, $a);
        //}
        $appForm->account_info = $accounts;

        // 设备信息
        //$appForm->timezone = $params['timezone'];
        //$appForm->resolution = $params['resolution'];
        //$appForm->platform = $params['platform'];
        //$appForm->openDatabase = $params['openDatabase'];
        //$appForm->language = $params['language'];
        //$appForm->hasSessionStorage = $params['hasSessionStorage'];
        //$appForm->hasLocalStorage = $params['hasLocalStorage'];
        //$appForm->hasIndexDb = $params['hasIndexDb'];
        //$appForm->addBehavior = $params['addBehavior'];
        //$appForm->cpuClass = $params['cpuClass'];
        //$appForm->colorDepth = $params['colorDepth'];
        //$appForm->eid = $params['eid'];
        //$appForm->getCanvasFingerprint = $params['getCanvasFingerprint'];
        //$appForm->fingerprint = $params['fingerprint'];
        //$appForm->getPluginsString = $params['getPluginsString'];
        //$appForm->userAgent = $params['userAgent'];

        $d = new Device();
        $d->terminal_type     = input('terminal_type');
        $d->os_platform       = input('os_platform');
        $d->os_version        = input('os_version');
        $d->resolution        = input('resolution');
        $d->network_type      = input('network_type');
        $d->local_ips         = input('local_ips');
        $d->uuid              = input('uuid');
        $d->mac_address       = input('mac_address');
        $d->longitude         = input('longitude');
        $d->latitude          = input('latitude');
        $d->fingerprint       = input('fingerprint');
        $d->eid               = input('eid');
        $d->open_uuid         = input('open_uuid');
        $d->device_id         = input('device_id');
        $d->is_smulator       = input('is_smulator');
        $d->mobile_sim        = input('mobile_sim');
        $d->is_root           = input('is_root');
        $d->escrow            = input('escrow');  // 设备指纹
        $d->ext               = input('ext');
        $appForm->device = $d;


        $orders = array();
        //foreach ($orderInfoArray as $order) {
        //    $o = new OrderInfo();
        //    $o->salesman_id = $order['salesman_id'];
        //    $o->campaign_id = $order['campaign_id'];
        //    $o->order_no = $order['order_no'];
        //    $o->order_time = $order['order_time'];
        //    $o->merchant_id = $order['merchant_id'];
        //    $o->total_quantity = $order['total_quantity'];
        //    $o->total_amount = $order['total_amount'];
        //
        //    /*以下为收货人信息*/
        //    $o->receiver_province = $order['receiver_province'];
        //    $o->receiver_city = $order['receiver_city'];
        //    $o->receiver_zone = $order['receiver_zone'];
        //    $o->receiver_add = $order['receiver_add'];
        //    $o->receiver_mobile = $order['receiver_mobile'];
        //    $o->receiver_name = $order['receiver_name'];
        //
        //    /*以下为旅游产品专属字段*/
        //    $o->departure_time = $order['departure_time'];
        //    $o->return_time = $order['return_time'];
        //    $o->travel_num = $order['travel_num'];
        //    $o->kids_num = $order['kids_num'];
        //    $o->travel_type = $order['travel_type'];
        //    $o->has_visa = $order['has_visa'];
        //    $o->origin = $order['origin'];
        //    $o->destination = $order['destination'];
        //    $o->is_offline = $order['is_offline'];
        //
        //    $orderItemArray = $order['order_item_info'];
        //    $orderItems = array();
        //    foreach ($orderItemArray as $orderItem) {
        //        $oi = new OrderItemInfo();
        //        $oi->sku = $orderItem['sku'];
        //        $oi->goods_name = $orderItem['goods_name'];
        //        $oi->category_level1_id = $orderItem['category_level1_id'];
        //        $oi->category_level2_id = $orderItem['category_level2_id'];
        //        $oi->category_level3_id = $orderItem['category_level3_id'];
        //        $oi->quantity = $orderItem['quantity'];
        //        $oi->price = $orderItem['price'];
        //        array_push($orderItems, $oi);
        //    }
        //    $o->order_item_info = $orderItems;
        //    array_push($orders, $o);
        //}
        $appForm->order_info = $orders;

        $app = new DoApp();
        $response = $app->send($appForm);

        // 格式化结果
        if ($response->resCode == '0000') {
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $response->handlerData;
        } else {
            $result['status'] = $response->resCode;
            $result['message'] = $response->resMsg;
            $result['data'] = $response->handlerData;
        }
        return $result;
    }
    private function upload ($name, $path) {
        // 提交信息
        $app = new DoApp();
        $response = $app->upload($name,$path);
        if ($response->resCode == '0000') {
            $id = $response->handlerData->fileId;
        } else {
            $id = 0;
        }
        return $id;
    }
}