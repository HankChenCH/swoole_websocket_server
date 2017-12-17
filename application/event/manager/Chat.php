<?php

namespace app\event\manager;

use app\event\BaseEvent;
use GHank\WSNotice\SL;

class Chat extends BaseEvent
{
    public static function message($from, $data)
    {
    	self::broadcastMyGroup($from, function ($fd, $userInfo) use ($data){
			if ($data->to_type === '1' && $userInfo['uid'] === $data->to_id) {
	        	self::push($fd, $data);
			} elseif ($data->to_type === '2') {
	        	self::push($fd, $data);
			}
        });
    }
}
 
