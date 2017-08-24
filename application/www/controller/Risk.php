<?php
namespace app\www\controller;

use risk\model\AppForm;
use risk\model\base\Device;
use risk\model\base\ContactInfo;

use risk\DoApp;

class Risk extends Base
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
    public function index(){
        return $this->fetch('index');
    }
    public function do() {
        $appForm = new AppForm();

        $appForm->id_coop       = '78fb6q93d14cua0bbsa9aa49ebe601y3'; // input("id_coop");
        $appForm->id_custc      = input("id_custc");
        $appForm->no_bus        = input("no_bus");

        $appForm->code_bus    = input('code_bus');
        $appForm->no_busb     = input('no_busb');

        $appForm->name_custc      = input("name");
        $appForm->id_type         = input("id_type");
        $appForm->id_card         = input("id_card");
        $appForm->mobile          = input("mobile");

        $appForm->social_identity = input("social_identity");

        $appForm->email           = input("email");
        $appForm->marital_status  = input("marital_status");
        $appForm->month_income    = input('month_income');

        // 住址信息
        $appForm->abode_province_code   = ''; //input("abode_province_code");
        $appForm->abode_city_code       = ''; //input("abode_city_code");
        $appForm->abode_zone_code       = ''; //input("abode_zone_code");
        $appForm->abode_add             = input("abode_add");
        $appForm->home_tel              = input("home_tel");

        // 单位信息
        $appForm->emp_province_code  = ''; //input("emp_province_code");
        $appForm->emp_city_code      = ''; //input("emp_city_code");
        $appForm->emp_zone_code      = ''; //input("emp_zone_code");
        $appForm->emp_add            = input("emp_add");
        $appForm->emp_name           = input("emp_name");
        $appForm->emp_type            = input('emp_type');
        $appForm->emp_dept            = input('emp_dept');
        $appForm->emp_title           = input('emp_title');
        $appForm->emp_tel             = input('emp_tel');
        $appForm->emp_tel_ext         = input('emp_tel_ext');

        // 学校信息
        $appForm->school_name         = input('school_name');     // 学校名称
        $appForm->education_level     = input('education_level'); // 教育程度(学历层次)
        $appForm->education           = input('education');       // 教育程度(学历类)
        $appForm->graduate_date       = input('graduate_date');   // 毕业)间

        // 银行卡信息
        $appForm->bank_card_no       = input("bank_card_no");       // 银行卡号
        $appForm->bank_code          = ''; //input("bank_code");          // 银行编码
        $appForm->bank_province_code = ''; //input("bank_province_code"); // 银行地址编码（省）
        $appForm->bank_city_code     = ''; //input("bank_city_code");     // 银行地址编码（市）

        // 产品信息
        $appForm->prod_type       = input("prod_type");    // 产品类型
        $appForm->prod_code       = input("prod_code");    // 产品代码
        $appForm->app_limit       = input("app_limit");    // 申请额度
        $appForm->app_term        = input("app_term");     // 申请期数
        $appForm->prod_child_code = input("prod_child_code"); // 子产品编码
        $appForm->prod_sub_code   = input("prod_sub_code");   // 这个是富贵日志占用
        $appForm->loan_purpose    = input("loan_purpose");    // 贷款用途
        $appForm->apply_time      = input("apply_time");      // 申请时间
        $appForm->repay_type      = input("repay_type");      //
        $appForm->month_repayment = input("month_repayment"); // 每期应还款
        $appForm->annual_rate     = input("annual_rate");     // 年利率
        $appForm->down_payment    = input("down_payment");    // 首付款
        $appForm->insurance_rate  = input("insurance_rate");  // 保险费率
        $appForm->insurance_amt   = input("insurance_amt");   //
        $appForm->riskrank_bus    = input("riskrank_bus");    // 商户提示风险等级
        $appForm->channel_info    = input('channel_info');

        $appForm->is_create_sales = input("is_create_sales"); //

        // 联系人
        $contacts = array();
        $lineal_relation    = input("lineal_relation");
        $lineal_name        = input("lineal_name");
        $lineal_mobile      = input("lineal_mobile");
        $lineal_address     = input("lineal_address");
        $other_relation    = input("other_relation");
        $other_name        = input("other_name");
        $other_mobile      = input("other_mobile");
        $other_address     = input("other_address");
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
        $id_photo        = input("id_photo");
        $id_front        = input("id_front");
        $id_back         = input("id_back");
        $photos = array();
        //foreach ($photosArray as $photo){
        //    $photoObj = new PhotoInfo();
        //    $photoObj->photo_type = $photo['photo_type'];
        //    $photoObj->photo_id = $photo['photo_id'];
        //    $photoObj->photo_desc = $photo['photo_desc'];
        //    array_push($photos, $photoObj);
        //}
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

        // H5设备信息
        //$appForm->timezone             = input("timezone");
        //$appForm->resolution           = input("resolution");
        //$appForm->platform             = input("platform");
        //$appForm->openDatabase         = input("openDatabase");
        //$appForm->language             = input("language");
        //$appForm->hasSessionStorage    = input("hasSessionStorage");
        //$appForm->hasLocalStorage      = input("hasLocalStorage");
        //$appForm->hasIndexDb           = input("hasIndexDb");
        //$appForm->addBehavior          = input("addBehavior");
        //$appForm->cpuClass             = input("cpuClass");
        //$appForm->colorDepth           = input("colorDepth");
        //$appForm->eid                  = input("eid");
        //$appForm->getCanvasFingerprint = input("getCanvasFingerprint");
        //$appForm->fingerprint          = input("fingerprint");
        //$appForm->getPluginsString     = input("getPluginsString");
        //$appForm->userAgent            = input("userAgent");

        // Phone设备信息
        $d = new Device();
        $d->terminal_type              = input('terminal_type');
        $d->os_platform                = input('os_platform');
        $d->os_version                 = input('os_version');
        $d->resolution                 = input('resolution');
        $d->network_type               = input('network_type');
        $d->local_ips                  = input('local_ips');
        $d->uuid                       = input('uuid');
        $d->mac_address                = input('mac_address');
        $d->longitude                  = input('longitude');
        $d->latitude                   = input('latitude');
        $d->fingerprint                = input('fingerprint');
        $d->eid                        = input('eid');
        $d->open_uuid                  = input('open_uuid');
        $d->device_id                  = input('device_id');
        $d->is_smulator                = input('is_smulator');
        $d->mobile_sim                 = input('mobile_sim');
        $d->is_root                    = input('is_root');
        $d->escrow                     = input('escrow'); //设备指纹
        $d->ext                        = input('ext');
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

        // 提交信息
        $app = new DoApp();
        $response = $app->send($appForm);

        // 处理结果
        $result = array ('status' => -1, 'message'=> '未知错误', 'data' => '');
        // echo "appClient.syncApprove:" . var_dump($response) . "<br/>";
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
        return json($result);
    }


}