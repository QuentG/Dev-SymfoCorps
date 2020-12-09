<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiAuthenticator extends AbstractGuardAuthenticator
{
    private const HEADER_AUTH_TOKEN = "X-SYMFO-AUTH-TOKEN";

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has(self::HEADER_AUTH_TOKEN);
    }

    public function getCredentials(Request $request): array
    {
        return [
            'accessToken' => $request->headers->get(self::HEADER_AUTH_TOKEN)
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        if (!$credentials['accessToken']) {
            throw new AuthenticationException("token_was_empty");
        }

        $apiToken = $this->userRepository->findOneBy([
            'accessToken' => $credentials['accessToken']
        ]);

        if (null === $apiToken) {
            throw new AuthenticationException("token_not_found");
        }

        $now = new \DateTime();
        if (!$apiToken->tokenIsValid($now)) {
            throw new AuthenticationException("token_was_expired");
        }

        return $apiToken;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse(['message' => 'access_token_invalid'], JsonResponse::HTTP_BAD_REQUEST);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse(['message' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
