<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\DTO\Events;

/**
 * @see ViewHomepageEvent Same field set as homepage view (did, event, url, UA, IP, optional source).
 */
class InitiateCheckoutEvent extends ViewHomepageEvent
{
}
