<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Entity\AllianceApplication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllianceApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('answer', TextAreaType::class, [
                'attr'=>[
                    'rows'=>"6",
                    'cols'=>"40",
                ],
                'mapped' => false,
                'required' =>false
            ])
            ->add('action',ChoiceType::class, [
                    'choices' => [
                        'yes'=>2,'no'=>1,'maybe'=>0
                    ],
                    'multiple'=>false,
                    'expanded'=>true,
                    'mapped' => false,
                    'data' => '1'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AllianceApplication::class,
        ]);
    }
}