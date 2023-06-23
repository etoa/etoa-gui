<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\EntityType;
use EtoA\Form\Type\Core\PositiveIntegerType;
use EtoA\Form\Type\Core\ShipType;
use EtoA\Universe\Entity\EntityLabelSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AddShipListType extends AbstractType
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
            ->add('shipId', ShipType::class, [
                'placeholder' => false,
                'label' => 'Schiff',
            ])
            ->add('count', PositiveIntegerType::class, [
                'label' => 'Anzahl',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Hinzufügen',
            ])
        ;
    }
}
