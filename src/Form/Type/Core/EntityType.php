<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Universe\Entity\EntityLabelSearch;
use EtoA\Universe\Entity\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceLoader;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityType extends AbstractType
{
    public function __construct(
        private EntityRepository $entityRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'search' => null,
            'placeholder' => '(Alle)',
            'choice_loader' => function (Options $options): ChoiceLoader {
                return ChoiceList::lazy($this, function () use ($options): array {
                    $search = $options->offsetGet('search');
                    if (!$search instanceof EntityLabelSearch) {
                        $search = EntityLabelSearch::create();
                    }

                    $entries = $this->entityRepository->searchEntityLabels($search);
                    $choices = [];
                    foreach ($entries as $entry) {
                        $choices[$entry->toStringWithOwner()] = $entry->id;
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
