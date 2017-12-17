<?php

namespace app\event\manager;

use app\event\BaseEvent;
use GHank\WSNotice\SL;
use app\model\User;

class Admin extends BaseEvent
{
	public static function permission_reload($from, $data)
	{
		$user = new User();
		$userInfo = $user->where('uid', '=', $data->id)->find();
		if (!empty($userInfo)) {
			self::push($userInfo['fd']);
		}
	}

	public static function group_reload($from, $data)
	{
		self::broadcastMyGroup($from, function ($fd) use ($data){
			self::push($fd, $data);
		});
	}
}