<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\FleetActionType;
use EtoA\Form\Type\Core\TimestampType;
use EtoA\Form\Type\Core\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LogAttackBanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', TimestampType::class)
            ->add('action', FleetActionType::class, [
                'choice_filter' => fn ($action) => is_string($action) || $action?->attitude() === 3,
            ])
            ->add('attacker', UserType::class, [
                'label' => 'Attacker',
            ])
            ->add('defender', UserType::class, [
                'label' => 'Verteidiger',
            ]);
    }
}
