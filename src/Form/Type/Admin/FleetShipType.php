<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Entity\FleetShip;
use EtoA\Form\Type\Core\ShipType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FleetShipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('count', IntegerType::class, [
                'label' => false,
            ])
            ->add('ship', ShipType::class, [
                'label' => false,
                'required' => true,
                'placeholder' => false,
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options) {
            /** @var FleetShip $fleetShip */
            $fleetShip = $event->getData();

            if ($fleetShip && $options['fleet']) {
                $fleetShip->setFleet($options['fleet']);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FleetShip::class,
            'fleet' => null,
        ]);
    }
}
