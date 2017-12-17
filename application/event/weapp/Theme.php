<?php

namespace app\event;

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