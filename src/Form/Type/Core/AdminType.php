<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Admin\AdminUserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminType extends AbstractType
{
    public function __construct(
        private readonly AdminUserRepository $userRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '',
            'choices' => $this->userRepository->findAll(),
            'choice_value' => 'id',
            'choice_label' => 'nick',
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
