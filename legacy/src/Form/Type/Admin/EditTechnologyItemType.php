<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\TechnologyBuildTypeType;
use EtoA\Form\Type\Core\TimestampType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class EditTechnologyItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentLevel', IntegerType::class, [
                'label' => 'Level',
            ])
            ->add('buildType', TechnologyBuildTypeType::class, [
                'label' => 'Status',
                'required' => true,
                'placeholder' => false,
            ])
            ->add('startTime', TimestampType::class, [
                'label' => 'Start',
            ])
            ->add('endTime', TimestampType::class, [
                'label' => 'Ende',
            ]);
    }
}
