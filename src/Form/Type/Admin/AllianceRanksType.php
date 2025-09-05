<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Alliance\AllianceRankRepository;
use EtoA\Entity\AllianceRank;
use EtoA\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllianceRanksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            $event
                ->getForm()
                ->add('name', TextType::class, [
                    'attr' => [
                        'size' => 35
                    ]
                ])
                ->add('level', ChoiceType::class, [
                    'choices' => range(0, 9),
                ])
                ->add('delete', CheckboxType::class, [
                    'required' => false,
                    'mapped' => false
                ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AllianceRank::class,
        ]);
    }
}