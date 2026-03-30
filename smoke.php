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
    '457R4326'
);

try {
    $data = $clientReferee->subscribers()->statusSubscriber('alexandru.clain@themarketer.com');
    var_dump($data);
} catch (ValidationException $e) {
    $message = $e->getMessage();
} catch (UnauthorizedException $e) {
    $message = $e->getMessage();
} catch (CustomerNotFoundException $e) {
    $message = $e->getMessage();
} catch (MethodNotAllowedException $e) {
    $message = $e->getMessage();
} catch (ApiException $e) {
    $message = $e->getMessage();
} catch (GuzzleException $e) {
    $message = $e->getMessage();
}

var_dump($message);