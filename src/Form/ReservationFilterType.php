<?php

namespace App\Form;

use App\Data\ReservationFilterData;
use App\Entity\CritErgo;
use App\Entity\Equipement;
use App\Entity\Salle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ReservationFilterType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
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
->add('lieu', EntityType::class, [
'class' => Salle::class,
'choice_label' => 'lieu',
'required' => true,
'label' => 'Lieu',
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
'label' => 'Critères ergonomiques',
])
->add('equipements', EntityType::class, [
'class' => Equipement::class,
'choice_label' => 'nom',
'multiple' => true,
'required' => false,
'label' => 'Équipements',
]);
}

public function configureOptions(OptionsResolver $resolver)
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



