<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\CellType;
use EtoA\Form\Type\Core\EntityType;
use EtoA\Form\Type\Core\EntityTypeType;
use EtoA\Form\Type\Core\UserType;
use EtoA\Form\Type\Core\YesNoMaybeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EntitySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('code', EntityTypeType::class, [
                'label' => 'Entitätstyp',
            ])
            ->add('cell', CellType::class, [
                'label' => 'Koordinaten',
            ])
            ->add('user', UserType::class, [
                'label' => 'Besitzer',
            ])
            ->add('entity', EntityType::class, [
                'label' => 'Entity',
            ])
            ->add('isMainPlanet', YesNoMaybeType::class, [
                'label' => 'Hauptplanet',
            ])
            ->add('planetDebris', YesNoMaybeType::class, [
                'label' => 'Trümmerfeld',
            ]);
    }
}
