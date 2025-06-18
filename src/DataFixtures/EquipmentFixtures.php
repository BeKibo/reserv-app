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
            // LUMIÈRE
            ['nom' => 'Lampe', 'categorie' => 'Lumière', 'quantite' => 200],
            ['nom' => 'Bougie', 'categorie' => 'Lumière', 'quantite' => 200],
            ['nom' => 'Projecteur', 'categorie' => 'Lumière', 'quantite' => 100],
            ['nom' => 'Spot coloré', 'categorie' => 'Lumière', 'quantite' => 100],
            ['nom' => 'Boule à facette', 'categorie' => 'Lumière', 'quantite' => 50],
            ['nom' => 'Guirlande lumineuse', 'categorie' => 'Lumière', 'quantite' => 100],

            // MOBILIER
            ['nom' => 'Chaise', 'categorie' => 'Mobilier', 'quantite' => 250],
            ['nom' => 'Table', 'categorie' => 'Mobilier', 'quantite' => 50],
            ['nom' => 'Banc', 'categorie' => 'Mobilier', 'quantite' => 50],
            ['nom' => 'Tabouret', 'categorie' => 'Mobilier', 'quantite' => 100],
            ['nom' => 'Estrade', 'categorie' => 'Mobilier', 'quantite' => 25],
            ['nom' => 'Paravent', 'categorie' => 'Mobilier', 'quantite' => 25],
            ['nom' => 'Comptoir pliant', 'categorie' => 'Mobilier', 'quantite' => 25],

            // ÉLECTRONIQUE
            ['nom' => 'Micro', 'categorie' => 'Électronique', 'quantite' => 100],
            ['nom' => 'Enceinte', 'categorie' => 'Électronique', 'quantite' => 100],
            ['nom' => 'Amplificateur', 'categorie' => 'Électronique', 'quantite' => 50],
            ['nom' => 'Machine à fumée', 'categorie' => 'Électronique', 'quantite' => 25],
            ['nom' => 'Ordinateur', 'categorie' => 'Électronique', 'quantite' => 50],
            ['nom' => 'Mixeur audio', 'categorie' => 'Électronique', 'quantite' => 25],
            ['nom' => 'Console DJ', 'categorie' => 'Électronique', 'quantite' => 25],

            // ÉLECTROMÉNAGER (1 par salle au minimum)
            ['nom' => 'Four', 'categorie' => 'Électroménager', 'quantite' => 50],
            ['nom' => 'Micro-onde', 'categorie' => 'Électroménager', 'quantite' => 50],
            ['nom' => 'Frigo', 'categorie' => 'Électroménager', 'quantite' => 50],
            ['nom' => 'Plaque de cuisson', 'categorie' => 'Électroménager', 'quantite' => 50],
            ['nom' => 'Congélateur', 'categorie' => 'Électroménager', 'quantite' => 50],
            ['nom' => 'Plancha', 'categorie' => 'Électroménager', 'quantite' => 50],
            ['nom' => 'Cafetière', 'categorie' => 'Électroménager', 'quantite' => 50],

            // USTENSILES (en masse pour 200+ personnes)
            ['nom' => 'Couvert', 'categorie' => 'Ustensile', 'quantite' => 500],
            ['nom' => 'Balais', 'categorie' => 'Ustensile', 'quantite' => 50],
            ['nom' => 'Poubelle', 'categorie' => 'Ustensile', 'quantite' => 100],
            ['nom' => 'Pelle', 'categorie' => 'Ustensile', 'quantite' => 50],
            ['nom' => 'Nappe', 'categorie' => 'Ustensile', 'quantite' => 100],
            ['nom' => 'Seau', 'categorie' => 'Ustensile', 'quantite' => 50],
            ['nom' => 'Éponge industrielle', 'categorie' => 'Ustensile', 'quantite' => 100],
        ];

        foreach ($equipements as $data) {
            for ($i = 1; $i <= $data['quantite']; $i++) {
                $equipement = new Equipement();
                $equipement->setNom($data['nom'] . ' ' . $i);
                $equipement->setCategorie($data['categorie']);
                $manager->persist($equipement);
            }
        }

        $manager->flush();
    }
}
