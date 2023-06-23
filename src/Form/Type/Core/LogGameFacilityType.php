<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Log\GameLogFacility;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogGameFacilityType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'choices' => array_flip(GameLogFacility::FACILITIES),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
