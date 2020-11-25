<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private const LOCALE_ATTRIBUTE = '_locale';

    private string $defaultLocale;

    public function __construct($defaultLocale = 'fr')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]]
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        if ($locale = $request->query->get(self::LOCALE_ATTRIBUTE)) { // Cf. DefaultController::switchLanguage
            $request->setLocale($locale);
        } else {
            $request->setLocale(
                $request->getSession()->get(self::LOCALE_ATTRIBUTE, $this->defaultLocale)
            );
        }
    }
}