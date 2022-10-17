<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\HandleBounced\SuppressedClient;
use App\Entity\HandleBounced\SuppressedMail;

class LogBouncedMail
{
    public function __construct(
        private readonly array $bounced_data,
        private readonly SuppressedClient $client,
        private readonly SuppressedMail $mail)
    {
    }

    public function getBouncedData(): array
    {
        return $this->bounced_data;
    }

    public function getClient(): SuppressedClient
    {
        return $this->client;
    }

    public function getMail(): SuppressedMail
    {
        return $this->mail;
    }
}