<?php

declare(strict_types=1);

namespace TheMarketer\ApiClient\Laravel\Mail;

use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TheMarketer\ApiClient\Client;

class TheMarketerTransport extends AbstractTransport
{
    private readonly ?string $defaultFromAddress;

    private readonly ?string $defaultReplyTo;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly Client $client,
        array $config = [],
    ) {
        parent::__construct();
        $this->defaultFromAddress = $this->extractAddress($config['from'] ?? null);
        $this->defaultReplyTo = $this->extractAddress($config['reply_to'] ?? null);
    }

    public function __toString(): string
    {
        return 'themarketer';
    }

    /**
     * @param SentMessage $message
     * @return void
     * @throws GuzzleException
     * @throws \JsonException
     */
    protected function doSend(SentMessage $message): void
    {
        $original = $message->getOriginalMessage();
        if (!$original instanceof Email) {
            throw new TransportException('TheMarketer transport only supports Symfony Email messages.');
        }

        $recipients = $this->collectRecipients($original);
        if ($recipients === []) {
            throw new TransportException('TheMarketer transport requires at least one recipient.');
        }

        $subject = (string) ($original->getSubject() ?? '');
        $body = $this->resolveBody($original);

        $emails = [];
        foreach ($recipients as $recipient) {
            $payload = [
                'to' => $recipient,
                'subject' => $subject,
                'body' => $body,
            ];

            $from = $this->resolveFrom($original);
            if ($from !== null) {
                $payload['from'] = $from;
            }

            $replyTo = $this->resolveReplyTo($original);
            if ($replyTo !== null) {
                $payload['reply_to'] = $replyTo;
            }

            $attachments = $this->extractAttachments($original);
            if ($attachments !== []) {
                $payload['attachments'] = $attachments;
            }

            $emails[] = $payload;
        }

        $transactionals = $this->client->transactionals();
        if (count($emails) === 1) {
            $transactionals->sendEmail($emails[0]);

            return;
        }

        $transactionals->sendEmailsBulk(['emails' => $emails]);
    }

    private function resolveBody(Email $email): string
    {
        $html = $email->getHtmlBody();
        if ($html !== null && $html !== '') {
            return $html;
        }

        return (string) ($email->getTextBody() ?? '');
    }

    /**
     * @return list<string>
     */
    private function collectRecipients(Email $email): array
    {
        $addresses = array_merge(
            $email->getTo(),
            $email->getCc(),
            $email->getBcc(),
        );

        $unique = [];
        foreach ($addresses as $address) {
            if (!$address instanceof Address) {
                continue;
            }
            $unique[$address->getAddress()] = true;
        }

        return array_keys($unique);
    }

    private function resolveFrom(Email $email): ?string
    {
        $from = $email->getFrom();
        if ($from !== []) {
            return $from[0]->getAddress();
        }

        return $this->defaultFromAddress;
    }

    private function resolveReplyTo(Email $email): ?string
    {
        $replyTo = $email->getReplyTo();
        if ($replyTo !== []) {
            return $replyTo[0]->getAddress();
        }

        return $this->defaultReplyTo;
    }

    /**
     * @param Email $email
     * @return array
     */
    private function extractAttachments(Email $email): array
    {
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $name = method_exists($attachment, 'getFilename') ? (string) ($attachment->getFilename() ?? 'attachment.bin') : 'attachment.bin';

            $body = '';
            if (method_exists($attachment, 'bodyToString')) {
                $body = (string) $attachment->bodyToString();
            } elseif (method_exists($attachment, 'getBody')) {
                $body = (string) $attachment->getBody();
            }

            $attachments[] = [
                'name' => $name,
                'content' => base64_encode($body),
            ];
        }

        return $attachments;
    }

    private function extractAddress(mixed $value): ?string
    {
        if (is_string($value)) {
            $trimmed = trim($value);

            return $trimmed === '' ? null : $trimmed;
        }

        if (!is_array($value)) {
            return null;
        }

        $address = $value['address'] ?? null;
        if (!is_string($address)) {
            return null;
        }

        $trimmed = trim($address);

        return $trimmed === '' ? null : $trimmed;
    }
}
