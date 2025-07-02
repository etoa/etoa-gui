<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CountType extends AbstractType
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
                        'size' => 10,
                        'onkeyup'=> "FormatNumber(this.id,this.value,'".$event->getData()->getCount(). "','','')"
                    ],
                    'mapped' => false,
                    'data' => 0
                ]);
        });
    }
}