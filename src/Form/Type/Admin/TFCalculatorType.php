<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Universe\Resources\ResourceNames;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TFCalculatorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('count', 0);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('metal', IntegerType::class, [
                'label' => ResourceNames::METAL,
                'attr' => [
                    'data-tf-calculator-target' => 'metal',
                ],
            ])
            ->add('crystal', IntegerType::class, [
                'label' => ResourceNames::CRYSTAL,
                'attr' => [
                    'data-tf-calculator-target' => 'crystal',
                ],
            ])
            ->add('plastic', IntegerType::class, [
                'label' => ResourceNames::PLASTIC,
                'attr' => [
                    'data-tf-calculator-target' => 'plastic',
                ],
            ])
            ->add('planets', CollectionType::class, [
                'entry_type' => TfCalculatorPlanetRowType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Aufteilen',
            ]);
    }
}
