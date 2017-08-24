<?php
namespace risk\model;
use \risk\model\base\Base;

class PayDayAppForm extends Base
{
    public $id_coop;

    public $no_busb;

    public $name_custc;

    public $id_card;

    public $mobile;

    public $marital_status;

    public $email;

    public $abode_province_code;

    public $abode_city_code;

    public $abode_zone_code;

    public $abode_add;

    public $bank_card_no;

    public $apply_time;

    public $prod_sub_code;

    public $app_limit;

    public $app_term;

    public $month_repayment;

    public $call_bcak_url;

    public $device;

    public $photo_info = array();

    public $contact_info = array();

    public $account_info = array();
}