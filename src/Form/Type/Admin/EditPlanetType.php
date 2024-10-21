<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Entity\Planet;
use EtoA\Form\Type\Core\AdjustResourceType;
use EtoA\Form\Type\Core\PlanetImageType;
use EtoA\Form\Type\Core\PlanetTypeType;
use EtoA\Form\Type\Core\UserType;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EditPlanetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Planet $data */
        $data = $options['data'];

        $builder
            ->add('name', TextType::class, [
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Beschreibung',
                'required' => false,
            ])
            ->add('typeId', PlanetTypeType::class, [
                'label' => 'Type',
                'show_all' => true,
            ])
            ->add('userId', UserType::class, [
                'label' => 'Besitzer',
            ])
            ->add('mainPlanet', CheckboxType::class, [
                'required' => false,
                'label' => 'Hauptplanet',
            ])
            ->add('userChanged', DateTimeType::class, [
                'required' => false,
                'input' => 'timestamp',
                'label' => 'Letzer Besitzerwechsel',
                'disabled' => 'disabled',
            ])
            ->add('resetUserChanged', CheckboxType::class, [
                'required' => false,
                'label' => 'Reset Letzer Besitzerwechsel',
                'mapped' => false,
            ])
            ->add('fields', IntegerType::class, [
                'label' => 'Felder',
            ])
            ->add('fieldsExtra', IntegerType::class, [
                'label' => 'Extra-Felder',
            ])
            ->add('fieldsUsed', IntegerType::class, [
                'label' => 'Felder benutzt',
                'disabled' => 'disabled',
            ])
            ->add('tempFrom', IntegerType::class, [
                'label' => 'Temperatur (von)',
            ])
            ->add('tempTo', IntegerType::class, [
                'label' => 'Temperatur (bis)',
            ])
            ->add('image', PlanetImageType::class, [
                'label' => 'Bild',
            ])
            ->add('resMetal', AdjustResourceType::class, [
                'label' => ResourceNames::METAL,
            ])
            ->add('resCrystal', AdjustResourceType::class, [
                'label' => ResourceNames::CRYSTAL,
            ])
            ->add('resPlastic', AdjustResourceType::class, [
                'label' => ResourceNames::PLASTIC,
            ])
            ->add('resFuel', AdjustResourceType::class, [
                'label' => ResourceNames::FUEL,
            ])
            ->add('resFood', AdjustResourceType::class, [
                'label' => ResourceNames::FOOD,
            ])
            ->add('people', AdjustResourceType::class, [
                'label' => 'Bewohner',
            ])
            ->add('prodMetal', IntegerType::class, [
                'label' => 'Produktion ' . ResourceNames::METAL,
                'disabled' => 'disabled',
            ])
            ->add('prodCrystal', IntegerType::class, [
                'label' => 'Produktion ' . ResourceNames::CRYSTAL,
                'disabled' => 'disabled',
            ])
            ->add('prodPlastic', IntegerType::class, [
                'label' => 'Produktion ' . ResourceNames::PLASTIC,
                'disabled' => 'disabled',
            ])
            ->add('prodFuel', IntegerType::class, [
                'label' => 'Produktion ' . ResourceNames::FUEL,
                'disabled' => 'disabled',
            ])
            ->add('prodFood', IntegerType::class, [
                'label' => 'Produktion ' . ResourceNames::FOOD,
                'disabled' => 'disabled',
            ])
            ->add('prodPower', IntegerType::class, [
                'label' => 'Produktion ' . ResourceNames::POWER,
                'disabled' => 'disabled',
            ])
            ->add('usePower', IntegerType::class, [
                'label' => 'Verbrauch ' . ResourceNames::POWER,
                'disabled' => 'disabled',
            ])
            ->add('prodPeople', IntegerType::class, [
                'label' => 'Bevölkerungswachstum',
                'disabled' => 'disabled',
            ])
            ->add('peoplePlace', IntegerType::class, [
                'label' => 'Wohnraum',
                'disabled' => 'disabled',
            ])
            ->add('storeMetal', IntegerType::class, [
                'label' => 'Speicher ' . ResourceNames::METAL,
                'disabled' => 'disabled',
            ])
            ->add('storeCrystal', IntegerType::class, [
                'label' => 'Speicher ' . ResourceNames::CRYSTAL,
                'disabled' => 'disabled',
            ])
            ->add('storePlastic', IntegerType::class, [
                'label' => 'Speicher ' . ResourceNames::PLASTIC,
                'disabled' => 'disabled',
            ])
            ->add('storeFuel', IntegerType::class, [
                'label' => 'Speicher ' . ResourceNames::FUEL,
                'disabled' => 'disabled',
            ])
            ->add('storeFood', IntegerType::class, [
                'label' => 'Speicher ' . ResourceNames::FOOD,
                'disabled' => 'disabled',
            ])
            ->add('wfMetal', IntegerType::class, [
                'label' => 'Trümmerfeld ' . ResourceNames::METAL,
            ])
            ->add('wfCrystal', IntegerType::class, [
                'label' => 'Trümmerfeld ' . ResourceNames::CRYSTAL,
            ])
            ->add('wfPlastic', IntegerType::class, [
                'label' => 'Trümmerfeld ' . ResourceNames::PLASTIC,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Übernehmen',
            ])
        ;
    }
}
