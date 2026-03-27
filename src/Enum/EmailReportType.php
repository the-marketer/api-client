<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Enum;

/**
 * Valori permise pentru parametrul `type` la rapoartele email:
 * `/reports/get-email-campaigns` și `/reports/get-email-automation`.
 */
enum EmailReportType: string
{
    case Sent = 'sent';
    case OpenRate = 'open-rate';
    case UniqueOpenRate = 'unique-open-rate';
    case ClickRate = 'click-rate';
    case UniqueClickRate = 'unique-click-rate';
    case Opens = 'opens';
    case UniqueOpens = 'unique-opens';
    case Clicks = 'clicks';
    case UniqueClicks = 'unique-clicks';
    case Transactions = 'transactions';
    case Revenue = 'revenue';
    case ConversionRate = 'conversion-rate';
    case AverageOrderValue = 'average-order-value';
    case Unsubscribed = 'unsubscribed';
    case Complaints = 'complaints';
    case Bounced = 'bounced';
    case BounceRate = 'bounce-rate';
    case ComplaintRate = 'complaint-rate';
    case UnsubscribeRate = 'unsubscribe-rate';
}
