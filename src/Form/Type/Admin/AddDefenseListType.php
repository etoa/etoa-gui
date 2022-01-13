<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\DefenseType;
use EtoA\Form\Type\Core\EntityType;
use EtoA\Universe\Entity\EntityLabelSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AddDefenseListType extends AbstractType
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
            ->add('defenseId', DefenseType::class, [
                'placeholder' => false,
                'label' => 'Verteidigung',
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
