<?php

namespace App\Controller;

use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(OfferRepository $offerRepository): Response
    {
        return $this->render('home.html.twig', [
            'offers' => $offerRepository->getPublish(3)
        ]);
    }

    /**
     * @Route("/switch-language/{locale}", name="language_switch")
     */
    public function switchLanguage(string $locale, Request $request): RedirectResponse
    {
        $request->getSession()->set('_locale', $locale);
        $referer = $request->headers->get('referer');

        if (!strpos($referer, $request->getHost())) {
            return $this->redirectToRoute('home');
        }

        return new RedirectResponse($referer);
    }
}
