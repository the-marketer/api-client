<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use TheMarketer\ApiClient\Client;

$clientTest = new Client(
    '65eed706bb527bd25c09ea97',
    'MGCAQVNY'
);

try {
    $response = $clientTest->checkCredentials("PGBIQUKO");
    var_dump($response);
} catch (Throwable $e) {
    fwrite(STDERR, get_class($e) . ': ' . $e->getMessage() . "\n");
    throw $e;
}
