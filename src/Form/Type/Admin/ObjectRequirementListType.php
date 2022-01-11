<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Validation\UniqueObjectRequirementConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectRequirementListType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'objectIds' => [],
                'objectNames' => [],
            ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['objectIds'] as $objectId) {
            $builder
                ->add('object-' .$objectId, CollectionType::class, [
                    'entry_type' => ObjectRequirementType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => false,
                    'label' => false,
                    'property_path' => sprintf('[%s]', $objectId),
                    'constraints' => [new UniqueObjectRequirementConstraint(['objectNames' => $options['objectNames']])],
                ]);
        }
    }
}
