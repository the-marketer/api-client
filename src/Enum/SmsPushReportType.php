<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Enum;

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
