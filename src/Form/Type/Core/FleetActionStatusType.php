<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FleetActionStatusType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'label' => false,
            'choices' => array_flip(\FleetAction::$statusCode),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
