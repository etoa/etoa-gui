<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\BuildingBuildTypeType;
use EtoA\Form\Type\Core\BuildingType;
use EtoA\Form\Type\Core\EntityType;
use EtoA\Form\Type\Core\UserType;
use EtoA\Universe\Entity\EntityLabelSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BuildingSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userId', UserType::class, [
                'label' => 'Spieler',
            ])
            ->add('entityId', EntityType::class, [
                'label' => 'Entity',
                'search' => EntityLabelSearch::create()
                    ->codeIn([\EtoA\Universe\Entity\EntityType::PLANET])
                    ->planetUserIdNotNull(),
            ])
            ->add('buildingId', BuildingType::class, [
                'label' => 'Gebäude',
            ])
            ->add('buildType', BuildingBuildTypeType::class, [
                'label' => 'Status',
            ])
        ;
    }
}
