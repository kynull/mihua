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

use LLpay\Pay;

class Index extends Base
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
        $this->redirect('Order/index');
    }
    public function login()
    {
        // 模板输出
        return $this->fetch('index');
    }
    public function signin()
    {
        $phone = input('param.phone');
        $this->assign('phone', $phone);
        // 模板输出
        return $this->fetch('signin');
    }
    public function reset()
    {
        $phone = input('param.phone');
        $reset = input('param.reset');
        if ($reset == '1') {
            $reset = 1;
            $user = Loader::model('UserService','service');
            $userResult = $user->getUserInfo($phone);
            if ($userResult['status'] != 200) {
                $this->error('用户不存在','Wap/Order/index');
                return;
            }
        } else {
            $reset = 0;
        }
        $this->assign('phone', $phone);
        $this->assign('reset', $reset);
        // 模板输出
        return $this->fetch('reset');
    }

    /**
     * 用户注册协议
     */
    public function agreement() {
        // 模板输出
        return $this->fetch('agreement');
    }
    public function test()
    {
        $uid = 1;
        $oid = 100;
        $type = 0;
        $deductModel = Loader::model('Deduct');
        $orderModel = Loader::model('Orders');
        $where = array(
            'uid' => $uid,
            'oid' => $oid,
            'pay_type' => $type
        );
        $map = array(
            'status' => ['in', '2,9'],
            'created_time'=> ['BETWEEN', time() - (2 * 60 * 60) .','. time()]
        );
        $deductSearch = $deductModel
            ->where(function ($query) use($where) {
                $query->where($where);
            })
            ->where(function ($query) use($map) {
                $query->whereOr($map);
            })
            ->fetchSql(true)
            ->find();
        $where = array(
            'uid' => array('eq',$uid),
        );
        $result = $orderModel->where(function ($query) use ($where) {
                $query->where($where);
            })->where(function ($query) {
                $query->where('progress', 'neq',1)->where('progress','neq',100);
            })
            ->fetchSql(true)
            ->count();
        echo $result;
    }

    public function pay ()
    {
        $llpay_url = 'https://wap.lianlianpay.com/signApply.htm';
        $user_id = input('post.user_id');
        $acct_name = input('post.acct_name');
        $id_no = input('post.id_no');
        $user_phone = input('post.user_phone');
        $card_no = input('post.card_no');

        $user_id = '500123';
        $user_phone = '18602396267';      // 18725659987
        $id_no = '500235198402027659';    // 500223199304097708
        $acct_name = '陈开云';             // 谢双朵
        $user_mail = 'cjky@bilai.net';
        $card_no = '6228480478635285370'; // 谢双朵
        $card_no = '6226221106998351';  // 62175632000022455867:中国 6226221106998351:民生银行 6225768788256897:信用卡
        $created_time = '1400000';

        $params = array (
            'user_id' => $user_id,
            'acct_name' => $acct_name,
            'id_no' => $id_no,
            'user_mail' => $user_mail,
            'user_phone' => $user_phone,
            'card_no' => $card_no,
            'created_time' => $created_time
        );
        $pay = new \llpay\Pay();
        $para = $pay->Apply($params);
        echo $para;
    }
}
