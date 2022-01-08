<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Technology\TechnologyBuildType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TechnologyBuildTypeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'choices' => array_flip(TechnologyBuildType::all()),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
