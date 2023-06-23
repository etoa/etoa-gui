<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceLoader;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipType extends AbstractType
{
    public function __construct(
        private ShipDataRepository $shipDataRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'search' => null,
            'choice_loader' => function (Options $options): ChoiceLoader {
                return ChoiceList::lazy($this, function () use ($options): array {
                    $search = $options->offsetGet('search');
                    if (!$search instanceof ShipSearch) {
                        $search = ShipSearch::create();
                    }

                    return array_flip($this->shipDataRepository->searchShipNames($search));
                });
            },
            'choices' => array_flip($this->shipDataRepository->getShipNames()),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
