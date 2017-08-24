<?php

namespace risk\model\base;


class OrderInfo extends Base
{

    public $salesman_id;   // 销售员
    public $campaign_id;   //
    public $order_no;      // 订单号
    public $order_time;    // 订单时间
    public $merchant_id;   // 商品编号
    public $total_quantity; // 购买数量
    public $total_amount;   // 购买金额

    /*以下为收货人信息*/
    public $receiver_province;  // 收货地址[省]
    public $receiver_city;      // 收货地址[市]
    public $receiver_zone;      // 收货地址[区/县]
    public $receiver_add;       // 收货地址
    public $receiver_mobile;    // 收货人电话
    public $receiver_name;      // 收货人

    /*以下为旅游产品专属字段*/
    public $departure_time;     // 离开时间
    public $return_time;        // 返程时间
    public $travel_num;         // 人数[成人]
    public $kids_num;           // 人数[小孩]
    public $travel_type;        // 类型
    public $has_visa;           // visa卡
    public $origin;             // 起点
    public $destination;        // 目的地
    public $is_offline;         // 线下产品

    public $order_item_info = array();
}