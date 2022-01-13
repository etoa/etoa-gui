<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\BuildingType;
use EtoA\Form\Type\Core\EntityType;
use EtoA\Universe\Entity\EntitySearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AddBuildingItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('buildingId', BuildingType::class, [
                'label' => 'Gebäude',
                'placeholder' => false,
                'required' => true,
            ])
            ->add('entityId', EntityType::class, [
                'label' => 'Entity',
                'placeholder' => false,
                'required' => true,
                'search' => EntitySearch::create()->codeIn([\EtoA\Universe\Entity\EntityType::PLANET]),
            ])
            ->add('currentLevel', IntegerType::class, [
                'label' => 'Level',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Hinzufügen',
            ])
        ;
    }
}
