<?php

namespace App\Entity\HandleBounced;

use App\Repository\HandleBounced\BouncedItemRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BouncedItemRepository::class)]
#[ORM\Index(columns: ["bounce_type"], name: "bounce_type_idx")]
#[ORM\Index(columns: ["bounce_sub_type"], name: "bounce_sub_type_idx")]
class BouncedItem
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private string $feedback_id;

    #[ORM\Column(length: 255)]
    private ?string $bounce_type = null;

    #[ORM\Column(length: 255)]
    private ?string $bounce_sub_type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $action = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $diagnostic_code = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $timestamp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reporting_mta = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(referencedColumnName: 'message_id', nullable: false)]
    private ?SuppressedMail $mail = null;

    #[ORM\ManyToOne(inversedBy: 'bounced')]
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

    public function getBounceType(): ?string
    {
        return $this->bounce_type;
    }

    public function setBounceType(string $bounceType): self
    {
        $this->bounce_type = $bounceType;

        return $this;
    }

    public function getBounceSubType(): ?string
    {
        return $this->bounce_sub_type;
    }

    public function setBounceSubType(string $bounceSubType): self
    {
        $this->bounce_sub_type = $bounceSubType;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDiagnosticCode(): ?string
    {
        return $this->diagnostic_code;
    }

    public function setDiagnosticCode(?string $diagnosticCode): self
    {
        $this->diagnostic_code = $diagnosticCode;

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

    public function getReportingMTA(): ?string
    {
        return $this->reporting_mta;
    }

    public function setReportingMTA(?string $reportingMTA): self
    {
        $this->reporting_mta = $reportingMTA;

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
