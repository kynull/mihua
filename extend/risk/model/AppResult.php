<?php
namespace risk\model;
use \risk\model\base\Base;

class AppResult extends Base
{
    //申请单状态-对应advice
    public $advice;

    //授信额度
    public $creditLimit;
    //授信期数
    public $creditTerm;
    //决策原因编码
    public $reasonCode;
    //对外决策原因
    public $reason = array();
    //利率
    public $interestCode;
    //首付款
    public $amtDownpay;
    //每期还款
    public $amtMonthrepay;
    //费率
    public $feeRateCode;

    //唯一的请求id
    public $noBus;

    //客户的请求单号
    public $noBusb;

    public $dataProd = array();
}