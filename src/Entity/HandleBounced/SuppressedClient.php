<?php

namespace App\Entity\HandleBounced;

use App\Repository\HandleBounced\SuppressedClientRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SuppressedClientRepository::class)]
#[ORM\Index(columns: ["score"], name: "score_idx")]
#[ORM\HasLifecycleCallbacks]
class SuppressedClient
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(nullable: true)]
    private ?float $score = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updated = null;

    #[ORM\OneToMany(mappedBy: 'recipient', targetEntity: BouncedItem::class, orphanRemoval: true)]
    private Collection $bounced;

    #[ORM\OneToMany(mappedBy: 'recipient', targetEntity: ComplaintItem::class, orphanRemoval: true)]
    private Collection $complaint;

    public function __construct(string $email)
    {
        $this->email = $email;
        $this->bounced = new ArrayCollection();
        $this->complaint = new ArrayCollection();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): self
    {
        $this->score = $score;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedValue(): void
    {
        $this->created = new DateTime();
    }

    public function getCreated(): ?DateTimeInterface
    {
        return $this->created;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedValue(): void
    {
        $this->updated = new DateTime();
    }

    public function getUpdated(): ?DateTimeInterface
    {
        return $this->updated;
    }

    /**
     * @return Collection<int, BouncedItem>
     */
    public function getBounced(): Collection
    {
        return $this->bounced;
    }

    public function addBounced(BouncedItem $bounced): self
    {
        if (!$this->bounced->contains($bounced)) {
            $this->bounced->add($bounced);
            $bounced->setRecipient($this);
        }

        return $this;
    }

    public function removeBounced(BouncedItem $bounced): self
    {
        // set the owning side to null (unless already changed)
        if ($this->bounced->removeElement($bounced) && $bounced->getRecipient() === $this) {
            $bounced->setRecipient(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, ComplaintItem>
     */
    public function getComplaint(): Collection
    {
        return $this->complaint;
    }

    public function addComplaint(ComplaintItem $complaint): self
    {
        if (!$this->complaint->contains($complaint)) {
            $this->complaint->add($complaint);
            $complaint->setRecipient($this);
        }

        return $this;
    }

    public function removeComplaint(ComplaintItem $complaint): self
    {
        // set the owning side to null (unless already changed)
        if ($this->complaint->removeElement($complaint) && $complaint->getRecipient() === $this) {
            $complaint->setRecipient(null);
        }

        return $this;
    }
}
