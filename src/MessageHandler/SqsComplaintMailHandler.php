<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\HandleBounced\ComplaintItem;
use App\Entity\HandleBounced\SuppressedClient;
use App\Entity\HandleBounced\SuppressedMail;
use App\Message\LogComplaintMail;
use App\Repository\HandleBounced\ComplaintItemRepository;
use App\Repository\HandleBounced\SuppressedClientRepository;
use App\Repository\HandleBounced\SuppressedMailRepository;
use DateTime;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SqsComplaintMailHandler implements MessageHandlerInterface
{
    private SuppressedClient $client;
    private SuppressedMail $mail;

    public function __construct(
        private readonly SuppressedClientRepository $clientRepository,
        private readonly SuppressedMailRepository $mailRepository,
        private readonly ComplaintItemRepository $complaintItemRepository
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(LogComplaintMail $data)
    {
        $this->mail = $data->getMail();
        $this->client = $data->getClient();

        /** TODO: Add calc for score! */
        $this->client->setScore(0);
        $this->client->setUpdatedValue();

        $complaint = $this->setComplaint($data->getComplaintData());

        $this->mailRepository->save($this->mail);
        $this->clientRepository->save($this->client);
        $this->complaintItemRepository->save($complaint, true);
    }

    /**
     * @throws Exception
     */
    private function setComplaint(array $data): ComplaintItem
    {
        $complaint = new ComplaintItem($data['feedbackId']);
        $complaint->setComplaintSubType($data['complaintSubType'])
            ->setComplaintFeedbackType($data['complaintFeedbackType'] ?? null)
            ->setTimestamp(new DateTime($data['timestamp']))
            ->setUserAgent($data['userAgent'] ?? null)
            ->setArrivalDate(new DateTime($data['arrivalDate']))
            ->setMail($this->mail)
            ->setRecipient($this->client);

        return $complaint;
    }
}