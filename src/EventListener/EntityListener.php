<?php

namespace App\EventListener;

use App\Entity\Offer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class EntityListener
{
    private const CREATED_AT_FIELD = "createdAt";
    private const UPDATED_AT_FIELD = "updatedAt";

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (property_exists($entity, self::CREATED_AT_FIELD)) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->setUpdatedAt(new \DateTime());
        }

        if ($entity instanceof Offer) {
            $entity->setSlug($this->slugger->slug($entity->getTitle()));
        }
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->setUpdatedAt(new \DateTime());
        }

        if ($entity instanceof Offer) {
            $entity->setSlug($this->slugger->slug($entity->getTitle()));
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }
}