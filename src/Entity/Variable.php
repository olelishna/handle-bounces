<?php

namespace App\Entity;

use App\Repository\VariableRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VariableRepository::class)]
class Variable
{
    #[ORM\Id]
    #[ORM\Column(length: 128)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::BLOB)]
    #[Assert\NotBlank]
    private $value;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): mixed
    {
        if ($this->value) {
            rewind($this->value);

            return unserialize(stream_get_contents($this->value), ['allowed_classes' => true]);
        }

        return null;
    }

    public function setValue($value): self
    {
        $this->value = serialize($value);

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
