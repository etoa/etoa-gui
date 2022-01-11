<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\PositiveIntegerType;
use EtoA\Form\Type\Core\RequirementItemType;
use EtoA\Requirement\ObjectRequirement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectRequirementType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ObjectRequirement::class,
            'label' => false,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objectId', HiddenType::class)
            ->add('requiredId', RequirementItemType::class, [
                'label' => false,
            ])
            ->add('requiredLevel', PositiveIntegerType::class, [
                'label' => false,
            ]);
    }
}
