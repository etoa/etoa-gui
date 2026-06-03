<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Entity\MissileListItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissileItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            $event
                ->getForm()
                ->add(
                    'count',TextType::class, [
                    'label' => false,
                    'attr' => [
                        'size' => 4,
                        'onkeyup'=> "FormatNumber(this.id,this.value,'".$event->getData()->getCount(). "','','')",
                        'data-action' => "live#action",
                        'data-live-action-param' => "debounce(2000)|updateByField"
                    ],
                    'data' => 0,
                    'mapped' => false
                ])
                ->add(
                    'id',TextType::class, [
                    'data' => $event->getData()->getMissile()->getId(),
                    'mapped' => false
                ])
                ->add(
                    'speed',TextType::class, [
                    'data' => $event->getData()->getMissile()->getSpeed(),
                    'mapped' => false
                ])
                ->add(
                    'range',TextType::class, [
                    'data' => $event->getData()->getMissile()->getRange(),
                    'mapped' => false
                ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MissileListItem::class,
        ]);
    }
}