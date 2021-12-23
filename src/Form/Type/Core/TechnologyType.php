<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Technology\TechnologyDataRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TechnologyType extends AbstractType
{
    public function __construct(
        private TechnologyDataRepository $technologyDataRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '',
            'choices' => array_flip($this->technologyDataRepository->getTechnologyNames()),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
