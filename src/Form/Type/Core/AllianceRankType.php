<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Entity\AllianceRank;
use EtoA\Entity\AllianceRight;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllianceRankType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,
                ['required'=>false]
            )
            ->add('level', TextType::class, [
                'attr' => [
                  'maxlength' => 1,
                  'size' => 2
                ],
                'empty_data' => 0
            ])
            ->add('rights', EntityType::class, [
                'class' => AllianceRight::class,
                'choice_label' => 'description',
                'choice_value' => 'id',
                'expanded' => true,
                'multiple' => true,
                'required' => false
            ])
            ->add('delete', CheckboxType::class, [
                'label'    => false,
                'required' => false,
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AllianceRank::class,
        ]);
    }
}