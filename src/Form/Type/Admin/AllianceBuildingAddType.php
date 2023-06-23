<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\AllianceBuildingType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AllianceBuildingAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('buildingId', AllianceBuildingType::class, [
                'label' => 'Gebäude',
            ])
            ->add('level', IntegerType::class, [
                'label' => 'Stufe',
                'data' => 1,
            ])
            ->add('memberFor', IntegerType::class, [
                'label' => 'Useranzahl',
                'data' => 1,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Hinzufügen',
            ]);
    }
}
