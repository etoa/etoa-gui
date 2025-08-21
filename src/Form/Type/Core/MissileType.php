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
            'choices' => $this->missileDataRepository->getMissileNames(true),
            'choice_value' => 'id',
            'choice_label' => 'name'
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
