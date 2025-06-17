<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Salle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SalleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $images = [
            'salle1.jpg',
            'salle2.jpg',
            'salle3.jpg',
            'salle4.jpg',
        ];

        $roomNames = [
            'Roberts',
            'Duponts',
            'Montess',
            'Charlemagne',
        ];

        $nameCounter = [];

        for ($i = 0; $i < 50; $i++) {

            // Choix d’un nom de base aléatoire
            $base = $faker->randomElement($roomNames);

            // Incrémentation du compteur
            if (!isset($nameCounter[$base])) {
                $nameCounter[$base] = 1;
            } else {
                $nameCounter[$base]++;
            }

            // Génération du nom complet
            $uniqueName = $base . ' ' . $nameCounter[$base];

            $salle = new Salle();
            $salle
                ->setNom($uniqueName)
                ->setLieu($faker->address())
                ->setCapacite($faker->randomElement([50, 80, 100, 140, 200]))
                ->setImage('/medias/images/' . $faker->randomElement($images))
                ->setReserved($faker->boolean(30));

            $manager->persist($salle);
        }

        $manager->flush();
    }
}
