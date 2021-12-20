<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Log\LogFacility;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogFacilityType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'choices' => array_flip(LogFacility::FACILITIES),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
