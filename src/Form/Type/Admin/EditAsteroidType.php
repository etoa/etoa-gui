<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\AdjustResourceType;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class EditAsteroidType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ->add('resPower', AdjustResourceType::class, [
                'label' => ResourceNames::POWER,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Übernehmen',
            ]);
    }
}
