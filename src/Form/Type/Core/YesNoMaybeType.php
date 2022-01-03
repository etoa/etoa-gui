<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class YesNoMaybeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'multiple' => false,
            'expanded' => true,
            'choices' => [
                'Ja' => 1,
                'Nein' => 0,
                'Egal' => '',
            ],
            'data' => '',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
