<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Salle;
use App\Entity\User;
use App\Entity\Reservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use phpDocumentor\Reflection\Types\Boolean;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $salles = $manager->getRepository(Salle::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();


        foreach ($salles as $salle) {
            for ($i = 0; $i < 5; $i++) {
                // Génère une date de début aléatoire dans les 30 prochains jours
                $start = \DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('now', '+30 days')
                );

                // Génère une date de fin +1 à +3 jours après
                $end = $start->modify('+' . $faker->numberBetween(1, 3) . ' days');



                // Création de la réservation
                $reservation = new Reservation();
                $reservation->setSalles($salle);
                $reservation->setUsers($faker->randomElement($users));
                $reservation->setDateDebut($start);
                $reservation->setDateFin($end);
                $reservation->setValidation($faker->boolean(15));

        

                $manager->persist($reservation);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SalleFixtures::class,
            UserFixtures::class,
        ];
    }
}
