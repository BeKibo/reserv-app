<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Salle;
use App\Entity\Equipement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('salles', EntityType::class, [
                'class' => Salle::class,
                'choice_label' => 'nom',
                'label' => 'Salle',
                'placeholder' => 'Choisir une salle',
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Date de début',
                ])
                ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Date de fin',
                ])
            ->add('equipements', EntityType::class, [
                'class' => Equipement::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true, // liste déroulante multi-select
                'label' => 'Équipements souhaités',
                'required' => false,
                'attr' => [
                    'class' => 'form-control select2', // compatible avec Select2 si tu veux améliorer l’UI
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
