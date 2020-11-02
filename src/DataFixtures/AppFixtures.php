<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private const PASSWORD = "password";

    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        for ($u = 0; $u < 10; $u++) {
            $user = (new User())
                ->setEmail(sprintf('test%s@test.com', $u));

            $user->setPassword($this->encoder->encodePassword($user, self::PASSWORD));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
