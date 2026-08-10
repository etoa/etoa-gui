<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchableChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'row_attr' => [
                'data-controller' => 'searchable-choice',
                // Choices.js replaces the <select> with its own markup. Without this a live
                // component re-render morphs that markup against the plain <select> the server
                // sent and ends up inserting a second widget next to the existing one.
                'data-live-ignore' => true,
            ],
            'attr' => [
                'data-searchable-choice-target' => 'input',
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
