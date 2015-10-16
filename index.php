<?php

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

use Pimple\Container;
use Monolog\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

$container = new Container();

$container['logger'] = function($c) {
    return new Logger('client');
};

$container['formatter'] = function($c) {
    return new MessageFormatter('{req_body} - {res_body}');
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