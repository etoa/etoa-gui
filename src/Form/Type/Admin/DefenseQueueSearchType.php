<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\DefenseType;
use EtoA\Form\Type\Core\EntityType;
use EtoA\Form\Type\Core\UserType;
use EtoA\Universe\Entity\EntityLabelSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DefenseQueueSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('entityId', EntityType::class, [
                'label' => 'Planet',
                'search' => EntityLabelSearch::create()
                    ->codeIn([\EtoA\Universe\Entity\EntityType::PLANET])
                    ->planetUserIdNotNull(),
            ])
            ->add('userId', UserType::class, [
                'label' => 'Spieler',
            ])
            ->add('defenseId', DefenseType::class, [
                'label' => 'Verteidigung',
            ]);
    }
}
