<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\UserType;
use EtoA\Message\AdminMessageRequest;
use EtoA\User\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageSendType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'admin_player' => null,
            ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('from', UserType::class, [
                'label' => 'Sender',
                'data' => $options['admin_player'],
                'placeholder' => 'System',
            ])
            ->add('user', UserType::class, [
                'label' => 'Empfänger',
                'placeholder' => '(Alle Spieler)',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Typ',
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'InGame-Nachricht' => AdminMessageRequest::MESSAGE_TYPE_IN_GAME,
                    'E-Mail' => AdminMessageRequest::MESSAGE_TYPE_EMAIL,
                    'InGame-Nachricht & E-Mail' => AdminMessageRequest::MESSAGE_TYPE_BOTH,
                ],
                'data' => AdminMessageRequest::MESSAGE_TYPE_IN_GAME
            ])
            ->add('subject', TextType::class, [
                'label' => 'Betreff',
            ])
            ->add('text', TextareaType::class)
            ->add('send', SubmitType::class)
        ;
    }
}
