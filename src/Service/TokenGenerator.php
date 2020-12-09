<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class TokenGenerator
{
    public const TOKEN_VALIDITY = "P2D";

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Generate token depends on size
     */
    public function generate(int $length = 10): string
    {
        return sha1(random_bytes($length));
    }

    public function generateAuthToken(User $user, int $length = 10): void
    {
        do {
            $accessToken = $this->generate($length);
        } while ($this->tokenAlreadyExists($accessToken, 'accessToken'));

        do {
            $refreshToken = $this->generate($length);
        } while ($this->tokenAlreadyExists($refreshToken, 'refreshToken'));

        $user->setAccessToken($accessToken)
            ->setRefreshToken($refreshToken)
            ->setExpirationDate((new \DateTime())->add(new \DateInterval(self::TOKEN_VALIDITY)));
    }

    private function tokenAlreadyExists(string $token, string $tokenType): bool
    {
        $tokenExists = $this->userRepository->findOneBy([
            $tokenType => $token
        ]);

        return null !== $tokenExists;
    }
}