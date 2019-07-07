<?php
require_once 'app/controller/index.php';

use App\Controller\Posters;

/**
 * 使用socket创建一个简单的http服务器
 */
class HttpServer
{
    protected $port;
    protected $addr;
    protected $socket;

    public function __construct($addr = 'localhost', $port = 9501)
    {
        $this->port = $port;
        $this->addr = $addr;
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!$this->socket) {
            throw new Exception('Server created failed!');
        }
        socket_bind($this->socket, $this->addr, $this->port);
    }

    public function run()
    {
        socket_listen($this->socket);
        echo 'Server stared at ' . $this->addr . ':' . $this->port;
        while (true) {
            $msg_socket = socket_accept($this->socket);
            socket_write($msg_socket, $this->response());
            socket_close($msg_socket);
        }
    }

    public function response()
    {
        $content = (new Posters)->getPoster();
		$text = 'HTTP/1.0 200 OK' . "\r\n";
		$text .= 'Content-Type: image/jpg' . "\r\n";
		$text .= 'Content-Length: ' . strlen($content) . "\r\n";

		$text .= "\r\n";

		$text .= $content;
		return $text;
    }

    public function __destruct()
    {
        socket_close($this->socket);
    }
}

$server = (new HttpServer)->run();
