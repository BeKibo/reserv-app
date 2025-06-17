<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 70; $i++) {

            $user = new User();
            $user
                ->setEmail($faker->email())
                ->setNom($faker->Name())
                ->setPassword($this->hasher->hashPassword($user, 'admin'))
            ;

            $manager->persist($user);
            $this->addReference('USER_' . $i, $user);
        }

        $manager->flush();

        // Admin
        $admin = new User();
        $admin
            ->setEmail('admin@admin.fr')
            ->setNom('admin')
            ->setPassword($this->hasher->hashPassword($admin, 'admin'))
            ->setRoles(['ROLE_ADMIN'])

        ;

        $manager->persist($admin);
        $manager->flush();
    }
}
