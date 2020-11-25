<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Offer;
use App\Entity\Particular;
use App\Utils\FixturesUtils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private const PASSWORD = "password";

    private UserPasswordEncoderInterface $encoder;
    private Generator $faker;
    private FixturesUtils $fixturesUtils;

    private array $users = [];
    private array $offers = [];

    public function __construct(UserPasswordEncoderInterface $encoder, FixturesUtils $fixturesUtils)
    {
        $this->encoder = $encoder;
        $this->faker = Factory::create('fr_FR');
        $this->fixturesUtils = $fixturesUtils;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadOffers($manager);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        for ($u = 0; $u < 20; $u++) {
            $user = $u < 10 ? new Company() : new Particular();
            $user->setIsVerified(true);

            $user->setPassword($this->encoder->encodePassword($user, self::PASSWORD));

            if ($user->isCompany()) {
                $user->setEmail(sprintf('company%s@test.com', $u))
                    ->setCompanyName($this->faker->company);
            } else {
                $user->setEmail(sprintf('particular%s@test.com', $u))
                    ->setFirstName($this->faker->firstName)
                    ->setLastName($this->faker->lastName);
            }

            $this->users[] = $user;
            $manager->persist($user);
        }
    }

    private function loadOffers(ObjectManager $manager): void
    {
        $users = $this->getCompanies();

        for ($o = 0; $o < 30; ++$o) {
            $offer = (new Offer())
                ->setTitle($this->faker->jobTitle)
                ->setDescription($this->fixturesUtils->generateParagraph(2))
                ->setOwner($this->fixturesUtils->getRandomItem($users))
                ->setActivity($this->fixturesUtils->getRandomItem(Offer::$activities))
                ->setContractType($this->fixturesUtils->getRandomItem(Offer::$contractTypes))
                ->setSalary($o < 20 ? null : $this->faker->numberBetween(15000, 40000))
                ->setStatus($this->fixturesUtils->getRandomItem(Offer::$statusTab))
            ;

            $this->offers[] = $offer;
            $manager->persist($offer);
        }
    }

    private function getCompanies(): array
    {
        return array_filter($this->users, static fn ($user) => $user instanceof Company);
    }

    private function getParticulars(): array
    {
        return array_filter($this->users, static fn ($user) => $user instanceof Particular);
    }
}
