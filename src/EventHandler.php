<?php

namespace GHank\WSNotice;

use \Closure;

class EventHandler
{
	protected static $_wsserv;
	protected static $server;
	protected static $userTable;

        protected static $eventPrefix;
        protected static $event;

	public function __construct(WebsocketServer $server)
	{
		static::$_wsserv = $server;
		static::$server = $server::$server;
		static::$userTable = $server::$userTable;
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

        public static function callEventHandler($frame, $fromData)
        {
            $eventArr = explode('/',$fromData->event);

            if (count($eventArr) > 2) {
                $actionName = array_pop($eventArr);
                $controllerName = ucfirst(array_pop($eventArr));
                $classPrefix = implode('\\',$eventArr);
                $eventClassName = '\\GHank\\WSNotice\\event\\' . $classPrefix . '\\' . $controllerName;

                if(class_exists($eventClassName) && method_exists($eventClassName, $actionName)) {
                    self::registerEvent($classPrefix, $controllerName, $actionName);
                    $eventClassName::$actionName($frame->fd, $fromData);
                }
            }

        }

	protected static function broadcast($from, $callback)
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

        protected static function push($from, $data)
        {
	    $data['event'] = static::$event;
            static::$server->push($from, json_encode($data));
        }

        protected static function registerEvent($prefix, $eventType, $eventName)
        {
           static::$eventPrefix = $prefix;
	   static::$event = $prefix . '/' . strtolower($eventType) . '/' . $eventName;
        }
}
