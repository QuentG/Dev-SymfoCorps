<?php

namespace App\Controller;

use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
