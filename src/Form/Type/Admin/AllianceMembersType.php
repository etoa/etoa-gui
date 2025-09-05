<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Alliance\AllianceRankRepository;
use EtoA\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllianceMembersType extends AbstractType
{


    public function __construct(
        private readonly AllianceRankRepository $allianceRankRepository
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            $event
                ->getForm()
                ->add('allianceRank', ChoiceType::class, [
                    'choices' => $this->allianceRankRepository->findBy(['alliance'=>$event->getData()->getAlliance()]),
                    'choice_value' => 'id',
                    'choice_label' => 'name',
                ])
                ->add('kick', CheckboxType::class, [
                    'required' => false,
                    'mapped' => false
                ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}