<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Api/SubscribersApi.php';

use TheMarketer\ApiClient\Client;
use TheMarketer\ApiClient\Exception\ApiException;
use TheMarketer\ApiClient\Exception\CustomerNotFoundException;
use TheMarketer\ApiClient\Exception\MethodNotAllowedException;
use TheMarketer\ApiClient\Exception\UnauthorizedException;
use TheMarketer\ApiClient\Exception\ValidationException;


// REFEREE IQ
$clientReferee = new Client(
    customerId: '699c4b545a11c005db056fbe',
    restKey: '9OQDWYSW',
    apiKey: 'MKRMEPZY'
);

try {
    $data = $clientReferee->getCosts();
    var_dump($data);
} catch (ValidationException $e) {
    $code = $e->getHttpStatusCode(); 
} catch (UnauthorizedException $e) {
    $e->getMessage();
} catch (CustomerNotFoundException $e) {
    $e->getMessage();
} catch (MethodNotAllowedException $e) {
    $e->getMessage();
} catch (ApiException $e) {
    $status = $e->getHttpStatusCode();
    $e->getMessage();
}