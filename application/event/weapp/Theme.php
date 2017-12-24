<?php

namespace app\event\weapp;

use app\event\BaseEvent;

class Theme extends BaseEvent
{
	public function syncProduct($from, $data)
	{
		self::broadcastMyGroup($from, function ($fd) use ($data){
			self::push($fd, $data);
		});
	}

	public function syncRank($from, $data)
	{
		self::broadcastMyGroup($from, function ($fd) use ($data){
			self::push($fd, $data);
		});
	}
}