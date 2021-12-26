<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Alliance\AllianceRepository;
use EtoA\User\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserWithoutAllianceType extends AbstractType
{
    public function __construct(
        private AllianceRepository $allianceRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'choices' => array_flip($this->allianceRepository->listSoloUsers()),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
