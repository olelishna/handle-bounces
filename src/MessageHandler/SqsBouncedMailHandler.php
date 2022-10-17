<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\HandleBounced\BouncedItem;
use App\Entity\HandleBounced\SuppressedClient;
use App\Entity\HandleBounced\SuppressedMail;
use App\Message\LogBouncedMail;
use App\Repository\HandleBounced\BouncedItemRepository;
use App\Repository\HandleBounced\SuppressedClientRepository;
use App\Repository\HandleBounced\SuppressedMailRepository;
use DateTime;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SqsBouncedMailHandler implements MessageHandlerInterface
{
    private SuppressedClient $client;
    private SuppressedMail $mail;

    public function __construct(
        private readonly SuppressedClientRepository $clientRepository,
        private readonly SuppressedMailRepository $mailRepository,
        private readonly BouncedItemRepository $bouncedItemRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(LogBouncedMail $data)
    {
        $this->mail = $data->getMail();
        $this->client = $data->getClient();

        /** TODO: Add calc for score! */
        $this->client->setScore(0);
        $this->client->setUpdatedValue();

        $bounced = $this->setBounced($data->getBouncedData());

        $this->mailRepository->save($this->mail);
        $this->clientRepository->save($this->client);
        $this->bouncedItemRepository->save($bounced, true);
    }

    /**
     * @throws Exception
     */
    private function setBounced(array $data): BouncedItem
    {
        $bounce = new BouncedItem($data['feedbackId']);
        $bounce->setBounceType($data['bounceType'])
            ->setBounceSubType($data['bounceSubType'])
            ->setAction($data['bouncedRecipients'][0]['action'] ?? null)
            ->setStatus($data['bouncedRecipients'][0]['status'] ?? null)
            ->setDiagnosticCode($data['bouncedRecipients'][0]['diagnosticCode'] ?? null)
            ->setTimestamp(new DateTime($data['timestamp']))
            ->setReportingMTA($data['reportingMTA'] ?? null)
            ->setMail($this->mail)
            ->setRecipient($this->client);

        return $bounce;
    }
}