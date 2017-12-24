<?php

namespace app\event;

use \Closure;
use GHank\WSNotice\SL;
use GHank\WSNotice\lib\Event;

class BaseEvent extends Event
{
	public static function onlineNotice($from, $userInfo)
	{
		// var_dump($userInfo);
        self::broadcast($from, function ($fd) use ($userInfo) {
        	$user = SL::$app->userTable->get($fd);
			if ($user && $user['userType'] === $userInfo->aud) {
				// var_dump($user);
				self::push($fd, json_encode([
					'uid' => $userInfo->user->uid,
					'userType' => $userInfo->aud,
					'event' => 'online/notice',
				]));
			}
        });
	}

	public static function offlineNotice($from)
	{
    	$fromUser = SL::$app->userTable->get($from);
        self::broadcast($from, function ($fd) use ($fromUser){
			$user = SL::$app->userTable->get($fd);
			if ($user && $user['userType'] === $fromUser['userType']) {
				self::push($fd, json_encode([
					'uid' => $fromUser['uid'],
					'userType' => $fromUser['userType'],
					'event' => 'offline/notice',
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