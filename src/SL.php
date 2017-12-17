<?php

namespace GHank\WSNotice;

class SL
{
	public static $app;

	public function __construct(WebsocketServer $server)
	{
		static::$app = new \StdClass();
		static::$app->server = $server::$server;
		static::$app->userTable = $server::$userTable;
	}

	public static function inject($name, $object)
	{
		static::$app->$name = $object;
	}
}