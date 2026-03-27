<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Enum;

/**
 * Valori permise pentru parametrul `type` la `/reports/get-audience`.
 */
enum AudienceReportType: string
{
    case TotalSubscribedEmails = 'total-subscribed-emails';
    case TotalSubscribedSms = 'total-subscribed-sms';
    case TotalSubscribedPush = 'total-subscribed-push';
    case TotalSubscribedLoyalty = 'total-subscribed-loyalty';
    case TotalUnsubscribedEmails = 'total-unsubscribed-emails';
    case TotalUnsubscribedSms = 'total-unsubscribed-sms';
    case TotalUnsubscribedPush = 'total-unsubscribed-push';
    case TotalUnsubscribedLoyalty = 'total-unsubscribed-loyalty';
    case TotalActiveEmails = 'total-active-emails';
    case TotalInactiveEmails = 'total-inactive-emails';
    case TotalCleanedEmails = 'total-cleaned-emails';
    case TotalBouncedEmails = 'total-bounced-emails';
    case SubscribedEmails = 'subscribed-emails';
    case SubscribedSms = 'subscribed-sms';
    case SubscribedPush = 'subscribed-push';
    case SubscribedLoyalty = 'subscribed-loyalty';
}
