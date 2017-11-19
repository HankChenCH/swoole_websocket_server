<?php

namespace GHank\WSNotice;

use \swoole_websocket_server;
use \swoole_table;
use \Firebase\JWT\JWT;

class WebsocketServer
{
	public static $server;

	public static $eventHandler;

	public static $userTable;

	public function __construct() 
    {
        static::$server = new swoole_websocket_server("0.0.0.0", 9502);
        static::$userTable = new swoole_table(1024);
        static::$eventHandler = new EventHandler($this);

        static::$server->set(config('server'));

        static::$server->on('open', [$this, 'handleOpen']);

        static::$server->on('message', [$this, 'handleMsg']);

        static::$server->on('close', [$this, 'handleClose']);

        static::$userTable->column('fd', swoole_table::TYPE_INT, 8);
        static::$userTable->column('userType', swoole_table::TYPE_STRING, 256);
        static::$userTable->column('uid', swoole_table::TYPE_INT, 8);
        static::$userTable->create();
    }

    public static function handleOpen($server, $request)
    {
        // 接受请求携带的jwt票据信息，如果不存在，拒绝链接
    	if (!isset($request->get) || !isset($request->get['token'])) {
    		static::$server->close($request->fd);
    		return false;
    	}

        // 尝试解析jwt票据信息，解析出错，代表用户身份有误，拒绝链接
        try{
            $decoded = JWT::decode($request->get['token'], md5(config('secure.token_salt') . getClientIp($request)), array('HS256'));
        } catch (\Exception $e) {
            static::handleClose($request->fd, $e->getMessage());
            return false;
        }

        static::$userTable->set($request->fd, ["fd" => $request->fd, "userType" => $decoded->aud, "uid" => $decoded->user->uid]);

        static::$eventHandler::onlineNotice($request->fd, $decoded);
    }

    public static function handleMsg($server, $frame)
    {
    	$fromData = json_decode($frame->data);
        if (!$fromData || !is_object($fromData) || empty($fromData->event)) {
    		echo "message data error:invalid event or miss\n";
            $this->handleClose($frame->fd, 'data format invalidate.');
            return false;
        }

    	$event = convertObliqueLine($fromData->event);

        var_dump($event);

        //  将发送信息的事件映射到事件处理器中，处理器存在该事件，绑定为事件驱动
    	if ($event && method_exists(static::$eventHandler, $event)) {
    		static::$eventHandler::$event($frame->fd, $fromData);
    	}
    }

    /**
     * @param $fd
     * @param $message
     * 关闭$fd的连接，并删除该用户的映射
     */
    public static function handleClose($ser, $fd)
    {
        static::$eventHandler::offlineNotice($fd);

        // 删除映射关系
        if (static::$userTable->exist($fd)) {
            static::$userTable->del($fd);
        }
        
        // 关闭连接
        static::$server->close(intval($fd));
    }

    public function run()
    {
        static::$server->start();
    }
}
