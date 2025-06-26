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

        $villes = [
            ['nom' => 'Paris', 'cp' => '750'],
            ['nom' => 'Lyon', 'cp' => '690'],
            ['nom' => 'Marseille', 'cp' => '130'],
            ['nom' => 'Toulouse', 'cp' => '310'],
            ['nom' => 'Nice', 'cp' => '060'],
        ];

        $nameCounter = [];

        for ($i = 0; $i < 50; $i++) {

            $base = $faker->randomElement($roomNames);
            $nameCounter[$base] = ($nameCounter[$base] ?? 0) + 1;
            $uniqueName = $base . ' ' . $nameCounter[$base];

            $ville = $faker->randomElement($villes);
            $codePostal = $ville['cp'] . $faker->numberBetween(0, 9) . $faker->numberBetween(0, 9);
            $adresse = $faker->streetAddress . ', ' . $codePostal . ' ' . $ville['nom'];

            $salle = new Salle();
            $salle
                ->setNom($uniqueName)
                ->setLieu($adresse) // <- adresse réaliste
                ->setCapacite($faker->randomElement([50, 80, 100, 140, 200]))
                ->setImage('/medias/images/' . $faker->randomElement($images))
                ->setDescription($faker->text(300));

            $manager->persist($salle);
        }

        $manager->flush();
    }
}
