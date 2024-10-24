<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Entity\UserMulti;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultiViewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('multiUserNick', TextType::class, [
                'attr'=>[
                    'maxlength'=>"20",
                    'size'=>"20",
                    'readonly' => "readonly"
                ]
            ])
            ->add('reason', TextType::class, [
                'attr'=>[
                    'maxlength'=>"50",
                    'size'=>"50",
                    'readonly' => "readonly"
                ]
            ])
            ->add('delMulti', CheckboxType::class, ['mapped' => false,'required'=>false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserMulti::class,
        ]);
    }
}