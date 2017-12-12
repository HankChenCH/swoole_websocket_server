<?php

namespace GHank\WSNotice\event\weapp;

class Pay extends BaseHandler
{
    public static function notice($from, $data)
    {
	self::broadcast($from, function ($fd) use ($data) {
	 	self::push($fd, ['data' => $data]);
	});
    }
}
