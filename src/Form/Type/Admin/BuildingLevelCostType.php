<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\BuildingType;
use EtoA\Form\Type\Core\PositiveIntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BuildingLevelCostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('itemId', BuildingType::class, [
                'label' => false,
                'required' => true,
                'placeholder' => 'Gebäude auswählen',
            ])
            ->add('level', PositiveIntegerType::class, [
                'label' => false,
                'required' => true,
            ]);
    }
}
