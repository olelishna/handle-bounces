<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\HandleBounced\BouncedItem;
use App\Message\LogBouncedMail;
use App\Repository\HandleBounced\BouncedItemRepository;
use App\Repository\HandleBounced\SuppressedClientRepository;
use App\Repository\HandleBounced\SuppressedMailRepository;
use App\Service\HandleBounced\Appraiser;
use DateTime;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SqsBouncedMailHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly SuppressedClientRepository $clientRepository,
        private readonly SuppressedMailRepository $mailRepository,
        private readonly BouncedItemRepository $bouncedItemRepository,
        private readonly Appraiser $appraiser
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(LogBouncedMail $data)
    {
        $mail = $data->getMail();
        $client = $data->getClient();
        $bounced = $this->setBounced($data->getBouncedData());

        $score = $this->appraiser->assess($client);
        $client->setScore($score);

        $bounced
            ->setMail($mail)
            ->setRecipient($client);

        $this->mailRepository->save($mail);
        $this->clientRepository->save($client);
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
            ->setReportingMTA($data['reportingMTA'] ?? null);

        return $bounce;
    }
}