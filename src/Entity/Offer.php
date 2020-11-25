<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\OfferRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OfferRepository::class)
 */
class Offer
{
    public static array $contractTypes = ['CDD', 'CDI', 'Stage', 'Alternance'];
    public static array $activities = ['IT', 'Informatique', 'Medical', 'BTP'];

    public const DRAFT = 'draft';
    public const PUBLISHED = "published";
    public const CLOSED = "closed";
    public static array $statusTab = [self::DRAFT, self::PUBLISHED, self::CLOSED];

    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $slug = '';

    /**
     * @ORM\Column(type="text")
     */
    private string $description = '';

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $salary = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $activity = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $contractType = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $status = '';

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="offers")
     * @ORM\JoinColumn(nullable=false)
     */
    private Company $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSalary(): ?int
    {
        return $this->salary;
    }

    public function setSalary(?int $salary): self
    {
        $this->salary = $salary;

        return $this;
    }

    public function getActivity(): string
    {
        return $this->activity;
    }

    public function setActivity(string $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getContractType(): string
    {
        return $this->contractType;
    }

    public function setContractType(string $contractType): self
    {
        $this->contractType = $contractType;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOwner(): Company
    {
        return $this->owner;
    }

    public function setOwner(Company $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
