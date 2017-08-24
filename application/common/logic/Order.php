<?php
namespace app\common\logic;


use think\Model;
use think\Loader;
use app\common\tools\Helper;

class Order extends Model
{
    private $rate = array(0.05,0.075,0.1,0.125,1); // 延期费率
    private $delayDays = 5;  // 每次延期5天
    private $delayTimes = 4; // 最多四次延期

    public function getSiteInfo ()
    {
        $order = Loader::model('Site');
        $data = $order-> getSiteInfo(['key'=> 'mihua_loan']);
        return $data;
    }
    public function getCost($params) {
        $amount = $params['limit'];
        $day = $params['day'];
        $siteInfo = $this->getSiteInfo();


        $periodRate = $siteInfo->rate / 100;
        $manage = $siteInfo->manage / 100;
        $rateDay = 0;
        $rateAmount = 0;
        foreach ($siteInfo->period as $vo) {
            if ($vo['amount'] == $day) {
                $rateDay = $vo['rate'] / 100;
            }
        }
        foreach ($siteInfo->bankroll as $vo) {
            if ($vo['amount'] == $amount) {
                $rateAmount = $vo['rate'] / 100;
            }
        }

        $serviceCost = $amount * $rateDay; // 服务费
        $interests = $amount * $periodRate / 30 * $day; // 利息

        $total = $interests + $serviceCost + $manage;

        return array(
            'manage' => $manage,
            'cost' => $serviceCost,
            'interests' => $interests,
            'total' => $total,
            'deposit' => $amount - $total,
        );
    }

