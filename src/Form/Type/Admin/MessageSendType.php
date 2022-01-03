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
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'admin_player_id' => null,
            ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fromChoices = ['System' => 0];
        if ($options['admin_player_id'] > 0) {
            $playerNick = $this->userRepository->getNick($options['admin_player_id']);
            if ($playerNick !== null) {
                $fromChoices[$playerNick] = $options['admin_player_id'];
            }
        }

        $builder
            ->add('fromId', ChoiceType::class, [
                'label' => 'Sender',
                'choices' => $fromChoices,
            ])
            ->add('userId', UserType::class, [
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
            ])
            ->add('subject', TextType::class, [
                'label' => 'Betreff',
            ])
            ->add('text', TextareaType::class)
            ->add('send', SubmitType::class)
            ;
    }
}
