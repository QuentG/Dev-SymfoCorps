<?php

namespace App\Controller\Api;

use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/offers", name="api_offers_")
 */
final class OfferController extends AbstractController
{
    private SerializerInterface $serializer;
    private OfferRepository $offerRepository;

    public function __construct(SerializerInterface $serializer, OfferRepository $offerRepository)
    {
        $this->serializer = $serializer;
        $this->offerRepository = $offerRepository;
    }

    /**
     * @Route("", name="all", methods={"GET"})
     */
    public function all(): JsonResponse
    {
        $offers = $this->serializer->serialize($this->offerRepository->findAll(), 'json', ['groups' => 'read_all']);
        return new JsonResponse($offers, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @Route("/{id}", name="get", methods={"GET"})
     */
    public function index(int $id): JsonResponse
    {
        if (!$offer = $this->offerRepository->find($id)) {
            return new JsonResponse(['message' => 'offer_not_found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $offers = $this->serializer->serialize($offer, 'json', ['groups' => 'read_all']);
        return new JsonResponse($offers, JsonResponse::HTTP_OK, [], true);
    }

}