    /**
     * 获取订单信息
     * @param $params
     * @return array
     */
    public function getInfo($params)
    {
        $result = array('status' => 0, 'message' => '添加错误', 'data' => array());
        $order = Loader::model('Orders');
        $where = array();
        if (array_key_exists('id', $params)) {
            $where['id'] = $params['id'];
        }
        if (array_key_exists('no', $params)) {
            $where['no'] = $params['no'];
        }
        $resultOrder = $order->where($where)->find();
        if (!$resultOrder) {
            $result['status'] = 301;
            $result['message'] = '没有找到数据';
        } else {
            $vo = $resultOrder;
            $data = array(
                'id' => $vo->id,
                'uid' => $vo->uid,
                'no' => $vo->no,
                'purpose' => $vo->purpose,
                'limit' => $vo->limit,
                'period' => $vo->period,
                'deposit' => $vo->deposit,
                'rate' => $vo->rate,
                'insurance' => $vo->insurance,
                'term' => $vo->term,
                'repay_type' => $vo->repay_type,
                'auditing_time' => $vo->auditing_time,
                'expire_time' => $vo->expire_time,
                'pay_time' => $vo->pay_time,
                'expire_cost' => $vo->expire_cost,
                'expire_count' => $vo->expire_count,
                'expire_info' => $vo->expire_info,
                'overdue_cost' => $vo->overdue_cost,
                'repay_cost' => $vo->repay_cost,
                'paybill' => $vo->paybill,
                'repay_time' => $vo->repay_time,
                'progress' => $vo->progress,
                'message' => $vo->message,
                'created_time' => $vo->created_time,
                'status' => $vo->status,
            );
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $data;
        }
        return $result;
    }
    public function progressCount($user_id){
        $orderModel = Loader::model('Orders');
        $where = array(
            'uid' => array('eq',$user_id),
            'status' => array('neq',2),
        );

        $result = $orderModel->where(function ($query) use ($where) {
            $query->where($where);
        })->where(function ($query) {
            $query->where('progress', 'NOT IN','1,100');
        })->count();

        return $result;
    }
    public function add($params)
    {
        $result = array(
            'status' => -1,
            'message' => '添加错误',
        );
        $order = Loader::model('Orders');
        $count = $this->progressCount($params['uid']);

        if ($count > 0) {
            $result['status'] = 301;
            $result['message'] = '你有正在进行中的订单';
        } else {
            $newOrder           = new $order;
            $newOrder->uid      = $params['uid'];  // 用户编号
            $newOrder->no       = Helper::uuid();  // 订单编号
            $newOrder->purpose  = '';      // 借款用途
            $newOrder->limit    = $params['amount'];   // 申请额度
            $newOrder->period   = $params['day'];     // 申请期限
            $newOrder->deposit  = 0;    // 到账金额
            $newOrder->rate     = 0;    // 年利率
            $newOrder->insurance = 0;   // 保险费率
            $newOrder->status    = 0;
            $newOrder->created_time = time();
            $newOrder->save();
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $newOrder;
        }
        return $result;

    }
    public function getList($params)
    {
        $result = array('status' => -1, 'message' => '添加错误', 'data' => array());
        $order = Loader::model('Orders');
        $where = array();
        if (!array_key_exists('count', $params)) {
            $params['count'] = 10;
        }
        if (array_key_exists('uid', $params)) {
            $where['uid'] = $params['uid'];
        }
        if (array_key_exists('type', $params)) {
            switch ($params['type']) {
                case '0':
                    $where = function ($query) {
                        $query->where('status', 'eq', 0);
                    };
                    break;
                case '1':
                    $where = function ($query) {
                        $query->where('status', 'eq', 2);
                    };
                    break;
                case '2':
                    $where = function ($query) {
                        $query->where('status', 'eq', 1)->where('progress', 'eq', 0);
                    };
                    break;
                case '3':
                    $where = function ($query) {
                        $query->where('status', 'eq', 1)->where('progress', 'eq', 10);
                    };
                    break;
                case '20':
                    $where = function ($query) {
                        $query->where('status', 'eq', 1)->where('progress', 'egt', 11)->where('progress', 'lt', 30);
                    };
                    break;
                case '30':
                    $where = function ($query) {
                        $query->where('status', 'eq', 1)->where('progress', 'eq', 30);
                    };
                    break;
                case '100':
                    $where = function ($query) {
                        $query->where('status', 'eq', 1)->where('progress', 'gt', 99);
                    };
                    break;
                default:
                    break;
            }
        }
        $map = array();
        if (array_key_exists('key', $params)) {
            if ($params['key'] != '') {
                $map = function ($query) use ($params) {
                    $query->where('created_time', 'eq', $params['key']);
                };
            }
        }

        $userTotal = $order->where($map)->where($where)->count('id');

        $resultOrder = $order->where($map)->where($where)->order('id desc,status')->paginate($params['count']);
        $page = $resultOrder->render();
        if (!$resultOrder) {
            $result['status'] = 301;
            $result['message'] = '没有找到数据';
        } else {
            $data = array();
            foreach ($resultOrder as $vo) {
                $item = array(
                    'id' => $vo->id,
                    'uid' => $vo->uid,
                    'no' => $vo->no,
                    'purpose' => $vo->purpose,
                    'limit' => $vo->limit,
                    'period' => $vo->period,
                    'deposit' => $vo->deposit,
                    'rate' => $vo->rate,
                    'insurance' => $vo->insurance,
                    'term' => $vo->term,
                    'repay_type' => $vo->repay_type,
                    'auditing_time' => $vo->auditing_time,
                    'expire_time' => $vo->expire_time,
                    'pay_time' => $vo->pay_time,
                    'expire_cost' => $vo->expire_cost,
                    'expire_count' => $vo->expire_count,
                    'expire_info' => $vo->expire_info,
                    'overdue_cost' => $vo->overdue_cost,
                    'repay_cost' => $vo->repay_cost,
                    'paybill' => $vo->paybill,
                    'repay_time' => $vo->repay_time,
                    'progress' => $vo->progress,
                    'message' => $vo->message,
                    'created_time' => $vo->created_time,
                    'status' => $vo->status,
                );
                if (!array_key_exists('uid', $params) && $vo->UserDetail) {
                    $user = $vo->UserDetail;
                    $userInfo = array(
                        'username' => $user->username,
                        'phone' => $user->phone,
                        'idcard' => $user->idcard,
                        'bankcard' => $user->bankcard,
                        'work' => $user->work,
                        'contacts' => $user->contacts,
                        'status' => $user->status,
                        'created_time' => $user->created_time
                    );
                    $item['user'] = $userInfo;
                }
                array_push($data, $item);
            }

            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $data;
            $result['total'] = $userTotal;
            $result['page'] = $page;
        }
        return $result;
    }

