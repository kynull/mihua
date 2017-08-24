<?php
namespace risk\model;

use risk\model\base\Base;

class AppForm extends Base
{

    public $id_coop;              // * 商户编码
    public $id_custc;             //
    public $no_bus;               //

    public $code_bus;             // 商户类型 0: 零售商 1: 合作商
    public $no_busb;              // - 商户申请单号

    public $name_custc;           // * 姓名
    public $id_type;              // 证件类型
    public $id_card;              // * 证件号码
    public $mobile;               // * 手机号

    public $social_identity;      // 社会身份

    public $marital_status;       // - 婚姻状况
    public $email;                // - 常用邮箱
    public $month_income;         //

    public $abode_province_code; // * 现居地址（省）
    public $abode_city_code;     // * 现居地址（市）
    public $abode_zone_code;     // * 现居地址（区/县）
    public $abode_add;           // * 现居地址（详细地址）

    public $home_tel;            // 家庭电话

    public $emp_province_code;    // 工作地址（省）
    public $emp_city_code;        // 工作地址（市）
    public $emp_zone_code;        // 工作地址（区/县）
    public $emp_add;              // 工作地址（详细地址）
    public $emp_name;             // 工作单位名称

    public $emp_type;             //
    public $emp_dept;             // 工作部门
    public $emp_title;            //
    public $emp_tel;              // 工作单位联系电话
    public $emp_tel_ext;          //

    public $school_name;          // 学校名称

    public $education_level;      // 教育程度(学历层次)
    public $education;            // 教育程度(学历类别)
    public $enral_date;           //
    public $graduate_date;        // 毕业时间

    public $bank_card_no;            // - 银行卡号
    public $bank_code;               // 银行编码
    public $bank_province_code;      // 银行地址编码（省）
    public $bank_city_code;          // 银行地址编码（市）

    public $apply_time;             // * 申请时间
    public $is_create_sales;        //
    public $channel_info;           //
    public $prod_code;              // 产品代码

    public $prod_child_code;    // 子产品编码
    public $prod_sub_code;      // 这个是富贵日志占用

    public $prod_type;          // 产品类型

    public $loan_purpose;       // 贷款用途
    public $app_limit;          // * 申请额度
    public $app_term;           // * 申请期数

    public $repay_type;         //
    public $month_repayment;    // - 每期应还款
    public $annual_rate;        // 年利率
    public $down_payment;       // 首付款

    public $insurance_rate;     // 保险费率
    public $insurance_amt;      //

    public $call_bcak_url;      // 回调地址,异步必传

    public $riskrank_bus;  // 商户提示风险等级

    public $app_term_type;

    public $device;

    public $photo_info = array();
    public $contact_info = array();
    public $account_info = array();
    public $order_info = array();
}