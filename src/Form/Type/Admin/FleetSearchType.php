<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\EntityType;
use EtoA\Form\Type\Core\FleetActionStatusType;
use EtoA\Form\Type\Core\FleetActionType;
use EtoA\Form\Type\Core\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FleetSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('entityFrom', EntityType::class, [
                'label' => 'Startentität',
            ])
            ->add('entityTo', EntityType::class, [
                'label' => 'Zielentität',
            ])
            ->add('action', FleetActionType::class, [
                'label' => 'Flottenaktion',
            ])
            ->add('status', FleetActionStatusType::class)
            ->add('user', UserType::class, [
                'label' => 'Besitzer',
            ])
        ;
    }
}
