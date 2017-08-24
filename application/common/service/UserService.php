<?php
namespace app\common\service;

use think\Model;
use think\Loader;

class UserService extends Model
{
    public function getList(array $params) {
        $user = Loader::model('User','logic');
        return $user->getList($params);
    }
    public function saveVerify(array $data) {
        $user = Loader::model('User','logic');
        return $user->saveVerify($data);
    }
    public function getVerify(array $data) {
        $user = Loader::model('User','logic');
        return $user->getVerify($data);
    }
    public function getUserInfo($id) {
        $user = Loader::model('User','logic');
        return $user->getUserInfo($id);
    }
    public function getUserDetail($id) {
        $user = Loader::model('User','logic');
        return $user->getUserDetail($id);
    }
	public function confirm(array $data)
	{
		$user = Loader::model('User','logic');
		return $user->confirm($data);
	}
    public function signup(array $params)
    {
        $user = Loader::model('User','logic');
        return $user->signup($params);
    }
    public function reset(array $params)
    {
        $user = Loader::model('User','logic');
        return $user->reset($params);
    }
	public function signin(array $params)
	{
		$user = Loader::model('User','logic');
		return $user->signin($params);
	}
	public function saveIDCard(array $params)
	{
		$user = Loader::model('User','logic');
		return $user->saveIDCard($params);
	}
	public function getIDCard(array $params)
	{
		$user = Loader::model('User','logic');
		return $user->getIDCard($params);
	}
	public function auditIDCard(array $params)
	{
		$user = Loader::model('User','logic');
		return $user->auditIDCard($params);
	}
	public function saveContacts(array $params)
	{
		$user = Loader::model('User','logic');
		return $user->saveContacts($params);
	}
	public function getContacts(array $params)
	{
		$user = Loader::model('User','logic');
		return $user->getContacts($params);
	}
	public function auditContacts(array $params)
	{
		$user = Loader::model('User','logic');
		return $user->auditContacts($params);
	}
    public function saveBankCard(array $params)
    {
        $user = Loader::model('User','logic');
        return $user->saveBankCard($params);
    }

    public function getBankCard(array $params)
    {
        $user = Loader::model('User','logic');
        return $user->getBankCard($params);
    }
    public function auditBankCard(array $params)
    {
        $user = Loader::model('User','logic');
        return $user->auditBankCard($params);
    }
    public function applyBankCard(array $params)
    {
        $user = Loader::model('User','logic');
        return $user->applyBankCard($params);
    }
    public function saveServiceCode(array $params)
    {
        $user = Loader::model('User','logic');
        return $user->saveServiceCode($params);
    }
	public function saveWork(array $params)
	{
		$user = Loader::model('User','logic');
		return $user->saveWork($params);
	}
    public function getWork(array $params)
    {
        $user = Loader::model('User','logic');
        return $user->getWork($params);
    }
    public function auditWork(array $params)
    {
        $user = Loader::model('User','logic');
        return $user->auditWork($params);
    }
    public function saveLive(array $params)
    {
        $user = Loader::model('User','logic');
        return $user->saveLive($params);
    }
    public function saveOther(array $params)
    {
        $user = Loader::model('User','logic');
        return $user->saveOther($params);
    }
	
}