<?php
namespace app\common\logic;

use think\Model;
use think\Loader;

class Log extends Model
{
	public function record($data)
	{
		$log = Loader::model('Log');
		return $log->record($data);
	}
}