<?php

namespace App\DataFixtures;

use App\Entity\Equipement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EquipementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $nbSalles = 50; // nombre de salles à équiper

        $equipements = [
            // LUMIÈRE
            ['nom' => 'Lampe', 'type' => 'Lumière', 'quantite' => 450], 
            ['nom' => 'Bougie', 'type' => 'Lumière', 'quantite' => 150],
            ['nom' => 'Projecteur', 'type' => 'Lumière', 'quantite' => 75],
            ['nom' => 'Spot coloré', 'type' => 'Lumière', 'quantite' => 100],
            ['nom' => 'Boule à facette', 'type' => 'Lumière', 'quantite' => 50],
            ['nom' => 'Guirlande lumineuse', 'type' => 'Lumière', 'quantite' => 100],

            // MOBILIER
            ['nom' => 'Chaise', 'type' => 'Mobilier', 'quantite' => 1400], 
            ['nom' => 'Table', 'type' => 'Mobilier', 'quantite' => 500], 
            ['nom' => 'Banc', 'type' => 'Mobilier', 'quantite' => 50],
            ['nom' => 'Tabouret', 'type' => 'Mobilier', 'quantite' => 100],
            ['nom' => 'Estrade', 'type' => 'Mobilier', 'quantite' => 25],
            ['nom' => 'Paravent', 'type' => 'Mobilier', 'quantite' => 25],
            ['nom' => 'Comptoir pliant', 'type' => 'Mobilier', 'quantite' => 25],

            // ÉLECTRONIQUE
            ['nom' => 'Micro', 'type' => 'Électronique', 'quantite' => 100], // 2 par salle
            ['nom' => 'Enceinte', 'type' => 'Électronique', 'quantite' => 100],
            ['nom' => 'Amplificateur', 'type' => 'Électronique', 'quantite' => 50],
            ['nom' => 'Machine à fumée', 'type' => 'Électronique', 'quantite' => 25],
            ['nom' => 'Ordinateur', 'type' => 'Électronique', 'quantite' => 50],
            ['nom' => 'Mixeur audio', 'type' => 'Électronique', 'quantite' => 25],
            ['nom' => 'Console DJ', 'type' => 'Électronique', 'quantite' => 25],

            // ÉLECTROMÉNAGER - essentiel pour chaque salle équipée
            ['nom' => 'Four', 'type' => 'Électroménager', 'quantite' => 50],
            ['nom' => 'Micro-onde', 'type' => 'Électroménager', 'quantite' => 50],
            ['nom' => 'Frigo', 'type' => 'Électroménager', 'quantite' => 50],
            ['nom' => 'Plaque de cuisson', 'type' => 'Électroménager', 'quantite' => 40],
            ['nom' => 'Congélateur', 'type' => 'Électroménager', 'quantite' => 40],
            ['nom' => 'Plancha', 'type' => 'Électroménager', 'quantite' => 30],
            ['nom' => 'Cafetière', 'type' => 'Électroménager', 'quantite' => 60],

            // USTENSILES - en nombre important pour couvrir tous les usages
            ['nom' => 'Couvert', 'type' => 'Ustensile', 'quantite' => 500],
            ['nom' => 'Balais', 'type' => 'Ustensile', 'quantite' => 60],
            ['nom' => 'Poubelle', 'type' => 'Ustensile', 'quantite' => 100],
            ['nom' => 'Pelle', 'type' => 'Ustensile', 'quantite' => 50],
            ['nom' => 'Nappe', 'type' => 'Ustensile', 'quantite' => 100],
            ['nom' => 'Seau', 'type' => 'Ustensile', 'quantite' => 60],
            ['nom' => 'Éponge industrielle', 'type' => 'Ustensile', 'quantite' => 100],
        ];

        foreach ($equipements as $data) {
            for ($i = 1; $i <= $data['quantite']; $i++) {
                $equipement = new Equipement();
                $equipement->setNom($data['nom'] . ' ' . $i);
                $equipement->setType($data['type']);
                $manager->persist($equipement);
            }
        }

        $manager->flush();
    }
}
