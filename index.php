<?php

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

use Pimple\Container;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use GuzzleHttp\Client;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

$container = new Container();

$container['logger'] = function($c) {
    $log = new Logger('client');
    $log->pushHandler($c['stream']);
    $log->pushHandler($c['firephp']);
    return $log;
};

$container['formatter'] = function($c) {
    return new MessageFormatter('{req_body} - {res_body}');
};

$container['stream'] = function($c) {
    return new StreamHandler(__DIR__. '/logger.log', Logger::DEBUG);
};

$container['firephp'] = function($c) {
    return new FirePHPHandler();
};

$container['stack'] = function($c) {
    $stack = HandlerStack::create();
    $stack->push(
        Middleware::log(
            $c['logger'],
            $c['formatter']
        )
    );
    return $stack;
};

$container['client'] = function($c) {
    return new Client(
        [
            'base_uri' => 'http://youtube.com/',
            'handler' => $c['stack']
        ]
    );
};

$client = $container['client'];

echo $client->request('GET', 'watch', ['query' => 'v=CSvFpBOe8eY'])->getBody();
$log = $container['logger'];
$log->addInfo('Logger is working!');