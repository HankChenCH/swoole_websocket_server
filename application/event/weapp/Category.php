<?php

namespace app\event;

class Category extends BaseEvent
{
	public function syncProduct($from, $data)
	{
		self::broadcastMyGroup($from, function ($fd) use ($data){
			self::push($fd, $data);
		});
	}
}