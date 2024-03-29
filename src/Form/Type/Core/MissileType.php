<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Missile\MissileDataRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissileType extends AbstractType
{
    public function __construct(
        private MissileDataRepository $missileDataRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'choices' => array_flip($this->missileDataRepository->getMissileNames(true)),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
