<?php

namespace App\DataFixtures;

use App\Entity\Salle;
use App\Entity\CritErgo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CritErgoFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $criteres = [
            ['nom' => 'Parking réservé', 'categorie' => 'PMR'],
            ['nom' => 'Rampe d\'accès', 'categorie' => 'PMR'],
            ['nom' => 'Porte d\'entrée large', 'categorie' => 'PMR'],
            ['nom' => 'Toilettes accessibles', 'categorie' => 'PMR'],
            ['nom' => 'Circulation intérieure large', 'categorie' => 'PMR'],
            ['nom' => 'Ascenseur ou élévateur', 'categorie' => 'PMR'],
            ['nom' => 'Zone PMR dans la salle', 'categorie' => 'PMR'],
            ['nom' => 'Signalétique accessible', 'categorie' => 'PMR'],
            ['nom' => 'Boucle magnétique', 'categorie' => 'PMR'],
            ['nom' => 'Éclairage suffisant', 'categorie' => 'PMR'],

            ['nom' => 'Caméras de surveillance', 'categorie' => 'Sécurité'],
            ['nom' => 'Alarme incendie', 'categorie' => 'Sécurité'],
            ['nom' => 'Plans d\'évacuation', 'categorie' => 'Sécurité'],
            ['nom' => 'Issues de secours', 'categorie' => 'Sécurité'],
            ['nom' => 'Portes coupe-feu', 'categorie' => 'Sécurité'],

            ['nom' => 'Lumière naturelle', 'categorie' => 'Confort & Environnement'],
            ['nom' => 'Éclairage LED modulable', 'categorie' => 'Confort & Environnement'],
            ['nom' => 'Chauffage & climatisation', 'categorie' => 'Confort & Environnement'],
            ['nom' => 'Isolation phonique', 'categorie' => 'Confort & Environnement'],
            ['nom' => 'Wi-Fi public ou privé', 'categorie' => 'Confort & Environnement'],
            ['nom' => 'Prises électriques', 'categorie' => 'Confort & Environnement'],
            ['nom' => 'Zone de dépose-minute', 'categorie' => 'Confort & Environnement'],
            ['nom' => 'Accès transports', 'categorie' => 'Confort & Environnement'],
        ];

        $critObjects = [];

        // Étape 1 : Créer les CritErgo une seule fois
        foreach ($criteres as $data) {
            $crit = new CritErgo();
            $crit->setNom($data['nom']);
            $crit->setCategorie($data['categorie']);
            $manager->persist($crit);
            $critObjects[] = $crit;
        }

        $manager->flush();

        // Étape 2 : Associer les critères aux salles
        $salles = $manager->getRepository(Salle::class)->findAll();

        foreach ($salles as $salle) {
            $selection = $faker->randomElements($critObjects, 5); // 5 critères aléatoires
            foreach ($selection as $crit) {
            $salle->addCritErgo($crit);
            }
            $manager->persist($salle);
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
