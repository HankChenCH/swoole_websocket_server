<?php

namespace app\event\weapp;

use app\event\BaseEvent;

class Category extends BaseEvent
{
	public function syncProduct($from, $data)
	{
		self::broadcastMyGroup($from, function ($fd) use ($data){
			self::push($fd, $data);
		});
	}
}