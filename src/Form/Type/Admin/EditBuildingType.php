<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\BuildingBuildTypeType;
use EtoA\Form\Type\Core\TimestampType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class EditBuildingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentLevel', IntegerType::class, [
                'label' => false,
                'attr' => ['size' => 4],
            ])
            ->add('buildType', BuildingBuildTypeType::class, [
                'label' => false,
            ])
            ->add('startTime', TimestampType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('endTime', TimestampType::class, [
                'label' => false,
                'required' => false,
            ])
        ;
    }
}
