<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

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
                'type' => ''
            ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($builder->getData() as $object) {
            $builder
                ->add('object-' . $object->getId(), CollectionType::class, [
                    'entry_type' => ObjectRequirementType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => false,
                    'label' => false,
                    'data' => $object->getObjectRequirements(),
                    'entry_options' => [
                        'data_class' => $options['type'],
                    ],
                ]);
        }
    }
}
