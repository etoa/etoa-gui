<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Universe\Entity\EntityLabelSearch;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
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
            'with_code_string' => true,
            'placeholder' => '(Alle)',
            'choice_loader' => function (Options $options): ChoiceLoader {
                return ChoiceList::lazy($this, function () use ($options): array {
                    $search = $options->offsetGet('search');
                    if (!$search instanceof EntitySearch) {
                        $search = EntityLabelSearch::create();
                    }

                    $entries = $this->entityRepository->searchEntityLabels($search);
                    $choices = [];
                    foreach ($entries as $entry) {
                        $labelPrefix = (bool) $options['with_code_string'] ? $entry->codeString() . ' ' : '';
                        $choices[$labelPrefix . $entry->toStringWithOwner()] = $entry->id;
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
