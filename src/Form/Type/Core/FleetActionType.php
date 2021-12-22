<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FleetActionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        // cleanup once reworked
        if (!defined('INVADE_ACTIVE_USER')) {
            define("INVADE_ACTIVE_USER", 0);
        }

        $resolver->setDefaults([
            'required' => false,
            'choices' => \FleetAction::getAll(),
            'choice_value' => fn ($action) => is_string($action) ? $action : $action?->code(),
            'choice_label' => fn (?\FleetAction $choice, $key, $value) => $choice?->name(),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
