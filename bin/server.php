<?php
// Composer
require(dirname(__FILE__) . '/../vendor/autoload.php');

use Ratchet\App;
use Ratchet\Http\HttpServerInterface;
use Ratchet\ConnectionInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

class HttpServer implements HttpServerInterface {
	public function onOpen(ConnectionInterface $from, RequestInterface $request = null) {
		$requestPath = $request->getPath();

		$output = 'Hello from Ratchet!';

		$resp = new Response(200, array(
			'Content-Type' => 'text/html',
			'Content-Length' => strlen($output),
			'Connection' => 'close'
		), $output);

		$from->send((string)$resp);
		$from->close();
	}

	public function onMessage(ConnectionInterface $from, $msg) {}
	public function onError(ConnectionInterface $from, Exception $e) {}
	public function onClose(ConnectionInterface $from) {}
}

set_time_limit(0); //No time limit

$host = 'localhost';
$port = 9000;

// Parse arguments if run in CLI
$sapiType = php_sapi_name();
if (substr($sapiType, 0, 3) == 'cli') {
	$options = getopt('p:f', array('port:'));

	if (isset($options['p']) || isset($options['port'])) {
		$port = (isset($options['p'])) ? $options['p'] : $options['port'];
	}
}

echo 'Starting Ratchet server at '.$host.':'.$port.'...'."\n";

$httpServer = new HttpServer;
$app = new App($host, $port, '0.0.0.0');
$app->route('/', $httpServer, array('*'), $host);

$app->run(); //Start the server