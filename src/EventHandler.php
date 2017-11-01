<?php

namespace GHank\WSNotice;

use \Closure;

class EventHandler
{
	protected static $_wsserv;
	protected static $server;
	protected static $userTable;

	public function __construct(WebsocketServer $server)
	{
		static::$_wsserv = $server;
		static::$server = $server::$server;
		static::$userTable = $server::$userTable;
	}

	public static function login($from, $data)
	{
		static::broadcast($from, $data);
	}

	public static function payNotice($from, $data)
	{
		static::broadcast($from, $data);
	}

	public static function onlineCount($from, $data)
	{
		$onlineUidArr = [];
		$event = humpToLine(__FUNCTION__);

		foreach (static::$server->connections as $fd) {
			$user = static::$userTable->get($fd);
			if ($user && $user['userType'] === $data->userType) {
				$onlineUidArr[] = $user['uid'];
			}
        }

        $onlineUidArr = array_unique($onlineUidArr);

        static::$server->push($from, json_encode([
        	'onlineMembers' => $onlineUidArr, 
        	'onlineCount' => count($onlineUidArr), 
        	'event' => $event,
        ]));
	}

	public static function onlineNotice($from, $userInfo)
	{
		$event = humpToLine(__FUNCTION__);
        static::broadcast($from, function ($fd) use ($userInfo, $event) {
        	$user = static::$userTable->get($fd);
			if ($user && $user['userType'] === $userInfo->aud) {
				static::$server->push($fd, json_encode([
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
    	$fromUser = static::$userTable->get($from);
        static::broadcast($from, function ($fd) use ($fromUser, $event){
			$user = static::$userTable->get($fd);
			if ($user && $user['userType'] === $fromUser['userType']) {
				static::$server->push($fd, json_encode([
					'uid' => $fromUser['uid'],
					'userType' => $fromUser['userType'],
					'event' => $event,
				]));
			}
        });
	}

	private static function broadcast($from, $callback)
	{
		foreach (static::$server->connections as $fd) {
			if ($fd === $from) {
				continue;
			}
            if ($callback instanceof Closure) {
            	$callback($fd);
            }
        }
	}
}