    /**
     * 用户确认订单
     * @param $params
     * @return array
     */
    public function confirm($params)
    {
        $result = array('status' => 0, 'message' => '更新错误');
        $orderModel = Loader::model('Orders');

        $where = array(
            'id' => $params['id'],
            'status' => 0
        );
        $resultOrder = $orderModel->where($where)->find();

        if (!$resultOrder) {
            $result['status'] = 401;
            $result['message'] = '数据没有找到';
        } else {
            $info = $this->getCost(array(
                'limit' => $resultOrder->limit,
                'day' => $resultOrder->period,
            ));
            $resultOrder->status = 1;
            $resultOrder->deposit = $info['deposit'];
            $resultOrder->repay_cost = $resultOrder->limit;

            $data = $resultOrder->save();
            if ($data > 0) {
                $newResult = $this->getInfo($params);
                if ($newResult['status'] != 200) {
                    $result['status'] = 305;
                    $result['message'] = '获取数据失败';
                } else {

                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $newResult['data'];
                }
            } else {
                $result['status'] = 301;
                $result['message'] = '更新失败';
            }
        }

        return $result;
    }
    /**
     * 用户取消订单
     * @param $params
     * @return array
     */
    public function cancel($params)
    {
        $result = array('status' => -1, 'message' => '更新错误',);
        $orderModel = Loader::model('Orders');
        $data = $orderModel->where('id', $params['id'])->setField('status', 2);
        if ($data > 0) {
            $newResult = $this->getInfo($params);
            if ($newResult['status'] != 200) {
                $result['status'] = 305;
                $result['message'] = '获取数据失败';
            } else {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $newResult['data'];
            }
        } else {
            $result['status'] = 301;
            $result['message'] = '更新失败';
        }
        return $result;
    }

    /**
     * 获取扣费订单详情
     * @param $params
     * @return array
     */
    public function getDeductInfo($params)
    {
        $result = array('status' => 0, 'message' => '更新错误');

        $deductModel = Loader::model('Deduct');
        $where = array();

        if (array_key_exists('id', $params)) {
            $where['id'] = $params['id'];
        }
        if (array_key_exists('uid', $params)) {
            $where['uid'] = $params['uid'];
        }
        if (array_key_exists('oid', $params)) {
            $where['oid'] = $params['oid'];
        }
        if (array_key_exists('pay_type', $params)) {
            $where['pay_type'] = $params['pay_type'];
        }
        if (array_key_exists('no', $params)) {
            $where['created_time'] = $params['no'];
        }

        $deductResult = $deductModel->where($where)->find();
        if (!$deductResult) {
            // 这里添加扣款
            $result['status'] = 301;
            $result['message'] = '没有找到可用还款订单';
        } else {
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = array(
                'id' => $deductResult->id,
                'uid' => $deductResult->uid,
                'oid' => $deductResult->oid,
                'days' => $deductResult->days,
                'amount' => $deductResult->amount,
                'pay_type' => $deductResult->pay_type,
                'status' => $deductResult->status,
                'paybill' => $deductResult->paybill,
                'card_no' => $deductResult->card_no,
                'bank_code' => $deductResult->bank_code,
                'bank_name' => $deductResult->bank_name,
                'settle_date' => $deductResult->settle_date,
                'desc' => $deductResult->desc,
                'created_time' => $deductResult->created_time,
            );
        }
        return $result;
    }

    /**
     * 获取指定订单和续期类型的有效扣费订单信息
     * @param $uid
     * @param $oid
     * @param $type
     * @return array
     */
    public function getValidDeduct($uid,$oid,$type)
    {
        $result = array('status' => 0, 'message' => '更新错误');
        $deductModel = Loader::model('Deduct');
        $where = array(
            'uid' => $uid,
            'oid' => $oid,
            'pay_type' => $type
        );
        // TODO: 查询条件有问题
        $map = array(
            'status' => ['in', '2,9'],
            'created_time' => ['BETWEEN', time() - (2 * 60 * 60) .','. time()]
        );
        $deductSearch = $deductModel->where(function ($query) use ($where) {
            $query->where($where);
        })->where(function ($query) use ($map) {
            $query->where($map);
        })->find();

        if (!$deductSearch) {
            // 这里添加扣款
            $result['status'] = 301;
            $result['message'] = '没有找到可用还款订单';
        } else {
            if ($deductSearch->status == 9) { // 支付成功
                $result['status'] = 200;
                $result['message'] = 'success';
            } else if($deductSearch->status == 2) { // 银行处理中，请勿重复提交
                $result['status'] = 201;
                $result['message'] = 'success';
            } else { // 有效订单，返回订单号
                $result['status'] = 202;
                $result['message'] = 'success';
            }
            $deductResult = $this->getDeductInfo(array('id'=>$deductSearch->id));
            if (!$deductResult) {
                $result['message'] = '系统错误:没有找到订单信息';
            } else {
                $result['data'] = $deductResult['data'];
            }

        }
        return $result;
    }

