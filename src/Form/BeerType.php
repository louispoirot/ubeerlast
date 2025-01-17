<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BeerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brewery_id', ChoiceType::class, [
                'label' => 'Brasserie',
                'choices' => [
                    'Brasserie 1' => 1,
                    'Brasserie 2' => 2,
                    'Brasserie 3' => 3,
                    'Brasserie 4' => 4,
                    'Brasserie 5' => 5
                ],
                'required' => true,
                'attr' => ['class' => 'form-select']
            ])
            ->add('beer', TextType::class, [
                'label' => 'Nom de la bière'
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'scale' => 2
            ])
            ->add('imageUrl', UrlType::class, [
                'label' => 'URL de l\'image'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
