<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Enum;

/**
 * Valori permise pentru `type` la rapoartele SMS și push (aceleași metrici):
 * `/reports/get-sms-campaigns`, `/reports/get-sms-automation`,
 * `/reports/get-push-campaigns`, `/reports/get-push-automation`.
 */
enum SmsPushReportType: string
{
    case Sent = 'sent';
    case ClickRate = 'click-rate';
    case UniqueClickRate = 'unique-click-rate';
    case Clicks = 'clicks';
    case UniqueClicks = 'unique-clicks';
    case Transactions = 'transactions';
    case Revenue = 'revenue';
    case ConversionRate = 'conversion-rate';
    case AverageOrderValue = 'average-order-value';
    case Unsubscribed = 'unsubscribed';
    case UnsubscribedRate = 'unsubscribed-rate';
}
