<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\EntityType;
use EtoA\Form\Type\Core\FleetActionStatusType;
use EtoA\Form\Type\Core\FleetActionType;
use EtoA\Form\Type\Core\TimestampType;
use EtoA\Form\Type\Core\UserType;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class FleetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userId', UserType::class, [
                'required' => true,
                'placeholder' => false,
                'label' => 'Besitzer',
            ])
            ->add('launchTime', TimestampType::class, [
                'label' => 'Startzeit',
            ])
            ->add('landTime', TimestampType::class, [
                'label' => 'Landezeit',
            ])
            ->add('entityFrom', EntityType::class, [
                'required' => true,
                'placeholder' => false,
            ])
            ->add('entityTo', EntityType::class, [
                'required' => true,
                'placeholder' => false,
            ])
            ->add('action', FleetActionType::class, [
                'required' => true,
                'placeholder' => false,
                'label' => 'Aktion',
            ])
            ->add('status', FleetActionStatusType::class, [
                'required' => true,
                'placeholder' => false,
                'label' => false,
            ])
            ->add('pilots', IntegerType::class, [
                'label' => 'Piloten',
            ])
            ->add('usageFuel', IntegerType::class, [
                'label' => 'Verbrauch Tritium',
            ])
            ->add('usageFood', IntegerType::class, [
                'label' => 'Verbrauch Nahrung',
            ])
            ->add('resMetal', IntegerType::class, [
                'label' => 'Fracht ' . ResourceNames::METAL,
            ])
            ->add('resCrystal', IntegerType::class, [
                'label' => 'Fracht '  . ResourceNames::CRYSTAL,
            ])
            ->add('resPlastic', IntegerType::class, [
                'label' => 'Fracht '  . ResourceNames::PLASTIC,
            ])
            ->add('resFuel', IntegerType::class, [
                'label' => 'Fracht '  . ResourceNames::FUEL,
            ])
            ->add('resFood', IntegerType::class, [
                'label' => 'Fracht '  . ResourceNames::FOOD,
            ])
            ->add('resPower', IntegerType::class, [
                'label' => 'Fracht '  . ResourceNames::POWER,
            ])
            ->add('resPeople', IntegerType::class, [
                'label' => 'Passagiere',
            ])
            ->add('fetchMetal', IntegerType::class, [
                'label' => 'Abholen ' . ResourceNames::METAL,
            ])
            ->add('fetchCrystal', IntegerType::class, [
                'label' => 'Abholen '  . ResourceNames::CRYSTAL,
            ])
            ->add('fetchPlastic', IntegerType::class, [
                'label' => 'Abholen '  . ResourceNames::PLASTIC,
            ])
            ->add('fetchFuel', IntegerType::class, [
                'label' => 'Abholen '  . ResourceNames::FUEL,
            ])
            ->add('fetchFood', IntegerType::class, [
                'label' => 'Abholen '  . ResourceNames::FOOD,
            ])
            ->add('fetchPower', IntegerType::class, [
                'label' => 'Abholen '  . ResourceNames::POWER,
            ])
            ->add('fetchPeople', IntegerType::class, [
                'label' => 'Abholen Passagiere',
            ])
            ->add('ships', CollectionType::class, [
                'entry_type' => FleetShipType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => false,
            ])
        ;
    }
}
