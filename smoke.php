<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Api/SubscribersApi.php';

use GuzzleHttp\Exception\GuzzleException;
use TheMarketer\ApiClient\Client;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;


// REFEREE IQ
$clientReferee = new Client(
    '62b969c6d0385a1b694ecfb9',
    '8784T542'
);

try {
    $data = $clientReferee->subscribers()->deleteSubscriber(['email' => 'radu.dalbea@themarketer.com', 'id' => 2]);
    var_dump($data);
    exit();
} catch (Exception $e) {
    var_dump($e);
}

