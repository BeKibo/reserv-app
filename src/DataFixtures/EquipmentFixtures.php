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
            ['nom' => 'Lampe', 'categorie' => 'Lumière', 'quantite' => 40],
            ['nom' => 'Bougie', 'categorie' => 'Lumière', 'quantite' => 40],
            ['nom' => 'Projecteur', 'categorie' => 'Lumière', 'quantite' => 30],
            ['nom' => 'Spot coloré', 'categorie' => 'Lumière', 'quantite' => 30],
            ['nom' => 'Boule à facette', 'categorie' => 'Lumière', 'quantite' => 30],
            ['nom' => 'Guirlande lumineuse', 'categorie' => 'Lumière', 'quantite' => 40],

            // MOBILIER
            ['nom' => 'Chaise', 'categorie' => 'Mobilier', 'quantite' => 40],
            ['nom' => 'Table', 'categorie' => 'Mobilier', 'quantite' => 30],
            ['nom' => 'Banc', 'categorie' => 'Mobilier', 'quantite' => 30],
            ['nom' => 'Tabouret', 'categorie' => 'Mobilier', 'quantite' => 40],
            ['nom' => 'Estrade', 'categorie' => 'Mobilier', 'quantite' => 30],
            ['nom' => 'Paravent', 'categorie' => 'Mobilier', 'quantite' => 30],
            ['nom' => 'Comptoir pliant', 'categorie' => 'Mobilier', 'quantite' => 30],

            // ÉLECTRONIQUE
            ['nom' => 'Micro', 'categorie' => 'Électronique', 'quantite' => 40],
            ['nom' => 'Enceinte', 'categorie' => 'Électronique', 'quantite' => 40],
            ['nom' => 'Amplificateur', 'categorie' => 'Électronique', 'quantite' => 30],
            ['nom' => 'Machine à fumée', 'categorie' => 'Électronique', 'quantite' => 30],
            ['nom' => 'Ordinateur', 'categorie' => 'Électronique', 'quantite' => 30],
            ['nom' => 'Mixeur audio', 'categorie' => 'Électronique', 'quantite' => 30],
            ['nom' => 'Console DJ', 'categorie' => 'Électronique', 'quantite' => 30],

            // ÉLECTROMÉNAGER
            ['nom' => 'Four', 'categorie' => 'Électroménager', 'quantite' => 30],
            ['nom' => 'Micro-onde', 'categorie' => 'Électroménager', 'quantite' => 30],
            ['nom' => 'Frigo', 'categorie' => 'Électroménager', 'quantite' => 30],
            ['nom' => 'Plaque de cuisson', 'categorie' => 'Électroménager', 'quantite' => 30],
            ['nom' => 'Congélateur', 'categorie' => 'Électroménager', 'quantite' => 30],
            ['nom' => 'Plancha', 'categorie' => 'Électroménager', 'quantite' => 30],
            ['nom' => 'Cafetière', 'categorie' => 'Électroménager', 'quantite' => 30],

            // USTENSILES
            ['nom' => 'Couvert', 'categorie' => 'Ustensile', 'quantite' => 40],
            ['nom' => 'Balais', 'categorie' => 'Ustensile', 'quantite' => 30],
            ['nom' => 'Poubelle', 'categorie' => 'Ustensile', 'quantite' => 30],
            ['nom' => 'Pelle', 'categorie' => 'Ustensile', 'quantite' => 30],
            ['nom' => 'Nappe', 'categorie' => 'Ustensile', 'quantite' => 30],
            ['nom' => 'Seau', 'categorie' => 'Ustensile', 'quantite' => 30],
            ['nom' => 'Éponge industrielle', 'categorie' => 'Ustensile', 'quantite' => 30],
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
