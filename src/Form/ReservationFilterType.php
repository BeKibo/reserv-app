<?php
// src/Form/ReservationFilterType.php
namespace App\Form;

use App\Data\ReservationFilterData;
use App\Entity\CritErgo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', SearchType::class, [
                'required' => false,
                'label' => 'Nom de la salle',
            ])
            ->add('capaciteMin', IntegerType::class, [
                'required' => false,
                'label' => 'Capacité minimale',
            ])
            ->add('ville', ChoiceType::class, [
                'choices' => [
                    'Paris' => 'Paris',
                    'Lyon' => 'Lyon',
                    'Marseille' => 'Marseille',
                    'Toulouse' => 'Toulouse',
                    'Nice' => 'Nice',
                ],
                'required' => false,
                'mapped' => false,
                'label' => 'Ville',
                'placeholder' => 'Toutes les villes',
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
            ->add('critergos', EntityType::class, [
                'class' => CritErgo::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'required' => false,
                'expanded' => true,
                'label' => 'Critères ergonomiques',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReservationFilterData::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
