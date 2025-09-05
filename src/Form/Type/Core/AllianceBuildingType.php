<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Alliance\AllianceBuildingRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllianceBuildingType extends AbstractType
{
    public function __construct(
        private readonly AllianceBuildingRepository $allianceBuildingRepository
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'required' => true,
                'choices' => $this->allianceBuildingRepository->findBy(['show'=>true]),
                'choice_value' => 'id',
                'choice_label' => 'name'
            ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
