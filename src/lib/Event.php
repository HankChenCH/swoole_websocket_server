<?php

namespace GHank\WSnotice\lib;

use \Closure;
use GHank\WSnotice\SL;

class Event
{
    protected static $eventPrefix;
    protected static $event;

	protected static function broadcast($from, $callback)
	{
		foreach (SL::$app->server->connections as $fd) {
			if ($fd === $from) {
				continue;
			}
            if ($callback instanceof Closure) {
            	$callback($fd);
            }
        }
	}

    protected static function push($to, $data=[])
    {
	    if (is_array($data) && !isset($data['event'])) {
			$data['event'] = static::$event;
	    } 
        
        if (is_object($data) && !isset($data->event)) {
			$data->event = static::$event;
	    }

        SL::$app->server->push($to, json_encode($data));
    }

    public static function registerEvent($prefix, $eventType, $eventName)
    {
       static::$eventPrefix = $prefix;
   	   static::$event = $prefix . '/' . strtolower($eventType) . '/' . $eventName;
    }
}