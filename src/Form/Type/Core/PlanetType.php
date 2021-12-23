<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Universe\Entity\EntityLabelSearch;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanetType extends AbstractType
{
    public function __construct(
        private EntityRepository $entityRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $planets = $this->entityRepository->searchEntityLabels(EntityLabelSearch::create()->codeIn([EntityType::PLANET]));
        $choices = [];
        foreach ($planets as $planet) {
            $choices[$planet->toString()] = $planet->id;
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
