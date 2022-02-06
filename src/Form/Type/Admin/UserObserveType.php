<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class UserObserveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userId', UserType::class, [
                'label' => 'Spieler',
                'placeholder' => 'Spieler auswählen',
            ])
            ->add('reason', TextareaType::class, [
                'label' => 'Grund',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Zur Beobachtungsliste hinzufügen',
            ]);
    }
}
