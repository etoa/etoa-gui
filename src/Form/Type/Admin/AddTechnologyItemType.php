<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\EntityType;
use EtoA\Form\Type\Core\TechnologyType;
use EtoA\Universe\Entity\EntityLabelSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AddTechnologyItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('technologyId', TechnologyType::class, [
                'label' => 'Forschung',
                'placeholder' => false,
                'required' => true,
            ])
            ->add('all', CheckboxType::class, [
                'label' => 'Alle Techs',
                'required' => false,
                'mapped' => false,
            ])
            ->add('entityId', EntityType::class, [
                'label' => 'Entity',
                'placeholder' => false,
                'required' => true,
                'search' => EntityLabelSearch::create()->codeIn([\EtoA\Universe\Entity\EntityType::PLANET])->planetUserIdNotNull(),
            ])
            ->add('currentLevel', IntegerType::class, [
                'label' => 'Level',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Hinzufügen',
            ])
        ;
    }
}
