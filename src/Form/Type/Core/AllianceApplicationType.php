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
    public const ACTION_IGNORE = 0;
    public const ACTION_REJECT = 1;
    public const ACTION_ACCEPT = 2;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('answer', TextareaType::class, [
                'attr'=>[
                    'rows'=>"6",
                    'cols'=>"40",
                ],
                'mapped' => false,
                'required' =>false
            ])
            ->add('action',ChoiceType::class, [
                    'choices' => [
                        'Annehmen' => self::ACTION_ACCEPT,
                        'Ablehnen' => self::ACTION_REJECT,
                        'Nicht bearbeiten' => self::ACTION_IGNORE,
                    ],
                    'multiple'=>false,
                    'expanded'=>true,
                    'mapped' => false,
                    'data' => self::ACTION_IGNORE
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