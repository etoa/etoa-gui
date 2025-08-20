<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\BuildingBuildTypeType;
use EtoA\Form\Type\Core\BuildingType;
use EtoA\Form\Type\Core\UserType;
use EtoA\Universe\Entity\EntityLabelSearch;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class BuildingSearchType extends AbstractType
{
    public function __construct(
        private readonly EntityRepository $entityRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserType::class, [
                'label' => 'Spieler',
            ])
            //TODO: use EntityType without inheriting the choices from the addBuilding form
            ->add('entity', ChoiceType::class, [
                'required' => false,
                'label' => 'Entity',
                'placeholder' => '(Alle)',
                'choice_loader' => new CallbackChoiceLoader(function (): array {
                    $search = EntityLabelSearch::create()
                        ->codeIn([EntityType::PLANET])
                        ->planetUserIdNotNull();

                    $entries = $this->entityRepository->searchEntityLabels($search);
                    $choices = [];
                    foreach ($entries as $entry) {
                        $choices[$entry->toString() .' '. $entry->displayName()] = $entry->getPlanet();
                    }

                    return $choices;
                }),
            ])
            ->add('building', BuildingType::class, [
                'label' => 'Gebäude',
            ])
            ->add('buildType', BuildingBuildTypeType::class, [
                'label' => 'Status',
            ])
        ;
    }
}
