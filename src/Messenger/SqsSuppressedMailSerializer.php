<?php

declare(strict_types=1);

namespace App\Messenger;

use App\Entity\HandleBounced\SuppressedClient;
use App\Entity\HandleBounced\SuppressedMail;
use App\Message\LogBouncedMail;
use App\Message\LogComplaintMail;
use App\Repository\HandleBounced\SuppressedClientRepository;
use DateTime;
use Exception;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

class SqsSuppressedMailSerializer implements SerializerInterface
{
    public function __construct(
        private readonly SuppressedClientRepository $clientRepository
    ) {
    }

    /**
     * @throws Exception|MessageDecodingFailedException
     */
    public function decode(array $encodedEnvelope): Envelope
    {
        $body = $encodedEnvelope['body'];

        $data = json_decode($body, true);

        if (null === $data) {
            throw new MessageDecodingFailedException('Invalid JSON.');
        }

        if (!isset($data['notificationType'])) {
            throw new MessageDecodingFailedException('Missing the type.');
        }

        if (!isset($data['mail'])) {
            throw new MessageDecodingFailedException('Missing the mail section.');
        }

        $message = $this->createMessage($data);

        return new Envelope($message);
    }

    /**
     * @throws Exception
     */
    public function encode(Envelope $envelope): array
    {
        throw new Exception('Transport & serializer not meant for sending messages');
    }

    private function createMessage(array $data): LogComplaintMail|LogBouncedMail
    {
        $mail = $this->createMail($data['mail']);
        $recipient = $this->createRecipient($mail->getDestination());

        return match ($data['notificationType']) {
            'Bounce' => new LogBouncedMail($data['bounce'], $recipient, $mail),
            'Complaint' => new LogComplaintMail($data['complaint'], $recipient, $mail),
            default => throw new MessageDecodingFailedException('Unknown type : '.$data['notificationType'].'!'),
        };
    }

    private function createMail(array $data): SuppressedMail
    {
        if (!isset(
            $data['messageId'], $data['source'], $data['destination'],
            $data['commonHeaders']['subject'], $data['timestamp']
        )) {
            throw new MessageDecodingFailedException('Missing the mail section data.');
        }

        try {
            $timestamp = new DateTime($data['timestamp']);
        } catch (Exception) {
            throw new MessageDecodingFailedException(
                'Something is wrong with the mail timestamp: '.$data['timestamp'].'!'
            );
        }

        $mail = new SuppressedMail($data['messageId']);
        $mail->setSource($data['source'])
            ->setDestination($data['destination'][0])
            ->setSubject($data['commonHeaders']['subject'])
            ->setTimestamp($timestamp);

        return $mail;
    }

    private function createRecipient(string $user_email): SuppressedClient
    {
        $user_email = mb_strtolower($user_email);

        $user = $this->clientRepository->find($user_email);

        if ($user === null) {
            $user = new SuppressedClient($user_email);
        }

        return $user;
    }
}