<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Entity\ShipListItem;
use EtoA\Support\StringUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BunkerShipCountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            $event
                ->getForm()
                ->add(
                    'bunkered',TextType::class, [
                    'label' => false,
                    'attr' => [
                        'size' => 10,
                        'onkeyup'=> "FormatNumber(this.id,this.value,'".$event->getData()->getBunkered(). "','','')"
                    ],
                ]);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $data['bunkered'] = StringUtils::parseFormattedNumber($data['bunkered']);

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShipListItem::class,
        ]);
    }
}