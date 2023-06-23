<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Planet\PlanetTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceLoader;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanetImageType extends AbstractType
{
    public function __construct(
        private PlanetTypeRepository $planetTypeRepository,
        private ConfigurationService $config
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => false,
            'choice_loader' => function (Options $options): ChoiceLoader {
                return ChoiceList::lazy($this, function (): array {
                    $typeNames = $this->planetTypeRepository->getPlanetTypeNames(true);
                    $choices = [];
                    $max = $this->config->getInt('num_planet_images');
                    foreach ($typeNames as $typeId => $typeName) {
                        for ($x = 1; $x <= $max; $x++) {
                            $choices[$typeName . ' ' . $x] = $typeId . '_' . $x;
                        }
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
