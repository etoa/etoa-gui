<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Realer Name',
                'attr' => [
                    'size' => 40,
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-Mail',
                'attr' => [
                    'size' => 40,
                ],
            ])
            ->add('boardUrl', UrlType::class, [
                'required' => false,
                'label' => 'Forum-Profil',
                'attr' => [
                    'size' => 60,
                ],
            ])
            ->add('ticketEmail', ChoiceType::class, [
                'label' => 'Mail bei Ticket',
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
            ])
            ->add('playerId', UserType::class, [
                'label' => 'Spieler-Account',
                'placeholder' => '(Niemand)',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Übernehmen',
            ]);
    }
}
