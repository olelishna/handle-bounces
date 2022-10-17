<?php

namespace App\Entity\HandleBounced;

use App\Repository\HandleBounced\ComplaintItemRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComplaintItemRepository::class)]
#[ORM\Index(columns: ["complaint_sub_type"], name: "complaint_sub_type_idx")]
class ComplaintItem
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private string $feedback_id;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $complaint_sub_type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $timestamp = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $arrival_date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $complaint_feedback_type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $user_agent = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(referencedColumnName: 'message_id', nullable: false)]
    private ?SuppressedMail $mail = null;

    #[ORM\ManyToOne(inversedBy: 'complaint')]
    #[ORM\JoinColumn(referencedColumnName: 'email', nullable: false)]
    private ?SuppressedClient $recipient = null;

    public function __construct(string $feedbackId)
    {
        $this->feedback_id = $feedbackId;
    }

    public function getFeedbackId(): ?string
    {
        return $this->feedback_id;
    }

    public function getComplaintSubType(): ?string
    {
        return $this->complaint_sub_type;
    }

    public function setComplaintSubType(?string $complaint_sub_type): self
    {
        $this->complaint_sub_type = $complaint_sub_type;

        return $this;
    }

    public function getTimestamp(): ?DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getArrivalDate(): ?DateTimeInterface
    {
        return $this->arrival_date;
    }

    public function setArrivalDate(DateTimeInterface $arrival_date): self
    {
        $this->arrival_date = $arrival_date;

        return $this;
    }

    public function getComplaintFeedbackType(): ?string
    {
        return $this->complaint_feedback_type;
    }

    public function setComplaintFeedbackType(?string $complaint_feedback_type): self
    {
        $this->complaint_feedback_type = $complaint_feedback_type;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function setUserAgent(?string $user_agent): self
    {
        $this->user_agent = $user_agent;

        return $this;
    }

    public function getMail(): ?SuppressedMail
    {
        return $this->mail;
    }

    public function setMail(SuppressedMail $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getRecipient(): ?SuppressedClient
    {
        return $this->recipient;
    }

    public function setRecipient(?SuppressedClient $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }
}
