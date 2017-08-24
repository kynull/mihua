<?php
namespace app\common\service;

use think\Model;
use think\Loader;

class LogService extends Model
{
	public function record($remark)
	{
		$log = Loader::model('Log','logic');
		return $log->record($remark);
	}
	
}