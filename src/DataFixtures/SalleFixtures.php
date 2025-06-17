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

        $room = [
            'Roberts',
            'Duponts',
            'Montess',
            'Charlemagne',
        ];

        for ($i = 0; $i < 50; $i++) {

            $salle = new Salle();
            $salle
                ->setNom($faker->company())
                ->setLieu($faker->address())
                ->setCapacite($faker->randomElement([50, 80, 100, 140, 200]))
                ->setImage('/medias/images/' . $faker->randomElement($images));


            
            $reserved = $faker->boolean(30);
            $salle->setReserved($reserved);


            $manager->persist($salle);

        }

        $manager->flush();
    }
}
