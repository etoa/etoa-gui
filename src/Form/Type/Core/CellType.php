<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Universe\Cell\CellRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CellType extends AbstractType
{
    public function __construct(
        private CellRepository $cellRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $cells = $this->cellRepository->findAllCoordinates();
        $choices = [];
        foreach ($cells as $cell) {
            $choices[$cell->toString()] = $cell->id;
        }

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'choices' => $choices,
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
