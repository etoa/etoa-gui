<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\FleetActionStatusType;
use EtoA\Form\Type\Core\FleetActionType;
use EtoA\Form\Type\Core\LogFleetFacilityType;
use EtoA\Form\Type\Core\LogSeverityType;
use EtoA\Form\Type\Core\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LogFleetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('severity', LogSeverityType::class, [
                'label' => 'Ab Schweregrad',
            ])
            ->add('facility', LogFleetFacilityType::class, [
                'label' => 'Kategorie',
            ])
            ->add('action', FleetActionType::class)
            ->add('status', FleetActionStatusType::class)
            ->add('fleetUser', UserType::class, [
                'placeholder' => '(Alle)',
                'label' => 'Flottenuser',
            ])
            ->add('entityUser', UserType::class, [
                'placeholder' => '(Alle)',
                'label' => 'Entityuser',
            ]);
    }
}
