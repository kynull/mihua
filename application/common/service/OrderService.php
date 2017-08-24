<?php
namespace app\common\service;

use think\Model;
use think\Loader;

class OrderService extends Model
{
    public function getSiteInfo()
    {
        $order = Loader::model('Order','logic');
        $data = $order-> getSiteInfo();
        return $data;
    }
    public function getCost($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> getCost($params);
        return $data;
    }
    public function getInfo($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> getInfo($params);
        return $data;
    }
    public function getList($params) {
        $order = Loader::model('Order','logic');
        $data = $order-> getList($params);
        return $data;
    }
    public function add($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> add($params);
        return $data;
    }
    public function confirm($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> confirm($params);
        return $data;
    }
    public function cancel($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> cancel($params);
        return $data;
    }

    public function getDeductInfo($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> getDeductInfo($params);
        return $data;
    }
    public function saveDeductOrderInfo($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> saveDeductOrderInfo($params);
        return $data;
    }
    public function updateDeductOrderInfo($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> updateDeductOrderInfo($params);
        return $data;
    }
    public function delay($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> delay($params);
        return $data;
    }
    public function getRepayInfo($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> getRepayInfo($params);
        return $data;
    }
    public function saveRepay($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> saveRepay($params);
        return $data;
    }
    public function repay($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> repay($params);
        return $data;
    }

    public function doPay($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> doPay($params);
        return $data;
    }
    public function savePay($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> savePay($params);
        return $data;
    }
    public function saveAudit($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> saveAudit($params);
        return $data;
    }
    public function doAudit($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> doAudit($params);
        return $data;
    }
    public function getAudit($params)
    {
        $order = Loader::model('Order','logic');
        $data = $order-> getAudit($params);
        return $data;
    }

}