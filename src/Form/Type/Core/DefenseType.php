<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Defense\DefenseDataRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefenseType extends AbstractType
{
    public function __construct(
        private DefenseDataRepository $defenseDataRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'choices' => array_flip($this->defenseDataRepository->getDefenseNames()),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
