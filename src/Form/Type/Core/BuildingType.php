<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Building\BuildingDataRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuildingType extends AbstractType
{
    public function __construct(
        private BuildingDataRepository $buildingDataRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'choices' => array_flip($this->buildingDataRepository->getBuildingNames()),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
