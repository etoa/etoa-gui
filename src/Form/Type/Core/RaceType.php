<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Race\RaceDataRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class RaceType extends AbstractType
{
    public function __construct(
        private RaceDataRepository $raceDataRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'choices' => $this->raceDataRepository->getRaceNames(),
            'choice_value' => 'id',
            'choice_label' => 'name',
            'constraints' => [
                new NotNull(['message'=>'Bitte Rasse auswählen!']),
            ],
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
