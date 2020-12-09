<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"particular"="Particular", "company"="Company"})
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Email déjà utlisé"
 * )
 */
abstract class User implements UserInterface
{
    public const TOKEN_VALIDITY = "-4 hours";

    public static array $roles = [Company::ROLE, Particular::ROLE];

    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    protected string $email = "";

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    protected string $password = "";

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $confirmationToken = null;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    protected bool $isVerified = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $resetPasswordToken = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $accessToken;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $refreshToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private \DateTime $expirationDate;

    public function isCompany(): bool
    {
        return $this instanceof Company;
    }

    public function isParticular(): bool
    {
        return $this instanceof Particular;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    abstract public function getRoles(): array;

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): void {}

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void {}

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): self
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getExpirationDate(): \DateTime
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTime $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function tokenIsValid(\DateTime $dateTime): bool
    {
        return $this->expirationDate > $dateTime;
    }
}
