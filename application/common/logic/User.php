<?php
namespace app\common\logic;

use think\Model;
use think\Loader;
use app\common\tools\Helper;

class User extends Model
{
    public function getList($params) {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $user = Loader::model('User');
        $where = array();
        if (!array_key_exists('count', $params)) {
            $params['count'] = 12;
        }
        if (array_key_exists('type', $params)) {
            switch ($params['type']) {
                case '1':
                    $where = function ($query) {
                        $query->where('idcard&bankcard&work&contacts', 'eq', 0);
                    };
                    break;
                case '2':
                    $where = function ($query) {
                        $query->whereOr('idcard', 'in', [1, 2])->whereOr('bankcard', 'in', [1, 2])->whereOr('work', 'in', [1, 2])->whereOr('contacts', 'in', [1, 2]);
                    };
                    break;
                case '3':
                    $where = function ($query) {
                        $query->where('idcard', 'eq', 3)->where('bankcard', 'eq', 3)->where('work', 'eq', 3)->where('contacts', 'eq', 3);
                    };
                    break;
                default:
                    break;
            }
        }
        $map = array();
        if (array_key_exists('type', $params)) {
            if ($params['key'] != '') {
                $map = function ($query) use ($params) {
                    $query->where('username', 'like', '%' . $params['key'] . '%')->whereOr('phone', 'like', '%' . $params['key'] . '%')->whereOr('created_time', 'like', '%' . $params['key'] . '%');
                };
            }
        }
        $userTotal = $user->where($map)->where($where)->count('id');

        $dataRow = $user->where($map)->where($where)->order('id desc,status')->paginate($params['count']);
        $page = $dataRow->render();

        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到此用户';
        } else {
            $list = array();
            foreach ($dataRow as $vo) {
                $item = array(
                    'id' => $vo->id,
                    'username' => $vo->username,
                    'phone' => $vo->phone,
                    'phonecode' => $vo->phonecode,
                    'phonestatus' => $vo->phonestatus,
                    'idcard' => $vo->idcard,
                    'bankcard' => $vo->bankcard,
                    'work' => $vo->work,
                    'contacts' => $vo->contacts,
                    'email' => $vo->email,
                    'qq' => $vo->qq,
                    'degrees' => $vo->degrees,
                    'marriage' => $vo->marriage,
                    'role' => $vo->role,
                    'created_time' => $vo->created_time,
                    'status' => $vo->status,
                );
                array_push($list, $item);
            }
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $list;
            $result['total'] = $userTotal;
            $result['page'] = $page;
        }
        return $result;
    }
    public function saveVerify($params) {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $smsModel = Loader::model('Sms');
        $map = array(
            'phone' => array('EQ', $params['phone']),
            'key' => array('EQ', $params['code']),
            'send_type' => array('EQ', $params['type']),
        );
        $map['TIMESTAMPDIFF(MINUTE,FROM_UNIXTIME(timestamp, "%Y-%m-%d %H:%i:%s"),now())']  = array('ELT','5');
        $dataRow = $smsModel->where($map)->find();
        if (!$dataRow) {
            $new = new $smsModel;
            $new->phone      = $params['phone'];
            $new->key = $params['code'];
            $new->send_type = $params['type'];
            $new->desc = $params['message'];
            $new->timestamp = time();
            $new->save();

            $result['status'] = 200;
            $result['message'] = 'success';
        } else {
            $result['status'] = 302;
            $result['message'] = '短信刚刚发送,请稍后再试';
        }

        return $result;
    }
    public function getVerify($params) {
        $result = ['status'=> 0, 'message'=>'参数错误', 'data'=>[], 'timestamp' => time()];
        $smsModel = Loader::model('Sms');
        $map = array(
            'phone' => array('EQ', $params['phone']),
            'key' => array('EQ', $params['code']),
        );
        // SELECT * FROM `mihua_sms` WHERE  `phone` = '18602396267'  AND `key` = '869376'  AND TIMESTAMPDIFF(MINUTE,FROM_UNIXTIME(timestamp, "%Y-%m-%d %H:%i:%s"),now()) <= '5' LIMIT 1;
        // SELECT TIMESTAMPDIFF(MINUTE,FROM_UNIXTIME(timestamp, "%Y-%m-%d %H:%i:%s"),now()) as 'time' FROM `mihua_sms` WHERE  `phone` = '18602396267'  AND `key` = '869376';
        $map['TIMESTAMPDIFF(MINUTE,FROM_UNIXTIME(timestamp, "%Y-%m-%d %H:%i:%s"),now())']  = array('ELT','5');
        $dataRow = $smsModel->where($map)->find();
        if (!$dataRow) {
            $result['status'] = 303;
            $result['message'] = '验证码无效';
        } else {
            $result['status'] = 200;
            $result['message'] = 'success';
        }
        return $result;
    }
    public function getUserInfo($mark)
    {
        $where = array();
        $result = array('status' => -1, 'message' => '查询错误');
        if($mark =='' || empty($mark)) return $result;
        $user = Loader::model('User');
        $len = strlen($mark);
        if(is_numeric($mark) && !Helper::VerifyUserName($mark)){
            $where['id'] = $mark;
        } else if ($len == 11) {
            $where['phone'] = $mark;
        } else if ($len == 6) {
            $where['invite_code'] = $mark;
        } else {
            $where['username'] = $mark;
        }
        $dataRow = $user->where($where)->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到此用户';
        } else {
            unset($dataRow['password']);
            $data = array(
                'id' => $dataRow->id,
                'username' => $dataRow->username,
                'phone' => $dataRow->phone,
                'phonestatus' => $dataRow->phonestatus,
                'idcard' => $dataRow->idcard,
                'bankcard' => $dataRow->bankcard,
                'work' => $dataRow->work,
                'contacts' => $dataRow->contacts,
                'province' => $dataRow->province,
                'city' => $dataRow->city,
                'area' => $dataRow->area,
                'address' => $dataRow->address,
                'period' => $dataRow->period,
                'invite_code' => $dataRow->invite_code,
                'email' => $dataRow->email,
                'qq' => $dataRow->qq,
                'degrees' => $dataRow->degrees,
                'marriage' => $dataRow->marriage,
                'role' => $dataRow->role,
                'created_time' => $dataRow->created_time,
            );

            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $data;
        }
        return $result;
    }
    public function getUserDetail($mark) {
        $where = array();
        $result = array('status' => -1, 'message' => '查询错误');
        if($mark =='' || empty($mark)) return $result;
        $user = Loader::model('User');
        $len = strlen($mark);
        if(is_numeric($mark) && !Helper::VerifyUserName($mark)){
            $where['id'] = $mark;
        } else if ($len == 11) {
            $where['phone'] = $mark;
        } else if ($len == 6) {
            $where['invite_code'] = $mark;
        } else {
            $where['username'] = $mark;
        }
        $dataRow = $user->where($where)->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到此用户';
        } else {
            unset($dataRow['password']);
            $data = array(
                'id' => $dataRow->id,
                'username' => $dataRow->username,
                'phone' => $dataRow->phone,
                'phonestatus' => $dataRow->phonestatus,
                'idcard' => $dataRow->idcard,
                'bankcard' => $dataRow->bankcard,
                'work' => $dataRow->work,
                'contacts' => $dataRow->contacts,
                'province' => $dataRow->province,
                'city' => $dataRow->city,
                'area' => $dataRow->area,
                'address' => $dataRow->address,
                'period' => $dataRow->period,
                'invite_code' => $dataRow->invite_code,
                'email' => $dataRow->email,
                'qq' => $dataRow->qq,
                'degrees' => $dataRow->degrees,
                'marriage' => $dataRow->marriage,
                'role' => $dataRow->role,
                'created_time' => $dataRow->created_time,
            );
            $idCardDetail = array();
            if ($dataRow->idcardDetail) {
                $item = $dataRow->idcardDetail;
                $idCardDetail = array(
                    'id' => $item->id,
                    'no' => $item->no,
                    'username' => $item->username,
                    'birthday' => $item->birthday,
                    'gender' => $item->gender,
                    'face' => $item->face,
                    'front' => $item->front,
                    'back' => $item->back,
                    'status' => $item->status,
                    'message' => $item->message,
                    'verify_uid' => $item->verify_uid,
                    'verify_time' => $item->verify_time,
                    'created_time' => $item->created_time,
                );
            }
            $data['idcardDetail'] = $idCardDetail;

            $contactsDetail = array();
            if ($dataRow->ContactsDetail) {
                foreach ($dataRow->ContactsDetail as $vo) {
                    $item = array(
                        'id' => $vo->id,
                        'relations' => $vo->relations,
                        'cname' => $vo->cname,
                        'mobile' => $vo->mobile,
                        'address' => $vo->address,
                        'status' => $vo->status,
                        'created_time' => $vo->created_time
                    );
                    array_push($contactsDetail, $item);
                }
            }
            $data['contactsDetail'] = $contactsDetail;

            $bankcardDetail = array();
            if ($dataRow->BankcardDetail) {
                $item = $dataRow->BankcardDetail;
                $bankcardDetail = array(
                    'id' => $item->id,
                    'no' => $item->no,
                    'card_type' => $item->card_type,
                    'username' => $item->username,
                    'idcard' => $item->idcard,
                    'bank_province' => $item->bank_province,
                    'bank_city' => $item->bank_city,
                    'bank_code' => $item->bank_code,
                    'bank_name' => $item->bank_name,
                    'status' => $item->status,
                    'agreeno' => $item->agreeno,
                    'message' => $item->message,
                    'verify_uid' => $item->verify_uid,
                    'verify_time' => $item->verify_time,
                    'created_time' => $item->created_time
                );
            }
            $data['bankcardDetail'] = $bankcardDetail;

            $workDetail = array();
            if ($dataRow->WorkDetail) {
                $item = $dataRow->WorkDetail;
                $workDetail = array(
                    'id' => $item->id,
                    'province' => $item->province,
                    'city' => $item->city,
                    'area' => $item->area,
                    'address' => $item->address,
                    'company' => $item->company,
                    'phone' => $item->phone,
                    'identity' => $item->identity,
                    'job' => $item->job,
                    'chsi' => $item->chsi,
                    'status' => $item->status,
                    'message' => $item->message,
                    'verify_uid' => $item->verify_uid,
                    'verify_time' => $item->verify_time,
                    'created_time' => $item->created_time
                );
            }
            $data['workDetail'] = $workDetail;

            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $data;
        }
        return $result;
    }
	public function confirm(array $params)
	{
        $result = array('status' => -1, 'message' => '查询错误',);
		$user = Loader::model('User');
        $map = [
            'phone' => $params['phone']
        ];
        $dataRow = $user->where($map)->find();
        if (!$dataRow) {
            $result['status'] = 201;
            $result['message'] = '没有此用户';
        } else {
            $result['status'] = 200;
            $result['message'] = '请先登录';
        }
		return $result;
	}

    /**
     * 产生一个唯一的店铺标示号
     * @return [string] [产生一个字符串]
     */
	private function createStringCode() {
        $newid = _random(6);
        $userResult = $this->getUserInfo($newid);
        if ($userResult['status'] == 200) {
            $newid = $this->createStoreID();
        }

        return $newid;
    }
    public function signup(array $params)
    {
        $result = array('status' => -1, 'message' => '查询错误',);
        $u = $this->confirm($params);
        if ($u['status'] != 201) {
            $result['status'] = 301;
            $result['message'] = '用户已经存在';
        } else {
            $user = Loader::model('User');
            $code = $this->createStringCode();
            $new           = new $user;
            $new->password   = $params['password'];
            $new->phone      = $params['phone'];
            $new->username   = substr_replace($params['phone'],'****',3,4);
            $new->invite_code = $code;
            $new->created_time = time();
            $new->save();

            $userResult = $this->getUserInfo($new->id);
            if ($userResult['status'] == 200) {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $userResult['data'];
            } else {
                $result['status'] = $userResult['status'];
                $result['message'] = $userResult['message'];
            }
        }
        return $result;
    }
    public function reset(array $params)
    {
        $result = array('status' => -1, 'message' => '查询错误',);
        $user = Loader::model('User');
        $map = [
            'phone' => $params['phone']
        ];
        $dataRow = $user->where($map)->find();
        if (!$dataRow) {
            $result['status'] = 201;
            $result['message'] = '没有此用户,请先注册';
        } else {
            $dataRow->password = $params['password'];
            $dataRow->save();
            $userResult = $this->getUserInfo($dataRow->id);
            if ($userResult['status'] != 200) {
                $result['status'] = $userResult['status'];
                $result['message'] = $userResult['message'];
            } else {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $userResult['data'];
            }
        }
        return $result;
    }
	public function signin(array $params)
	{
        $result = array(
            'status' => -1,
            'message' => '查询错误',
        );
		$user = Loader::model('User');
        $dataRow = $user->where('phone', 'eq', $params['phone'])->find();
		if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有此用户';
        } else {
		    if ($params['password'] == $dataRow['password']) {
                $result['status'] = 200;
                $result['message'] = 'success';
                $userResult = $this->getUserInfo($dataRow->id);
                if ($userResult['status'] == 200) {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $userResult['data'];
                } else {
                    $result['status'] = $userResult['status'];
                    $result['message'] = $userResult['message'];
                }
            } else {
                $result['status'] = 302;
                $result['message'] = '密码错误';
            }
        }
		return $result;
	}

	public function saveIDCard(array $params)
	{
        $result = array(
            'status' => -1,
            'message' => '查询错误',
        );
		$userModel = Loader::model('User');
        $dataRow = $userModel->where('id', 'eq', $params['uid'])->find();
		if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到您的基本信息';
        } else {
            $idCardModel = Loader::model('Idcard');
            $idcardRow = $idCardModel->where('uid', 'eq', $params['uid'])->find();
            if (!$idcardRow) {
                $new = new $idCardModel;
                $new->uid    = $dataRow['id'];
                $new->no    = $params['no'];
                $new->username    = $params['username'];
                $new->front    = $params['front'];
                $new->back    = $params['back'];
                $new->face    = $params['face'];
                $new->created_time = time();
                $new->status = 2;
                $new->save();
            } else {
                $idcardRow->no      = $params['no'];
                $idcardRow->username = $params['username'];
                $idcardRow->front   = $params['front'];
                $idcardRow->back    = $params['back'];
                $idcardRow->face    = $params['face'];
                $idcardRow->status = 2;
                $idcardRow->save();
            }
            $userModel->where('id', 'eq', $params['uid'])->setField('idcard', 2);

            $userResult = $this->getUserInfo($dataRow->id);
            if ($userResult['status'] == 200) {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $userResult['data'];
            } else {
                $result['status'] = $userResult['status'];
                $result['message'] = $userResult['message'];
            }

        }

		return $result;
	}
    public function getIDCard(array $params)
    {
        $result = array(
            'status' => -1,
            'message' => '查询错误',
        );
        $idCardModel = Loader::model('Idcard');
        $dataRow = $idCardModel->where('uid', 'eq', $params['uid'])->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到您的实名认证信息';
        } else {
            $data = array(
                'id' => $dataRow->id,
                'uid' => $dataRow->uid,
                'username' => $dataRow->username,
                'gender' => $dataRow->gender,
                'birthday' => $dataRow->birthday,
                'no' => $dataRow->no,
                'front' => $dataRow->front,
                'back' => $dataRow->back,
                'face' => $dataRow->face,
                'created_time' => $dataRow->created_time,
                'status' => $dataRow->status,
                'message' => $dataRow->message,
                'verify_time' => $dataRow->verify_time,
            );
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $data;
        }
        return $result;
    }
    public function auditIDCard(array $params)
    {
        $result = array(
            'status' => -1,
            'message' => '查询错误',
        );
        $userModel = Loader::model('User');
        $userRow = $userModel->where('id', 'eq', $params['uid'])->find();
        if (!$userRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到用户基本信息';
        } else {
            $idCardModel = Loader::model('Idcard');
            $idcardRow = $idCardModel->where('uid', 'eq', $params['uid'])->find();
            if ($idcardRow) {
                $idcardRow->no    = $params['no'];
                $idcardRow->username    = $params['username'];
                //$idcardRow->gender    = $params['gender'];
                //$idcardRow->birthday    = $params['birthday'];
                $idcardRow->message    = $params['message'];
                $idcardRow->verify_uid    = $params['verify_uid'];
                $idcardRow->verify_time = time();
                $idcardRow->status = $params['status'];
                $idcardRow->save();

                $userRow->idcard = $params['status'];
                $userRow->username = $params['username'];
                $userRow->save();
            }
            $userResult = $this->getUserInfo($userRow->id);
            if ($userResult['status'] == 200) {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $userResult['data'];
            } else {
                $result['status'] = $userResult['status'];
                $result['message'] = $userResult['message'];
            }

        }

        return $result;
    }
    public function saveContacts(array $params)
    {
        $result = array(
            'status' => -1,
            'message' => '查询错误',
        );
        $user = Loader::model('User');
        $dataRow = $user->where('id', 'eq', $params['uid'])->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到您的基本信息';
        } else {
            $contacts = Loader::model('Contacts');
            $contacts->where('uid', 'eq', $params['uid'])->setField('status', 0);

            foreach ($params['data'] as $vo) {
                $new = new $contacts;
                $new->uid      = $dataRow['id'];
                $new->relations = $vo['relations'];
                $new->cname     = $vo['cname'];
                $new->mobile   = $vo['mobile'];
                $new->address  = $vo['address'];
                $new->created_time = time();
                $new->status = 2;
                $new->save();
            }
            $user->where('id', 'eq', $params['uid'])->setField('contacts', 2);


            $userResult = $this->getUserInfo($dataRow->id);
            if ($userResult['status'] != 200) {
                $result['status'] = 300;
                $result['message'] = '没有找到您的资料';
            } else {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $userResult['data'];
            }

        }

        return $result;
    }
    public function getContacts(array $params) {
        $result = array(
            'status' => 0,
            'message' => '查询错误',
        );
        $userResult = $this->getUserInfo($params['uid']);
        if ($userResult['status'] != 200) {
            $result['status'] = 300;
            $result['message'] = '没有找到您的资料';
        }
        if ($result['status'] == 0) {
            $userInfo = $userResult['data'];
            $user = Loader::model('Contacts');
            $where = array(
                'uid' => $params['uid'],
                'status' => 3
            );
            $dataRow = $user->where($where)->select();
            if (!$dataRow) {
                $result['status'] = 301;
                $result['message'] = '没有找到您的紧急联系人信息';
            } else {
                $details = array();
                foreach ($dataRow as $vo) {
                    $item = array(
                        'id' => $vo->id,
                        'uid' => $vo->uid,
                        'relations' => $vo->relations,
                        'cname' => $vo->cname,
                        'mobile' => $vo->mobile,
                        'address' => $vo->address,
                        'created_time' => $vo->created_time,
                        'status' => $vo->status,
                    );
                    array_push($details, $item);
                }
                $data = array(
                    'status' =>  $userInfo['contacts'],
                    'details' => $details
                );
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $data;

            }
        }

        return $result;
    }
    public function auditContacts(array $params)
    {
        $result = array('status' => -1, 'message' => '查询错误',);
        $userModel = Loader::model('User');
        $dataRow = $userModel->where('id', 'eq', $params['uid'])->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到用户基本信息';
        } else {
            $ContactsModel = Loader::model('Contacts');
            $where = array(
                'uid' => $params['uid'],
                'status' => 2
            );
            $contactsRow = $ContactsModel->where($where)->select();

            foreach ($contactsRow as $vo) {
                //$vo->message    = $params['message'];
                $vo->verify_uid    = $params['verify_uid'];
                $vo->verify_time = time();
                $vo->status = $params['status'];
                $vo->save();
            }

            $userModel->where('id', 'eq', $params['uid'])->setField('contacts', $params['status']);

            $userResult = $this->getUserInfo($dataRow->id);
            if ($userResult['status'] == 200) {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $userResult['data'];
            } else {
                $result['status'] = $userResult['status'];
                $result['message'] = $userResult['message'];
            }

        }

        return $result;
    }
    public function saveBankCard(array $params)
    {
        $result = array('status' => -1, 'message' => '查询错误',);
        $user = Loader::model('User');
        $dataRow = $user->where('id', 'eq', $params['uid'])->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到您的基本信息';
        } else {
            $bankModel = Loader::model('Bankcard');
            $bankcardResult = $bankModel->where('uid', 'eq', $params['uid'])->find();

            $vo = $params['data'];
            if (!$bankcardResult) {
                $new = new $bankModel;
                $new->uid      = $dataRow['id'];
                $new->no       = $vo['no'];
                $new->idcard   = $vo['idcard'];
                $new->username = $vo['username'];
                $new->bank_code     = $vo['bank_code'];
                $new->bank_name     = $vo['bank_name'];
                $new->card_type     = $vo['card_type'];
                $new->bank_province = 0;
                $new->bank_city     = 0;
                $new->created_time  = time();
                $new->status = 0;
                $new->save();
            } else {
                $bankcardResult->no       = $vo['no'];
                $bankcardResult->idcard   = $vo['idcard'];
                $bankcardResult->username = $vo['username'];
                $bankcardResult->bank_code = $vo['bank_code'];
                $bankcardResult->bank_name = $vo['bank_name'];
                $bankcardResult->card_type = $vo['card_type'];
                $bankcardResult->created_time  = time();
                $bankcardResult->status = 0;
                $bankcardResult->save();
            }

            $user->where('id', 'eq', $params['uid'])->setField('bankcard', 0);

            $userResult = $this->getUserInfo($dataRow->id);
            if ($userResult['status'] != 200) {
                $result['status'] = 300;
                $result['message'] = '没有找到您的资料';
            } else {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $userResult['data'];
            }
        }

        return $result;
    }
    public function getBankCard(array $params) {
        $result = array(
            'status' => -1,
            'message' => '查询错误',
        );
        $BankcardModel = Loader::model('Bankcard');
        $dataRow = $BankcardModel->where('uid', 'eq', $params['uid'])->find();

        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到您的银行卡信息';
        } else {
            $data = array(
                'id' => $dataRow->id,
                'uid' => $dataRow->uid,
                'no' => $dataRow->no,
                'idcard' => $dataRow->idcard,
                'username' => $dataRow->username,
                'card_type' => $dataRow->card_type,
                'code' => $dataRow->bank_code,
                'bank_name' => $dataRow->bank_name,
                'province' => $dataRow->bank_province,
                'city' => $dataRow->bank_city,
                'created_time' => $dataRow->created_time,
                'status' => $dataRow->status,
                'message' => $dataRow->message,
                'verify_time' => $dataRow->verify_time,
            );
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $data;

        }
        return $result;
    }
    public function auditBankCard(array $params)
    {
        $result = array('status' => -1, 'message' => '查询错误',);
        $userModel = Loader::model('User');
        $dataRow = $userModel->where('id', 'eq', $params['uid'])->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到用户基本信息';
        } else {
            $bankcardModel = Loader::model('Bankcard');
            $idcardRow = $bankcardModel->where('uid', 'eq', $params['uid'])->find();
            if ($idcardRow) {
                $idcardRow->no          = $params['no'];
                $idcardRow->username    = $params['username'];
                $idcardRow->idcard      = $params['idcard'];
                $idcardRow->message     = $params['message'];
                $idcardRow->verify_uid  = $params['verify_uid'];
                $idcardRow->verify_time = time();
                $idcardRow->status      = $params['status'];
                $idcardRow->save();

                $userModel->where('id', 'eq', $params['uid'])->setField('bankcard', $params['status']);
            }
            $userResult = $this->getUserInfo($dataRow->id);
            if ($userResult['status'] == 200) {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $userResult['data'];
            } else {
                $result['status'] = $userResult['status'];
                $result['message'] = $userResult['message'];
            }

        }

        return $result;
    }
    public function applyBankCard(array $params)
    {
        $result = array(
            'status' => -1,
            'message' => '查询错误',
        );
        $userModel = Loader::model('User');
        $dataRow = $userModel->where('id', 'eq', $params['uid'])->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到用户基本信息';
        } else {
            $bankcardModel = Loader::model('Bankcard');
            $idcardRow = $bankcardModel->where('uid', 'eq', $params['uid'])->find();
            if ($idcardRow) {
                $idcardRow->agreeno     = $params['agreeno'];
                $idcardRow->verify_uid  = 9999;
                $idcardRow->verify_time = time();
                $idcardRow->message     = $params['message'];
                $idcardRow->status      = $params['status'];
                $idcardRow->save();

                $userModel->where('id', 'eq', $params['uid'])->setField('bankcard', $params['status']);
            }
            $userResult = $this->getBankCard($params);
            if ($userResult['status'] == 200) {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $userResult['data'];
            } else {
                $result['status'] = $userResult['status'];
                $result['message'] = $userResult['message'];
            }

        }

        return $result;
    }
    public function saveServiceCode(array $params)
    {
        $result = array(
            'status' => -1,
            'message' => '查询错误',
        );
        $user = Loader::model('User');
        $dataRow = $user->where('id', 'eq', $params['uid'])->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到您的基本信息';
        } else {
            $vo = $params['data'];
            if ($dataRow->phone == $vo['mobile']) {
                $dataRow->phonecode     = $vo['code'];
                $dataRow->phonestatus   = 2;
                $dataRow->save();

                $userResult = $this->getUserInfo($dataRow->id);
                if ($userResult['status'] == 200) {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $userResult['data'];
                } else {
                    $result['status'] = $userResult['status'];
                    $result['message'] = $userResult['message'];
                }
            } else {
                $result['status'] = 302;
                $result['message'] = '绑定手机号必须与注册手机号一致';
            }
        }

        return $result;
    }
    public function saveWork(array $params)
    {
        $result = array(
            'status' => -1,
            'message' => '查询错误',
        );
        $userModel = Loader::model('User');
        $dataRow = $userModel->where('id', 'eq', $params['uid'])->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到您的基本信息';
        } else {
            $workModel = Loader::model('Work');
            $workModel->where('uid', 'eq', $params['uid'])->setField('status', 0);

            $vo = $params['data'];
            $new = new $workModel;
            $new->uid      = $dataRow['id'];
            $new->identity = $vo['identity'];
            $new->company     = $vo['company'];
            $new->phone   = $vo['phone'];
            $new->address  = $vo['address'];
            $new->created_time = time();
            $new->status = 2;
            $new->save();
            $userModel->where('id', 'eq', $params['uid'])->setField('work', 2);

            $userResult = $this->getUserInfo($dataRow->id);
            if ($userResult['status'] == 200) {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $userResult['data'];
            } else {
                $result['status'] = $userResult['status'];
                $result['message'] = $userResult['message'];
            }

        }

        return $result;
    }
    public function getWork(array $params) {
        $result = array(
            'status' => -1,
            'message' => '查询错误',
        );
        $userModel = Loader::model('Work');
        $dataRow = $userModel->where('uid', 'eq', $params['uid'])->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到您的工作信息';
        } else {
            $data = array(
                'id' => $dataRow->id,
                'uid' => $dataRow->uid,
                'identity' => $dataRow->identity,
                'job' => $dataRow->job,
                'company' => $dataRow->company,
                'phone' => $dataRow->phone,
                'province' => $dataRow->province,
                'city' => $dataRow->city,
                'area' => $dataRow->area,
                'address' => $dataRow->address,
                'coordinate' => $dataRow->coordinate,
                'created_time' => $dataRow->created_time,
                'status' => $dataRow->status,
                'message' => $dataRow->message,
                'verify_time' => $dataRow->verify_time,
            );
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $data;

        }
        return $result;
    }
    public function auditWork(array $params)
    {
        $result = array(
            'status' => -1,
            'message' => '查询错误',
        );
        $userModel = Loader::model('User');
        $dataRow = $userModel->where('id', 'eq', $params['uid'])->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到用户基本信息';
        } else {
            $workModel = Loader::model('Work');
            $workRow = $workModel->where('uid', 'eq', $params['uid'])->find();
            if ($workRow) {
                $workRow->message    = $params['message'];
                $workRow->verify_uid    = $params['verify_uid'];
                $workRow->verify_time = time();
                $workRow->status = $params['status'];
                $workRow->save();

                $userModel->where('id', 'eq', $params['uid'])->setField('work', $params['status']);
            }
            $userResult = $this->getUserInfo($dataRow->id);
            if ($userResult['status'] == 200) {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $userResult['data'];
            } else {
                $result['status'] = $userResult['status'];
                $result['message'] = $userResult['message'];
            }

        }

        return $result;
    }
    public function saveLive(array $params)
    {
        $result = array(
            'status' => -1,
            'message' => '查询错误',
        );
        $userModel = Loader::model('User');
        $dataRow = $userModel->where('id', 'eq', $params['uid'])->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到您的基本信息';
        } else {
            $vo = $params['data'];
            $dataRow->period = $vo['period'];
            $dataRow->address     = $vo['address'];
            $dataRow->province   = $vo['province'];
            $dataRow->city   = $vo['city'];
            $dataRow->area   = $vo['area'];
            $dataRow->save();

            $userResult = $this->getUserInfo($dataRow->id);
            if ($userResult['status'] == 200) {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $userResult['data'];
            } else {
                $result['status'] = $userResult['status'];
                $result['message'] = $userResult['message'];
            }

        }

        return $result;
    }
    public function saveOther(array $params)
    {
        $result = array(
            'status' => -1,
            'message' => '查询错误',
        );
        $userModel = Loader::model('User');
        $dataRow = $userModel->where('id', 'eq', $params['uid'])->find();
        if (!$dataRow) {
            $result['status'] = 301;
            $result['message'] = '没有找到您的基本信息';
        } else {
            $vo = $params['data'];
            $dataRow->degrees = $vo['degrees'];
            $dataRow->marriage     = $vo['marriage'];
            $dataRow->qq   = $vo['qq'];
            $dataRow->email   = $vo['email'];
            $dataRow->save();


            $userResult = $this->getUserInfo($dataRow->id);
            if ($userResult['status'] == 200) {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $userResult['data'];
            } else {
                $result['status'] = $userResult['status'];
                $result['message'] = $userResult['message'];
            }

        }

        return $result;
    }
}