<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Building\BuildingDataRepository;
use EtoA\Technology\TechnologyDataRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequirementItemType extends AbstractType
{
    public function __construct(
        private TechnologyDataRepository $technologyDataRepository,
        private BuildingDataRepository $buildingDataRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => 'Anforderung wählen',
            'choices' => [
                'Gebäude' => array_map(fn (int $id) => 'b:'.$id, array_flip($this->buildingDataRepository->getBuildingNames())),
                'Technologien' => array_map(fn (int $id) => 't:'.$id, array_flip($this->technologyDataRepository->getTechnologyNames())),
            ],
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
