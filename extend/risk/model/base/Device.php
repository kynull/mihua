<?php

namespace risk\model\base;


class Device extends Base
{
    public $terminal_type;
    public $os_platform;
    public $os_version;
    public $resolution;
    public $network_type;
    public $local_ips;
    public $uuid;
    public $mac_address;
    public $longitude;
    public $latitude;
    public $fingerprint;
    public $eid;
    public $open_uuid;
    public $device_id;
    public $is_smulator;
    public $mobile_sim;
    public $is_root;
    //设备指纹
    public $escrow = array();
}