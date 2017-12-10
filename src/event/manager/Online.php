<?php

namespace GHank\WSNotice\event\manager;

class Online extends BaseHandler
{

    public static function count($from, $data)
    {
        $onlineUidArr = [];
	foreach (self::$server->connections as $fd) {
	    $user = self::$userTable->get($fd);
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
