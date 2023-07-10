<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Fleet\FleetAction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceLoader;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FleetActionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'choice_loader' => function (Options $options): ChoiceLoader {
                return ChoiceList::lazy($this, function (): array {
                    $actions = FleetAction::getAll();

                    $choices = [];
                    foreach ($actions as $action) {
                        $choices[$action->name()] = $action->code();
                    }

                    return $choices;
                });
            },
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
