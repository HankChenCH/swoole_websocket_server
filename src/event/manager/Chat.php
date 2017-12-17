<?php

namespace GHank\WSNotice\event\manager;

class Chat extends BaseHandler
{

    public static function message($from, $data)
    {
	self::broadcast($from, function ($fd) use ($data){
            	$user = static::$userTable->get($fd);
                if ($user && $user['userType'] === self::$eventPrefix) {
                	self::push($fd, $data);    
        	}
        });      
    }
}
 
