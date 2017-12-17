<?php

namespace app\event\manager;

use GHank\WSNotice\SL;
use app\event\BaseEvent;

class Online extends BaseEvent
{

    public static function count($from, $data)
    {
        $onlineUidArr = [];
    	foreach (SL::$app->server->connections as $fd) {
    	    $user = SL::$app->userTable->get($fd);
    	    if ($user && $user['userType'] === self::$eventPrefix) {
    	        $onlineUidArr[] = $user['uid'];
    	    }
        }

        $onlineUidArr = array_unique($onlineUidArr);

    	$data = [
    	    'onlineMembers' => $onlineUidArr,
            'onlineCount' => count($onlineUidArr),
        ];

    	if (is_null($from)) {
            self::broadcast($from, function ($fd) use ($data){
                self::push($fd, $data);
            }); 
        } else {
            self::push($from, $data);
        }
    }
}
