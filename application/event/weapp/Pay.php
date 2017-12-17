<?php

namespace app\event\weapp;

use app\event\BaseEvent;

class Pay extends BaseEvent
{
    public static function notice($from, $data)
    {
		self::broadcastToGroup('manager', $from, function ($fd) use ($data) {
		 	self::push($fd, ['data' => $data]);
		});
    }
}
