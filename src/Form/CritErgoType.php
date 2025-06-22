<?php

namespace App\Form;

use App\Entity\CritErgo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CritErgoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'required' => true,
                'choices' => [
                    'Accessibilité' => 'Accessibilité',
                    'Confort' => 'Confort',
                    'Acoustique' => 'Acoustique',
                    'Luminosité' => 'Luminosité',
                    'Température' => 'Température',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CritErgo::class,
        ]);
    }
}
