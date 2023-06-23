<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class UniverseBigBangType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $twoDigitFieldAttributes = [
            'min' => 0,
            'max' => 100,
            'size' => 2,
        ];

        $builder
            ->add('numberOfSectors1', IntegerType::class, [
                'label' => false,
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('numberOfSectors2', IntegerType::class, [
                'label' => false,
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('numberOfCells1', IntegerType::class, [
                'label' => false,
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('numberOfCells2', IntegerType::class, [
                'label' => false,
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('starPercent', IntegerType::class, [
                'label' => false,
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('asteroidPercent', IntegerType::class, [
                'label' => false,
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('nebulaPercent', IntegerType::class, [
                'label' => false,
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('wormholePercent', IntegerType::class, [
                'label' => false,
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('wormholePersistentPercent', IntegerType::class, [
                'label' => false,
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('emptySpacePercent', IntegerType::class, [
                'label' => false,
                'disabled' => 'disabled',
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('numberOfPlanets1', IntegerType::class, [
                'label' => false,
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('numberOfPlanets2', IntegerType::class, [
                'label' => false,
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('solarSystemPlanetPercent', IntegerType::class, [
                'label' => false,
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('solarSystemAsteroidPercent', IntegerType::class, [
                'label' => false,
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('solarSystemEmptySpacePercent', IntegerType::class, [
                'label' => false,
                'disabled' => 'disabled',
                'attr' => $twoDigitFieldAttributes,
            ])
            ->add('planetFields1', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'size' => 4,
                    'min' => 0,
                ],
            ])
            ->add('planetFields2', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'size' => 4,
                    'min' => 0,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Weiter',
            ]);
    }
}
