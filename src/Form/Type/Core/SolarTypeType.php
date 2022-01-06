<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Universe\Star\SolarTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceLoader;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SolarTypeType extends AbstractType
{
    public function __construct(
        private SolarTypeRepository $solarTypeRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => false,
            'show_all' => false,
            'choice_loader' => function (Options $options): ChoiceLoader {
                return ChoiceList::lazy($this, function () use ($options): array {
                    return array_flip($this->solarTypeRepository->getSolarTypeNames($options['show_all']));
                });
            },
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
