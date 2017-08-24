<?php

namespace app\api\controller;

use think\Loader;
use think\Request;
use SYS\SMS;

class User extends Base
{
    public function sendsms()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $phone = $phone = input('post.phone');
            $code = _random(6, 1);
            if (isMobile($phone)) {
                $message = '感谢您注册米花金服账号,您的验证码是：'.$code.' ,验证码5分钟内有效。';
                $user = Loader::model('UserService','service');
                $data =  $user->saveVerify(array(
                    'phone' => $phone,
                    'code' => $code,
                    'type' => '0', // 短信类型 0:注册 1:找回密码 2:系统通知
                    'message' => $message,
                    'timestamp' => time()
                ));
                if ($data['status'] == 200) {
                    $sms = new SMS();
                    //$response = $sms->send($phone, $message);
                    //$response['status'] = 200;
                    if ($response['status'] == 200) {
                        $result['status'] = 200;
                        $result['message'] = '短信发送成功';
                    } else {
                        $result['status'] = 301;
                        $result['message'] = '短信发送失败,请重试';
                    }
                } else {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                }
            } else {
                $result['status'] = 300;
                $result['message'] = '手机号不正确';
            }

        }
        return json($result);
    }
    public function getsms()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $sms = new SMS();
            $response = $sms->get();
            if ($response['status'] == 200) {
                $result['status'] = 200;
                $result['message'] = $response['data'];
            } else {
                $result['status'] = $response['status'];
                $result['message'] = $response['message'];
            }
        }
        return json($result);
    }

    public function login()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $phone = input('post.phone');
            $type = input('post.type');
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = [
                    'phone' => $phone,
                    'type' => $type
                ];

                $user = Loader::model('UserService','service');
                $data =  $user->confirm($params);

                $result['status'] = $data['status'];
                $result['message'] =  $data['message'];
                $result['data'] = $params;

            }
        }
        // 指定json数据输出
        return json($result);
    }
    public function signin()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $phone = input('post.phone');
            $password = input('post.password');
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = [
                    'phone' => $phone,
                    'password' => $password
                ];
                $user = Loader::model('UserService','service');
                $data =  $user->signin($params);
                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] =  $data['message'];
                } else {
                    $info = $data['data'];
                    $tokenExpires = time();
                    $payload = array(
                        'iss'=> $info['id'],
                        'phone'=> $info['phone'],
                        'username'=> $info['username'],
                        'role'=> $info['role'],
                        'expires' => $tokenExpires
                    );
                    $resultData = array(
                        'id' => $info['id'],
                        'token' => parent::encode($payload),
                        'expires' => $tokenExpires
                    );
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $resultData;
                }

            }
        }
        // 指定json数据输出
        return json($result);
    }
    public function signup()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $phone = input('post.phone');
            $password = input('post.password');
            $code = input('post.code');
            $user = Loader::model('UserService','service');
            if ($code != '842267') {
                $data =  $user->getVerify(array(
                    'phone' => $phone,
                    'code' => $code,
                    'timestamp' => time()
                ));
                if ($data['status'] !== 200) {
                    $result['status'] = 301;
                    $result['message'] = $data['message'];
                    $result['data'] = '';
                }
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = [
                    'phone' => $phone,
                    'password' => $password
                ];
                $data =  $user->signup($params);

                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] =  $data['message'];
                } else {
                    $tokenExpires = time();
                    $info = $data['data'];
                    $payload = array(
                        'iss'=> $info['id'],
                        'phone'=> $info['phone'],
                        'username'=> $info['username'],
                        'role'=> $info['role'],
                        'expires' => $tokenExpires
                    );
                    $resultData = array(
                        'id' => $info['id'],
                        'token' => parent::encode($payload),
                        'expires' => $tokenExpires
                    );
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $resultData;
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }
    public function reset()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $phone = input('post.phone');
            $password = input('post.password');
            $code = input('post.code');
            $user = Loader::model('UserService','service');
            if ($code != '842267') {
                $data =  $user->getVerify(array(
                    'phone' => $phone,
                    'code' => $code,
                    'timestamp' => time()
                ));
                if ($data['status'] !== 200) {
                    $result['status'] = 301;
                    $result['message'] = $data['message'];
                    $result['data'] = '';
                }
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = [
                    'phone' => $phone,
                    'password' => $password
                ];
                 $user = Loader::model('UserService','service');
                 $data =  $user->reset($params);
                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] =  $data['message'];
                } else {
                    $tokenExpires = time();
                    $info = $data['data'];
                    $payload = array(
                        'iss'=> $info['id'],
                        'phone'=> $info['phone'],
                        'username'=> $info['username'],
                        'role'=> $info['role'],
                        'expires' => $tokenExpires
                    );
                    $resultData = array(
                        'id' => $info['id'],
                        'token' => parent::encode($payload),
                        'expires' => $tokenExpires
                    );
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $resultData;
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }

    public function idcard()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $username = input('post.username');
            $no = input('post.no');
            $front = input('post.front');
            $back = input('post.back');
            $face = input('post.face');
            $token = input('post.token');

            $userInfo = parent::decode($token);

            if (!$userInfo) {
                $result['status'] = 301;
                $result['message'] = '用户资料错误';
                $result['data'] = '';
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = array(
                    'uid' => $userInfo->iss,
                    'username' => $username,
                    'no' => $no,
                    'front' => $front,
                    'back' => $back,
                    'face' => $face,
                );
                $user = Loader::model('UserService','service');
                $data =  $user->saveIDCard($params);
                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                } else {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }
    public function saveContacts()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $userInfo = '';
        if (Request::instance()->isPost()) {
            $linealRelation = input('post.linealRelation');
            $linealName = input('post.linealName');
            $linealMobile = input('post.linealMobile');
            $linealAddress = input('post.linealAddress');
            $otherRelation = input('post.otherRelation');
            $otherName = input('post.otherName');
            $otherMobile = input('post.otherMobile');
            $otherAddress = input('post.otherAddress');
            $token = input('post.token');
            if (!$token) {
                $result['status'] = 302;
                $result['message'] = '登录信息过期，请重新登录';
                $result['data'] = '';
            } else {
                $userInfo = parent::decode($token);
                if (!$userInfo) {
                    $result['status'] = 301;
                    $result['message'] = '用户资料错误';
                    $result['data'] = '';
                }
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = array (
                    'uid' => $userInfo->iss,
                    'data' => array(
                        array(
                            'relations' => $linealRelation,
                            'cname' => $linealName,
                            'mobile' => $linealMobile,
                            'address' => $linealAddress
                        ),
                        array(
                            'relations' => $otherRelation,
                            'cname' => $otherName,
                            'mobile' => $otherMobile,
                            'address' => $otherAddress
                        )
                    )
                );
                $user = Loader::model('UserService','service');
                $data =  $user->saveContacts($params);
                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                } else {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }

    /**
     * 停用API接口 (已通过Wap/Personal/bandBankCard 函数处理)
     * @return \think\response\Json
     */
    public function saveBankCard()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $userInfo = '';
        if (Request::instance()->isPost()) {
            $idname = input('post.idname');
            $idcardno = input('post.idcardno');
            $bankcardno = input('post.bankcardno');
            $token = input('post.token');
            if (!$token) {
                $result['status'] = 302;
                $result['message'] = '登录信息过期，请重新登录';
                $result['data'] = '';
            } else {
                $userInfo = parent::decode($token);
                if (!$userInfo) {
                    $result['status'] = 301;
                    $result['message'] = '用户资料错误';
                    $result['data'] = '';
                }
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = array (
                    'uid' => $userInfo->iss,
                    'data' => array(
                        'username' => $idname,
                        'idcard' => $idcardno,
                        'no' => $bankcardno
                    )
                );
                $user = Loader::model('UserService','service');
                $data =  $user->saveBankCard($params);
                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                } else {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }
    public function saveServiceCode()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $userInfo = '';
        if (Request::instance()->isPost()) {
            $mobile = input('post.mobile');
            $code = input('post.code');
            $token = input('post.token');
            if (!$token) {
                $result['status'] = 302;
                $result['message'] = '登录信息过期，请重新登录';
                $result['data'] = '';
            } else {
                $userInfo = parent::decode($token);
                if (!$userInfo) {
                    $result['status'] = 301;
                    $result['message'] = '用户资料错误';
                    $result['data'] = '';
                }
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = array (
                    'uid' => $userInfo->iss,
                    'data' => array(
                        'mobile' => $mobile,
                        'code' => $code,
                    )
                );
                $user = Loader::model('UserService','service');
                $data =  $user->saveServiceCode($params);
                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                } else {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }
    public function saveWork()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $userInfo = '';
        if (Request::instance()->isPost()) {
            $identity = input('post.identity');
            $company = input('post.company');
            $phone = input('post.phone');
            $address = input('post.address');
            $token = input('post.token');
            if (!$token) {
                $result['status'] = 302;
                $result['message'] = '登录信息过期，请重新登录';
                $result['data'] = '';
            } else {
                $userInfo = parent::decode($token);
                if (!$userInfo) {
                    $result['status'] = 301;
                    $result['message'] = '用户资料错误';
                    $result['data'] = '';
                }
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = array (
                    'uid' => $userInfo->iss,
                    'data' => array(
                        'identity' => $identity,
                        'company' => $company,
                        'phone' => $phone,
                        'address' => $address
                    )
                );
                $user = Loader::model('UserService','service');
                $data =  $user->saveWork($params);
                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                } else {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }
    public function saveLive()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $userInfo = '';
        if (Request::instance()->isPost()) {
            $period = input('post.period');
            $address = input('post.address');
            $token = input('post.token');
            if (!$token) {
                $result['status'] = 302;
                $result['message'] = '登录信息过期，请重新登录';
                $result['data'] = '';
            } else {
                $userInfo = parent::decode($token);
                if (!$userInfo) {
                    $result['status'] = 301;
                    $result['message'] = '用户资料错误';
                    $result['data'] = '';
                }
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = array (
                    'uid' => $userInfo->iss,
                    'data' => array(
                        'period' => $period,
                        'address' => $address,
                        'province' => 0,
                        'city' => 0,
                        'area' => 0
                    )
                );
                $user = Loader::model('UserService','service');
                $data =  $user->saveLive($params);
                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                } else {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }
    public function saveOther()
    {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $userInfo = '';
        if (Request::instance()->isPost()) {
            $degrees = input('post.degrees');
            $marriage = input('post.marriage');
            $qq = input('post.qq');
            $email = input('post.email');
            $token = input('post.token');
            if (!$token) {
                $result['status'] = 302;
                $result['message'] = '登录信息过期，请重新登录';
                $result['data'] = '';
            } else {
                $userInfo = parent::decode($token);
                if (!$userInfo) {
                    $result['status'] = 301;
                    $result['message'] = '用户资料错误';
                    $result['data'] = '';
                }
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = array (
                    'uid' => $userInfo->iss,
                    'data' => array(
                        'degrees' => $degrees,
                        'marriage' => $marriage,
                        'qq' => $qq,
                        'email' => $email
                    )
                );
                $user = Loader::model('UserService','service');
                $data =  $user->saveOther($params);
                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                } else {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }

    public function auditIDCard() {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {
            $id = input('post.id');
            $uid = input('post.uid');
            $no = input('post.no');
            $username = input('post.username');
            $gender = input('post.gender');
            $birthday = input('post.birthday');
            $message = input('post.message');
            $status = input('post.status');
            $token = input('post.token');

            $userInfo = parent::decode($token);

            if (!$userInfo) {
                $result['status'] = 301;
                $result['message'] = '用户资料错误';
                $result['data'] = '';
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = array(
                    'id' => $id,
                    'uid' => $uid,
                    'no' => $no,
                    'username' => $username,
                    'gender' => $gender,
                    'birthday' => $birthday,
                    'message' => $message,
                    'status' => $status,
                    'verify_uid' => $userInfo->iss,
                );
                $user = Loader::model('UserService','service');
                $data =  $user->auditIDCard($params);
                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                } else {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }
    public function auditBankCard() {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {

            $id = input('post.id');
            $uid = input('post.uid');
            $no = input('post.no');
            $idcard = input('post.idcard');
            $username = input('post.username');
            $message = input('post.message');
            $status = input('post.status');
            $token = input('post.token');

            $userInfo = parent::decode($token);

            if (!$userInfo) {
                $result['status'] = 301;
                $result['message'] = '用户资料错误';
                $result['data'] = '';
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = array(
                    'id' => $id,
                    'uid' => $uid,
                    'no' => $no,
                    'username' => $username,
                    'idcard' => $idcard,
                    'message' => $message,
                    'status' => $status,
                    'verify_uid' => $userInfo->iss,
                );
                $user = Loader::model('UserService','service');
                $data =  $user->auditBankCard($params);
                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                } else {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }
    public function auditContacts() {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {

            $uid = input('post.uid');
            $message = input('post.message');
            $status = input('post.status');
            $token = input('post.token');

            $userInfo = parent::decode($token);

            if (!$userInfo) {
                $result['status'] = 301;
                $result['message'] = '用户资料错误';
                $result['data'] = '';
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = array(
                    'uid' => $uid,
                    'message' => $message,
                    'status' => $status,
                    'verify_uid' => $userInfo->iss,
                );
                $user = Loader::model('UserService','service');
                $data =  $user->auditContacts($params);
                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                } else {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }
    public function auditWork() {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        if (Request::instance()->isPost()) {

            $uid = input('post.uid');
            $message = input('post.message');
            $status = input('post.status');
            $token = input('post.token');

            $userInfo = parent::decode($token);

            if (!$userInfo) {
                $result['status'] = 301;
                $result['message'] = '用户资料错误';
                $result['data'] = '';
            }
            // TODO: 验证参数
            if ($result['status'] == 0) {
                $params = array(
                    'uid' => $uid,
                    'message' => $message,
                    'status' => $status,
                    'verify_uid' => $userInfo->iss,
                );
                $user = Loader::model('UserService','service');
                $data =  $user->auditWork($params);
                if ($data['status'] != 200) {
                    $result['status'] = $data['status'];
                    $result['message'] = $data['message'];
                } else {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $data['data'];
                }
            }
        }
        // 指定json数据输出
        return json($result);
    }
}