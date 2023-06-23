<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\EntityType;
use EtoA\Universe\Entity\EntityLabelSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class EditWormholeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('changed', DateTimeType::class, [
                'label' => 'Entstanden',
                'disabled' => 'disabled',
                'input' => 'timestamp',
            ])
            ->add('targetId', EntityType::class, [
                'label' => 'Ziel',
                'disabled' => 'disabled',
                'search' => EntityLabelSearch::create()->codeIn([\EtoA\Universe\Entity\EntityType::WORMHOLE]),
            ])
            ->add('persistent', ChoiceType::class, [
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
                'expanded' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Übernehmen',
            ]);
    }
}
