<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class ReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            AssociationField::new('users', 'Utilisateur'),
            AssociationField::new('salles', 'Salle'),
            // AssociationField::new('equipements', 'Equipement'),
            DateTimeField::new('dateDebut', 'Date Début'),
            DateTimeField::new('dateFin', 'Date Fin'),
            BooleanField::new('validation', 'Validation'),
        ];
    }
}
