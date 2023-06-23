<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\EntityType;
use EtoA\Form\Type\Core\TechnologyBuildTypeType;
use EtoA\Form\Type\Core\TechnologyType;
use EtoA\Form\Type\Core\UserType;
use EtoA\Universe\Entity\EntitySearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TechnologySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userId', UserType::class, [
                'label' => 'Spieler',
            ])
            ->add('entityId', EntityType::class, [
                'label' => 'Planet',
                'search' => EntitySearch::create()->codeIn([\EtoA\Universe\Entity\EntityType::PLANET]),
            ])
            ->add('techId', TechnologyType::class, [
                'placeholder' => '(Alle)',
                'label' => 'Technology',
            ])
            ->add('buildType', TechnologyBuildTypeType::class, [
                'label' => 'Status',
            ])
        ;
    }
}
