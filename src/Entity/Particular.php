<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Particular extends User
{
    public const ROLE = "particular";

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $firstName = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $lastName = null;

    public function getRoles(): array
    {
        return ['ROLE_PARTICULAR'];
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }
}
