<?php

namespace App\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private string $defaultUserAvatar;

    public function __construct(string $defaultUserAvatar)
    {
        $this->defaultUserAvatar = $defaultUserAvatar;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('userImage', [$this, 'getUserImage'])
        ];
    }

    public function getUserImage(User $user): string
    {
        if ($avatar = $user->getAvatar()) {
            return '/' . $avatar;
        }

        return $this->defaultUserAvatar;
    }
}