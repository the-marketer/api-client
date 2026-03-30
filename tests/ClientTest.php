<?php

declare(strict_types=1);

namespace Tests;

use TheMarketer\ApiClient\Client as ApiClient;
use TheMarketer\ApiClient\Common\Config;

final class ClientTest extends TestCase
{
    public function testExposesTrackingKeyAsApiKey(): void
    {
        $api = new ApiClient(
            new Config('cid', 'rest'),
            apiKey: 'track-xyz',
        );

        $this->assertSame('track-xyz', $api->trackingKey());
    }
}
