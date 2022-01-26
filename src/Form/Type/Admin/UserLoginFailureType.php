<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserLoginFailureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userId', UserType::class, [
                'label' => 'Spieler',
            ])
            ->add('ip', TextType::class, [
                'label' => 'IP-Adresse',
            ])
            ->add('host', TextType::class, [
                'label' => 'Host',
            ])
            ->add('client', TextType::class, [
                'label' => 'Client',
            ]);
    }
}
