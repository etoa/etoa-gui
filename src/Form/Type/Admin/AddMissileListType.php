<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\EntityType;
use EtoA\Form\Type\Core\MissileType;
use EtoA\Universe\Entity\EntityLabelSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AddMissileListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('entityId', EntityType::class, [
                'placeholder' => false,
                'label' => 'Entity',
                'search' => EntityLabelSearch::create()
                    ->codeIn([\EtoA\Universe\Entity\EntityType::PLANET])
                    ->planetUserIdNotNull(),
            ])
            ->add('missileId', MissileType::class, [
                'placeholder' => false,
                'label' => 'Rakete',
            ])
            ->add('count', IntegerType::class, [
                'label' => 'Anzahl',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Hinzufügen',
            ])
        ;
    }
}
