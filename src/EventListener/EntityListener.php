<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

class EntityListener
{
    private const CREATED_AT_FIELD = "createdAt";
    private const UPDATED_AT_FIELD = "updatedAt";

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (property_exists($entity, self::CREATED_AT_FIELD)) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->setUpdatedAt(new \DateTime());
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
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }
}