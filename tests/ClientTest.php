<?php

declare(strict_types=1);

namespace Tests;

use TheMarketer\ApiClient\Client as ApiClient;

final class ClientTest extends TestCase
{
    public function testExposesTrackingKeyAsApiKey(): void
    {
        $api = new ApiClient(
            customerId: 'cid',
            restKey: 'rest',
            apiKey: 'track-xyz',
        );

        $this->assertSame('track-xyz', $api->trackingKey());
    }
}
