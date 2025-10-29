<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\BuildingType;
use EtoA\Form\Type\Core\PositiveIntegerType;
use EtoA\Form\Type\Core\TechnologyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectRequirementType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('obj', HiddenType::class, [
                'property_path' => 'obj.id'
            ])
            ->add('building', BuildingType::class)
            ->add('tech', TechnologyType::class)
            ->add('level', PositiveIntegerType::class, [
                'label' => false,
            ]);
    }
}
