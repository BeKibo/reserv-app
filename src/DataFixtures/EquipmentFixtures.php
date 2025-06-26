<?php

namespace App\DataFixtures;

use App\Entity\Equipement;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class EquipmentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $equipements = [
            // ÉQUIPEMENT
            ['nom' => 'Projecteur', 'categorie' => 'Équipement'],
            ['nom' => 'Écran de projection', 'categorie' => 'Équipement'],
            ['nom' => 'Système de sonorisation', 'categorie' => 'Équipement'],
            ['nom' => 'Micro sans fil', 'categorie' => 'Équipement'],
            ['nom' => 'Table de mixage audio', 'categorie' => 'Équipement'],
            ['nom' => 'Pupitre', 'categorie' => 'Équipement'],
            ['nom' => 'Estrade', 'categorie' => 'Équipement'],
            ['nom' => 'Table pliante', 'categorie' => 'Équipement'],
            ['nom' => 'Chaise confort', 'categorie' => 'Équipement'],
            ['nom' => 'Éclairage d’ambiance', 'categorie' => 'Équipement'],

            // LOGICIEL
            ['nom' => 'PowerPoint', 'categorie' => 'Logiciel'],
            ['nom' => 'Zoom', 'categorie' => 'Logiciel'],
            ['nom' => 'Google Calendar', 'categorie' => 'Logiciel'],
            ['nom' => 'VirtualDJ', 'categorie' => 'Logiciel'],
            ['nom' => 'ProPresenter', 'categorie' => 'Logiciel'],
            ['nom' => 'DMX Light Controller', 'categorie' => 'Logiciel'],
            ['nom' => 'VLC Media Player', 'categorie' => 'Logiciel'],
            ['nom' => 'Logiciel photobooth', 'categorie' => 'Logiciel'],
            ['nom' => 'Eventbrite', 'categorie' => 'Logiciel'],
            ['nom' => 'Teams', 'categorie' => 'Logiciel'],
        ];

        foreach ($equipements as $data) {
            $equipement = new Equipement();
            $equipement->setNom($data['nom']);
            $equipement->setCategorie($data['categorie']);
            $manager->persist($equipement);
        }

        $manager->flush();
    }
}
