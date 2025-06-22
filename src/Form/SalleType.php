<?php

namespace App\Form;

use App\Entity\Salle;
use App\Entity\Equipement;
use App\Entity\CritErgo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'required' => true,
            ])
            ->add('capacite', IntegerType::class, [
                'label' => 'Capacité',
                'required' => true,
                'attr' => [
                    'min' => 10,
                    'max' => 999,
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => ['rows' => 5],
            ])
            ->add('image', TextType::class, [
                'label' => 'Image',
                'required' => false,
                'help' => 'Chemin vers l\'image (ex: /medias/images/salle1.jpg)',
            ])
            ->add('Equipement', EntityType::class, [
                'class' => Equipement::class,
                'choice_label' => 'nom',
                'label' => 'Équipements',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
            ->add('critergo', EntityType::class, [
                'class' => CritErgo::class,
                'choice_label' => 'nom',
                'label' => 'Critères Ergonomiques',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Salle::class,
        ]);
    }
}
