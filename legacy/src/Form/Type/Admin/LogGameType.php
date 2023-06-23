<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\AllianceType;
use EtoA\Form\Type\Core\BuildingType;
use EtoA\Form\Type\Core\DefenseType;
use EtoA\Form\Type\Core\LogGameFacilityType;
use EtoA\Form\Type\Core\LogSeverityType;
use EtoA\Form\Type\Core\EntityType;
use EtoA\Form\Type\Core\ShipType;
use EtoA\Form\Type\Core\TechnologyType;
use EtoA\Form\Type\Core\UserType;
use EtoA\Log\GameLogFacility;
use EtoA\Universe\Entity\EntityLabelSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LogGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('severity', LogSeverityType::class, [
                'label' => 'Ab Schweregrad',
            ])
            ->add('facility', LogGameFacilityType::class, [
                'label' => 'Bereich',
            ]);

        switch ($options['data']['facility'] ?? '') {
            case GameLogFacility::BUILD:
                $builder->add('object', BuildingType::class);

                break;
            case GameLogFacility::TECH:
                $builder->add('object', TechnologyType::class);

                break;
            case GameLogFacility::SHIP:
                $builder->add('object', ShipType::class);

                break;
            case GameLogFacility::DEF:
                $builder->add('object', DefenseType::class);

                break;
        }

        $builder
            ->add('query', TextType::class, [
                'label' => 'Suchtext',
            ])
            ->add('user', UserType::class)
            ->add('alliance', AllianceType::class, [
                'label' => 'Allianz',
            ])
            ->add('entity', EntityType::class, [
                'label' => 'Planet',
                'search' => EntityLabelSearch::create()->codeIn([\EtoA\Universe\Entity\EntityType::PLANET]),
            ]);
    }
}
