<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/offers", name="offers_")
 */
final class OfferController extends AbstractController
{
    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(OfferRepository $offerRepository): Response
    {
        return $this->render('offer/index.html.twig', [
            'offers' => $offerRepository->getPublish()
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET"})
     */
    public function show(Offer $offer): Response
    {
        return $this->render('offer/show.html.twig', compact('offer'));
    }
}