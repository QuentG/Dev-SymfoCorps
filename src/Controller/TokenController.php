<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/token", name="token_")
 */
class TokenController extends AbstractController
{
    /**
     * @Route("/refresh", name="refresh", methods={"PATCH"})
     */
    public function refresh(
        Request $request,
        UserRepository $userRepository,
        TokenGenerator $tokenGenerator
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!array_key_exists('refreshToken', $data)) {
            return new JsonResponse(['message' => 'missing_fields'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy([
            'refreshToken' => $data['refreshToken']
        ]);

        if (null === $user) {
            return new JsonResponse(['message' => 'refresh_token_not_found'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $tokenGenerator->generateAuthToken($user);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'accessToken' => $user->getAccessToken(),
            'refreshToken' => $user->getRefreshToken()
        ]);
    }
}