<?php
namespace app\common\model;

use think\Config;
use think\Db;
use think\Loader;
use think\Model;

class User extends Base
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'mihua_users';
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }
    public function IdcardDetail()
    {
        return $this->hasOne('Idcard','uid')->field('id,no,username,birthday,gender,face,front,back,status,message,verify_uid,verify_time,created_time');
    }
    public function ContactsDetail()
    {
        return $this->hasMany('Contacts','uid')->field('id,relations,cname,mobile,address,status,created_time');
    }
    public function BankcardDetail()
    {
        return $this->hasOne('Bankcard','uid')->field('id,no,card_type,username,idcard,bank_code,bank_name,bank_province,bank_city,status,agreeno,message,verify_uid,verify_time,created_time');
    }
    public function WorkDetail()
    {
        return $this->hasOne('Work','uid')->field('id,province,city,area,address,company,phone,identity,job,chsi,status,message,verify_uid,verify_time,created_time');
    }
}