<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\User\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerType extends AbstractType
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Niemand)',
            'choices' => array_flip($this->userRepository->searchUserNicknames()),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
