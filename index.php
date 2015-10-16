<?php

require_once('vendor/autoload.php');

use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'https://www.youtube.com/']);

$res = $client->request('GET','watch', ['query' => 'v=Eco4z98nIQY']);
echo $res->getBody();