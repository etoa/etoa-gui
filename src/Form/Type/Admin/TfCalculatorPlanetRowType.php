<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\EntityType;
use EtoA\Universe\Entity\EntityLabelSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class TfCalculatorPlanetRowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('planet', EntityType::class, [
                'required' => true,
                'placeholder' => false,
                'search' => EntityLabelSearch::create()->codeIn([\EtoA\Universe\Entity\EntityType::PLANET])->planetUserIdNotNull(),
            ])
            ->add('percentage', IntegerType::class, [
                'label' => 'Anteil %',
                'data' => 0,
                'attr' => [
                    'data-target' => 'percentage',
                ],
            ])
            ->add('metal', IntegerType::class, [
                'label' => 'Anteil Titan',
                'attr' => [
                    'data-target' => 'metal',
                ],
            ])
            ->add('crystal', IntegerType::class, [
                'label' => 'Anteil Silizium',
                'attr' => [
                    'data-target' => 'crystal',
                ],
            ])
            ->add('plastic', IntegerType::class, [
                'label' => 'Anteil PVC',
                'attr' => [
                    'data-target' => 'plastic',
                ],
            ]);
    }
}
