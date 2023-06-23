<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Log\FleetLogFacility;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogFleetFacilityType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'choices' => array_flip(FleetLogFacility::FACILITIES),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