    /**
     * 订单延期
     * @param $params
     * @return array
     */
    public function delay($params)
    {
        $result = array('status' => 0, 'message' => '更新错误',);
        $orderModel = Loader::model('Orders');

        $where = array(
            'id' => $params['id'],
            'status' => 1
        );
        $resultOrder = $orderModel->where($where)->find();
        if (!$resultOrder) {
            $result['status'] = 401;
            $result['message'] = '没有找到订单';
        } else {
            $count = $resultOrder->expire_count; // 剩余延期次数
            $progress = $resultOrder->progress;  // 当前进度

            if ($count == 0) {
                $result['status'] = 402;
                $result['message'] = '延期次数超限';
            }
            if ($result['status'] == 0) {
                // 第N次续期
                $current_times =  $this->delayTimes - $count + 1;
                // 续期手续费
                $current_cost = $resultOrder->limit * $this->rate[ $current_times - 1 ];

                $validResult = $this->getValidDeduct( $resultOrder->uid, $resultOrder->id, $current_times);
                if ($validResult['status'] == 301) {
                    $deductModel = Loader::model('Deduct');
                    $deduct = new $deductModel();
                    $deduct->uid = $resultOrder->uid;
                    $deduct->oid = $resultOrder->id;
                    $deduct->days = $this->delayDays;
                    $deduct->amount = $current_cost;
                    $deduct->pay_type = $current_times;
                    $deduct->status = 0;
                    $deduct->created_time = time();
                    $deduct->save();

                    $deductResult = $this->getDeductInfo(array('id'=>$deduct->id));

                    if ($deductResult['status'] != 200) {
                        $result['status'] = 301;
                        $result['message'] = '新订单保存失败';
                    } else {

                        $resultOrder->progress = 21; // 延期申请中
                        $resultOrder->save();

                        $result['status'] = 200;
                        $result['message'] = '新订单编号';
                        $result['data'] = $deductResult['data'];
                    }


                } else if ($validResult['status'] == 202) {
                    $result['status'] = 201;
                    $result['message'] = '订单有效';
                    $result['data'] = $validResult['data'];
                } else {
                    // 提醒系统处理中
                    $result['status'] = 403;
                    $result['message'] = '系统处理中';
                }


            }
        }
        return $result;
    }
    /**
     * 第三方支付回显结果保存
     * @param $params
     * @return array
     */
    public function saveDeductOrderInfo($params)
    {
        $result = array('status' => 0, 'message' => '更新错误');
        $deductModel = Loader::model('Deduct');
        $where = array(
            'created_time' => $params['no_order'],
        );
        $deductSearch = $deductModel->where($where)->find();

        if (!$deductSearch) {
            // 这里添加扣款
            $result['status'] = 301;
            $result['message'] = '没有找到可用还款订单';
        } else {

            $deductSearch->paybill = $params['oid_paybill'];
            $deductSearch->desc = $params['result_pay'].'|'.$params['money_order'];
            $deductSearch->save();

            $orderModel = Loader::model('Orders');
            $where = array(
                'id' => $deductSearch->oid,
                'status' => 1
            );
            $resultOrder = $orderModel->where($where)->find();
            if (!$resultOrder) {
                $result['status'] = 401;
                $result['message'] = '订单查询失败';
            } else {
                if ($params['result_pay'] != 'SUCCESS') {
                    $resultOrder->progress = 20; // 延期支付失败
                    $data = $resultOrder->save();

                    if ($data > 0) {
                        $result['status'] = 200;
                        $result['message'] = 'success';
                    } else {
                        $result['status'] = 301;
                        $result['message'] = '延期进度修改失败';
                    }
                } else {
                    $result['status'] = 200;
                    $result['message'] = 'success';
                }
            }
        }
        return $result;

    }
    /**
     * 第三方支付成功后的回调处理
     * @param $params
     * @return array
     */
    public function updateDeductOrderInfo($params)
    {
        $result = array('status' => 0, 'message' => '更新错误');
        $deductModel = Loader::model('Deduct');
        $where = array(
            'created_time' => $params['no_order'],
        );
        $deductSearch = $deductModel->where($where)->find();

        if (!$deductSearch) {
            // 这里添加扣款
            $result['status'] = 301;
            $result['message'] = '没有找到可用还款订单';
        } else {
            $deductSearch->status = 9;
            $deductSearch->save();

            $orderModel = Loader::model('Orders');
            $where = array(
                'id' => $deductSearch->oid
            );

            $resultOrder = $orderModel->where($where)->find();
            if (!$resultOrder) {
                $result['status'] = 401;
                $result['message'] = '订单查询失败';
            } else {
                $count = $resultOrder->expire_count; // 剩余延期次数
                $cost = $resultOrder->expire_cost;   // 延期总费用
                $this_cost = $resultOrder->limit * $this->rate[$this->delayTimes - $count]; // 本次延期费用
                $info = json_decode($resultOrder->expire_info);

                if (is_array($info)) {
                    array_push($info, time());
                } else {
                    $info = array(time());
                }
                $resultOrder->progress = 22; // 延期成功
                $resultOrder->expire_cost = $cost + $this_cost;
                $resultOrder->expire_info = json_encode($info);
                $resultOrder->expire_count = $count - 1;
                $resultOrder->expire_time = $resultOrder->expire_time + $this->delayDays * 24 * 60 * 60;

                $data = $resultOrder->save();
                if ($data > 0) {
                    $newResult = $this->getInfo($where);
                    if ($newResult['status'] != 200) {
                        $result['status'] = 305;
                        $result['message'] = '获取数据失败';
                    } else {
                        $result['status'] = 200;
                        $result['message'] = 'success';
                        $result['data'] = $newResult['data'];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 用户还款申请信息
     * @param $params
     * @return array
     */
    public function getRepayInfo($params)
    {
        $result = array('status' => 0, 'message' => '更新错误');
        $orderResult = $this->getInfo($params);
        if ($orderResult['status'] != 200) {
            $result['status'] = 301;
            $result['message'] = '没有找到订单信息';
        } else {
            $orderInfo = $orderResult['data'];
            $validResult = $this->getValidDeduct($orderInfo['uid'],$orderInfo['id'],0);
            if ($validResult['status'] == 301) {
                $deductModel = Loader::model('Deduct');
                $deduct = new $deductModel();
                $deduct->uid = $orderInfo['uid'];
                $deduct->oid = $orderInfo['id'];
                $deduct->days = $this->delayDays;
                $deduct->amount = $orderInfo['repay_cost'];
                $deduct->pay_type = 0;
                $deduct->status = 0;
                $deduct->created_time = time();
                $deduct->save();

                $deductResult = $this->getDeductInfo(array('id' => $deduct->id));

                if ($deductResult['status'] != 200) {
                    $result['status'] = 301;
                    $result['message'] = '新订单保存失败';
                } else {
                    $result['status'] = 200;
                    $result['message'] = '新订单编号';
                    $result['data'] = $deductResult['data'];
                    $result['data']['orderInfo'] = $orderInfo;
                }
            } else if ($validResult['status'] == 202) {
                $result['status'] = 201;
                $result['message'] = '订单有效';
                $result['data'] = $validResult['data'];
                $result['data']['orderInfo'] = $orderInfo;
            } else {
                // 提醒系统处理中
                $result['status'] = 403;
                $result['message'] = '系统处理中';
            }

        }
        return $result;
    }
    public function saveRepay($params) {
        $result = array('status' => 0, 'message' => '更新错误',);
        $where = array(
            'created_time' => $params['no_order'],
        );
        $deductModel = Loader::model('Deduct');
        $deductSearch = $deductModel->where($where)->find();

        if (!$deductSearch) {
            // 这里添加扣款
            $result['status'] = 301;
            $result['message'] = '没有找到可用还款订单';
        } else {
            $deductSearch->paybill = $params['oid_paybill'];
            $deductSearch->desc = $params['money_order'];
            $deductSearch->save();

            $result['status'] = 200;
            $result['message'] = 'success';
        }
        return $result;
    }
    /**
     * 用户还款申请成功后的回调处理[还款成功]
     * @param $params
     * @return array
     */
    public function repay($params)
    {
        $result = array('status' => 0, 'message' => '更新错误',);

        $where = array(
            'created_time' => $params['no_order'],
        );
        $deductModel = Loader::model('Deduct');
        $deductSearch = $deductModel->where($where)->find();

        if (!$deductSearch) {
            // 这里添加扣款
            $result['status'] = 301;
            $result['message'] = '没有找到可用还款订单';
        } else {
            $deductSearch->status = 9;
            $deductSearch->paybill = $params['paybill'];
            $deductSearch->card_no = $params['card_no'];
            $deductSearch->bank_code = $params['bank_code'];
            $deductSearch->settle_date = $params['settleDate'];
            $deductSearch->desc = $params['result_pay'] . '|' . $params['no_agree']. '|' . $params['id_no']. '|' . $params['acct_name'];
            $deductSearch->save();
        }

        $orderModel = Loader::model('Orders');
        $where = array(
            'id' => $deductSearch['oid'],
            'status' => 1
        );
        $resultOrder = $orderModel->where($where)->find();
        if (!$resultOrder) {
            $result['status'] = 301;
            $result['message'] = '未找到数据';
        } else {
            if ($params['result_pay'] == 'SUCCESS') {
                // 设置订单还款成功
                $resultOrder->progress = 100;
                $resultOrder->repay_time = time();
                $data = $resultOrder->save();
            }
            $newResult = $this->getInfo(array('id'=>$deductSearch['oid']));
            if ($newResult['status'] != 200) {
                $result['status'] = 305;
                $result['message'] = '获取数据失败';
            } else {
                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $newResult['data'];
            }
        }
        return $result;
    }

    /**
     * 财务确认支付后修改还款时间
     * @param $params
     * @return array
     */
    public function doPay($params)
    {
        $result = array('status' => 0, 'message' => '更新错误',);
        $orderModel = Loader::model('Orders');

        $where = array(
            'id' => $params['id'],
            'progress' => 10,
            'status' => 1
        );
        $resultOrder = $orderModel->where($where)->find();
        if (!$resultOrder) {
            $result['status'] = 301;
            $result['message'] = '未找到数据';
        } else {
            $day = $resultOrder->period;
            $resultOrder->pay_time = time();
            $resultOrder->expire_time = time() + $day * 24 * 60 * 60;
            $data = $resultOrder->save();
            if ($data > 0) {
                $newResult = $this->getInfo($params);
                if ($newResult['status'] != 200) {
                    $result['status'] = 305;
                    $result['message'] = '获取数据失败';
                } else {

                    $result['status'] = 200;
                    $result['message'] = 'success';
                    $result['data'] = $newResult['data'];
                }
            } else {
                $result['status'] = 302;
                $result['message'] = '更新失败';
            }
        }
        return $result;
    }
    /**
     * 财务确认支付后签约成功回调
     * @param $params
     * @return array
     */
    public function savePay($params)
    {
        $result = array('status' => 0, 'message' => '更新错误',);
        $orderModel = Loader::model('Orders');

        $where = array(
            'created_time' => $params['repayment_no']
        );
        $resultOrder = $orderModel->where($where)->find();
        if (!$resultOrder) {
            $result['status'] = 301;
            $result['message'] = '未找到数据';
        } else {
            $resultOrder->progress = 11;
            $data = $resultOrder->save();
            if ($data > 0) {
                $result['status'] = 200;
                $result['message'] = 'success';
            } else {
                $result['status'] = 302;
                $result['message'] = '保存签约协议失败';
            }
        }
        return $result;
    }

    /**
     * 获取风控信息
     * @param $params
     * @return array
     */
    public function getAudit($params)
    {
        $result = array(
            'status' => -1,
            'message' => '添加错误',
            'data' => array()
        );
        $RiskModel = Loader::model('Risk');
        $where = array();
        if (array_key_exists('uid', $params)) {
            $where['uid'] = $params['uid'];
        }
        if (array_key_exists('id', $params)) {
            $where['noBusb'] = $params['id'];
        }

        $resultRisk = $RiskModel->where($where)->select();
        if (!$resultRisk) {
            $result['status'] = 301;
            $result['message'] = '没有找到数据';
        } else {
            $data = array();
            foreach ($resultRisk as $vo) {
                $item = array(
                    'id' => $vo->id,
                    'uid' => $vo->uid,
                    'noBusb' => $vo->noBusb,
                    'noBus' => $vo->noBus,
                    'creditLimit' => $vo->creditLimit,
                    'creditTerm' => $vo->creditTerm,
                    'reasonCode' => $vo->reasonCode,
                    'reason' => json_decode($vo->reason),
                    'interestCode' => $vo->interestCode,
                    'feeRateCode' => $vo->feeRateCode,
                    'amtDownpay' => $vo->amtDownpay,
                    'amtMonthrepay' => $vo->amtMonthrepay,
                    'dataProd' => $vo->dataProd,
                    'status' => $vo->status,
                    'created_time' => $vo->created_time
                );
                array_push($data, $item);
            }

            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $data;
        }
        return $result;
    }

    /**
     * 系统驳回申请
     * @param $params
     */
    public function doAudit($params) {
        $result = array('status' => -1, 'message' => '更新错误',);
        $orderModel = Loader::model('Orders');
        $upParams = array(
            'message'=> $params['message'],
            'progress' => 1,
            'auditing_time'=> time()
        );
        $resultOrder = $orderModel->where('id', $params['id'])->update($upParams);
        if ($resultOrder > 0) {
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $this->getInfo($params);
        } else {
            $result['status'] = 301;
            $result['message'] = '保存失败';
        }
        return $result;
    }
    /**
     * 保存风控结果
     * @param $params
     * @return array
     */
    public function saveAudit($params)
    {
        $result = array('status' => -1, 'message' => '更新错误',);
        $RiskModel = Loader::model('Risk');
        $where = array(
            'noBus' => $params['noBus']
        );
        $resultOrder = $RiskModel->where($where)->find();

        if ($resultOrder) {
            // 记录已经存在
            $result['status'] = 301;
            $result['message'] = '记录已经存在';
        } else {
            $status = 0;
            switch ($params['advice']) {
                case 'cancel':     // 1:cancel系统原因
                    $status = 1;
                    break;
                case 'limit':      // 2:limit风控限制
                    $status = 2;
                    break;
                case 'lackdt':     // 3:lackdt数据不足
                    $status = 3;
                    break;
                case 'reject':     // 4:reject拒绝
                    $status = 4;
                    break;
                case 'accept':     // 9:accept通过
                    $status = 9;
                    break;
                default:
                    break;
            }
            $new = new $RiskModel;
            $new-> uid = $params['uid'];
            $new-> noBusb = $params['noBusb'];
            $new-> noBus = $params['noBus'];
            $new-> creditLimit = $params['creditLimit'];
            $new-> creditTerm = $params['creditTerm'];
            $new-> reasonCode = $params['reasonCode'];
            $new-> reason = $params['reason'];
            // $new-> interestCode = $params['interestCode'];
            // $new-> feeRateCode = $params['feeRateCode'];
            $new-> amtDownpay = $params['amtDownpay'];
            $new-> amtMonthrepay = $params['amtMonthrepay'];
            $new-> dataProd = $params['dataProd'];
            $new-> status = $status;
            $new-> created_time = time();
            $new->save();

            $orderModel = Loader::model('Orders');
            $upParams = array(
                'message'=> '',
                'progress' => 10,
                'auditing_time'=> time()
            );
            if ($status == 9) {
                $upParams['message'] = '风控审核通过';
                $upParams['progress'] = 10;
                $orderModel->where('id', $params['noBusb'])->update($upParams);
            } else {
                $upParams['message'] = '审核未通过';
                $upParams['progress'] = 1;
                $orderModel->where('id', $params['noBusb'])->update($upParams);
            }

            $newResult = $this->getInfo($params);
            if ($newResult['status'] != 200) {
                $result['status'] = 305;
                $result['message'] = '获取数据失败';
            } else {

                $result['status'] = 200;
                $result['message'] = 'success';
                $result['data'] = $newResult['data'];
            }
        }
        return $result;
    }
}