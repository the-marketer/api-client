<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Enum;

/**
 * Valori permise pentru `type` la `/reports/get-forms-popups` și `/reports/get-forms-embedded`.
 */
enum FormsReportType: string
{
    case TotalImpressions = 'total-impressions';
    case TotalSubscribedUsers = 'total-subscribed-users';
    case TotalSubscribeRate = 'total-subscribe-rate';
    case Impressions = 'impressions';
    case SubscribedUsers = 'subscribed-users';
    case SubscribeRate = 'subscribe-rate';
}
