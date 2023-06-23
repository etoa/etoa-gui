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
