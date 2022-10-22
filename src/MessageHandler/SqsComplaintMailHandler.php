<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\HandleBounced\ComplaintItem;
use App\Message\LogComplaintMail;
use App\Repository\HandleBounced\ComplaintItemRepository;
use App\Repository\HandleBounced\SuppressedClientRepository;
use App\Repository\HandleBounced\SuppressedMailRepository;
use App\Service\HandleBounced\Appraiser;
use DateTime;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SqsComplaintMailHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly SuppressedClientRepository $clientRepository,
        private readonly SuppressedMailRepository $mailRepository,
        private readonly ComplaintItemRepository $complaintItemRepository,
        private readonly Appraiser $appraiser
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(LogComplaintMail $data)
    {
        $mail = $data->getMail();
        $client = $data->getClient();
        $complaint = $this->setComplaint($data->getComplaintData());

        $score = $this->appraiser->assess($client);
        $client->setScore($score);

        $complaint
            ->setMail($mail)
            ->setRecipient($client);

        $this->mailRepository->save($mail);
        $this->clientRepository->save($client);
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
            ->setArrivalDate(new DateTime($data['arrivalDate']));

        return $complaint;
    }
}