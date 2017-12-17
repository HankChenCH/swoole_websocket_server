<?php

namespace app\event;

use \Closure;
use GHank\WSNotice\SL;
use GHank\WSNotice\lib\Event;

class BaseEvent extends Event
{
	public static function onlineNotice($from, $userInfo)
	{
		$event = humpToLine(__FUNCTION__);
        self::broadcast($from, function ($fd) use ($userInfo, $event) {
        	$user = SL::$app->userTable->get($fd);
			if ($user && $user['userType'] === $userInfo->aud) {
				self::push($fd, json_encode([
					'uid' => $userInfo->user->uid,
					'userType' => $userInfo->aud,
					'event' => $event,
				]));
			}
        });
	}

	public static function offlineNotice($from)
	{
		$event = humpToLine(__FUNCTION__);
    	$fromUser = SL::$app->userTable->get($from);
        self::broadcast($from, function ($fd) use ($fromUser, $event){
			$user = SL::$app->userTable->get($fd);
			if ($user && $user['userType'] === $fromUser['userType']) {
				self::push($fd, json_encode([
					'uid' => $fromUser['uid'],
					'userType' => $fromUser['userType'],
					'event' => $event,
				]));
			}
        });
	}

	protected static function broadcastToGroup($group, $from, $callback)
	{
        self::broadcast($from, function ($fd) use ($group, $callback) {
			$user = SL::$app->userTable->get($fd);
		    if ($user && $user['userType'] === $group && $callback instanceof Closure) {
		    	$callback($fd);
		    }
        });
	}

	protected static function broadcastMyGroup($from, $callback)
	{
        self::broadcast($from, function ($fd) use ($callback) {
			$user = SL::$app->userTable->get($fd);
		    if ($user && $user['userType'] === self::$eventPrefix && $callback instanceof Closure) {
		    	$callback($fd, $user);
		    }
        });
	}
}