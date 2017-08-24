<?php
namespace app\wap\controller;

use app\common\service\UserService;

use think\Config;
use think\Loader;
use think\Request;
use think\Url;
use think\Session;
use think\Cookie;
use llpay\Pay;

class Personal extends Base
{
    /**
     * 需要调用的前置方法列表
     **/
    protected $beforeActionList = [
        'init',
        'isLogin'  =>  ['except'=>'list,detail'],    // except 不使用前置方法
        'isAdmin'  =>  ['only'=>'add,save'],        // only 仅使用的前置方法
    ];
    /**
     * Get www/personal/index
     */
    public function index()
    {
        $userInfo = parent::isLogin();
        $user = Loader::model('UserService','service');

        $result =  $user->getUserInfo($userInfo->iss);
        $this->assign('userInfo', $result['data']);
        // 模板输出
        return $this->fetch('index');
    }
    public function base()
    {
        $userInfo = parent::isLogin();
        $user = Loader::model('UserService','service');

        $result =  $user->getUserInfo($userInfo->iss);
        $this->assign('userInfo', $result['data']);
        // 模板输出
        return $this->fetch('base');
    }
    public function contacts()
    {
        $userInfo = parent::isLogin();
        $user = Loader::model('UserService','service');
        $params = array(
            'uid' => $userInfo->iss,
        );
        $data =  $user->getContacts($params);
        $this->assign('info', $data);
        // 模板输出
        return $this->fetch('contacts');
    }

    public function bank()
    {
        $userInfo = parent::isLogin();
        $user = Loader::model('UserService','service');
        $params = array(
            'uid' => $userInfo->iss,
        );
        $result =  $user->getBankCard($params);

        $this->assign('BankInfo', $result);
        // 模板输出
        return $this->fetch('bank');
    }
    public function bandBankCard() {
        $userInfo = parent::isLogin();

        $idname = input('post.idname');
        $idcardno = input('post.idcardno');
        $cardno = input('post.bankcardno');
        $bankName = input('post.bank_name');
        $bankCode = input('post.bank_code');
        $cardType = input('post.card_type');

        $params = array (
            'uid' => $userInfo->iss,
            'data' => array(
                'username' => $idname,
                'idcard' => $idcardno,
                'no' => $cardno,
                'bank_name' => $bankName,
                'bank_code' => $bankCode,
                'card_type' => $cardType,
            )
        );

        $user = Loader::model('UserService','service');
        $userResult =  $user->saveBankCard($params);

        if ($userResult['status'] != 200) {
            $this->error('参数错误','Wap/Personal/bank');
            return;
        }
        $userInfo = $userResult['data'];

        $params = array(
            'user_id' => $userInfo['id'],
            'user_phone' => $userInfo['phone'],
            'user_mail' => $userInfo['email'],
            'created_time' => $userInfo['created_time'],
            'acct_name' => $idname,
            'id_no' => $idcardno,
            'card_no' => $cardno,
            'return_url' => url('Wap/Personal/bankCardReturn',[],true,true)
        );

        $pay = new Pay();
        $htmlFormApply = $pay->Apply($params);

        return $htmlFormApply;
    }
    public function bankCardReturn() {
        $pay = new Pay();
        $returnStatus = $_GET["status"];
        if ($returnStatus != '0000') {
            $this->error($_GET["result"],'Wap/Personal/bank');
            return;
        }

        $returnData = $pay->getApplyReturn();
        $params = array(
            'uid' => $returnData['data']['user_id'],
            'agreeno' => $returnData['data']['agreeno'],
            'message' => '快捷支付签约成功',
            'status' => 3,
        );
        $user = Loader::model('UserService','service');
        $resultData =  $user->applyBankCard($params);

        $this->assign('BankInfo', $resultData);
        // 模板输出
        return $this->fetch('bank');
    }

    public function phone()
    {
        $userInfo = parent::isLogin();
        $user = Loader::model('UserService','service');
        $result =  $user->getUserInfo($userInfo->iss);

        $this->assign('info', $result['data']);
        // 模板输出
        return $this->fetch('phone');
    }
    public function operator()
    {
        // 模板输出
        return $this->fetch('operator');
    }
    public function idcard()
    {
        $userInfo = parent::isLogin();
        $user = Loader::model('UserService','service');
        $params = array(
            'uid' => $userInfo->iss,
        );
        $data =  $user->getIDCard($params);
        $this->assign('info', $data);
        // 模板输出
        return $this->fetch('base_idcard');
    }
    public function work()
    {
        $userInfo = parent::isLogin();
        $user = Loader::model('UserService','service');
        $params = array(
            'uid' => $userInfo->iss,
        );
        $data =  $user->getWork($params);
        $this->assign('info', $data);
        // 模板输出
        return $this->fetch('base_work');
    }
    public function address()
    {
        $userInfo = parent::isLogin();
        $user = Loader::model('UserService','service');
        $result =  $user->getUserInfo($userInfo->iss);
        $this->assign('info', $result['data']);
        // 模板输出
        return $this->fetch('base_address');
    }
    public function other()
    {
        $userInfo = parent::isLogin();
        $user = Loader::model('UserService','service');
        $result =  $user->getUserInfo($userInfo->iss);
        $this->assign('info', $result['data']);
        // 模板输出
        return $this->fetch('base_other');
    }

    /**
     * Get www/personal/help
     */
    public function help()
    {
        // 模板输出
        return $this->fetch('help');
    }

    /**
     * Get www/personal/list
     */
    public function getList()
    {
        // 模板输出
        return $this->fetch('list');
    }
    /**
     * Get www/personal/:id
     */
    public function read()
    {
        // 模板输出
        return $this->fetch('detail');
    }
    /**
     * Get www/personal/:id/edit
     */
    public function edit()
    {
        // 模板输出
        return $this->fetch('edit');
    }
    /**
     * POST	www/personal
     */
    public function save()
    {
        $this->redirect('personal', ['id' => 2]);
    }
    /**
     * PUT	www/personal/:id
     */
    public function update()
    {
        $id = input('get.id');
        $this->success('保存成功', 'personal/' . $id);
    }
    /**
     * DELETE	www/personal/:id
     */
    public function delete()
    {
        $this->success('删除成功', 'personal/list');

    }

}
