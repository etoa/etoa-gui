<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\PositiveIntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class NewDefaultItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('object', DefaultItemType::class, [
                'required' => true
            ])
            ->add('count', PositiveIntegerType::class, [
                'label' => 'Stufe/Anzahl',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Hinzufügen',
            ]);
    }
}
