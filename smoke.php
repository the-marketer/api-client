<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use TheMarketer\ApiClient\Client;

$clientTest = new Client([
    'customerId' => '65eed706bb527bd25c09ea97',
    'restKey' => 'MGCAQVNY',
]);

$clientReferee2 = new Client([
    'customerId' => '69cd09e87e1f7708da06ba09',
    'restKey' => 'UIWVQYFX',
]);

$clientNamos = new Client([
    'customerId' => '62b969c6d0385a1b694ecfb9',
    'restKey' => '8784T542',
]);

try {
    $response = $clientNamos->events()->search(
        [
            "k" => '457R4326',
            'did' => 'device-abc',
            'event' => 'search',
            'search_term' => 'view_product',
            'url' => 'https://example.com/search?q=running+shoes',
            'http_user_agent' => 'Mozilla/5.0 (compatible)',
            'remote_addr' => '203.0.113.1',
        ]
    );
    var_dump($response);
} catch (Throwable $e) {
    fwrite(STDERR, get_class($e) . ': ' . $e->getMessage() . "\n");
    throw $e;
}
