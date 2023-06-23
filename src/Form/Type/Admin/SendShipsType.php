<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\EntityType;
use EtoA\Form\Type\Core\ShipType;
use EtoA\Form\Type\Core\TimestampType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SendShipsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('launchTime', TimestampType::class, [
                'label' => 'Startzeit',
            ])
            ->add('landTime', TimestampType::class, [
                'label' => 'Landezeit',
            ])
            ->add('entityFrom', EntityType::class, [
                'required' => true,
                'placeholder' => false,
                'label' => 'Startzelle',
            ])
            ->add('entityTo', TextType::class, [
                'label' => 'Ziel',
                'mapped' => false,
                'data' => 'Hauptplanet jedes Spielers',
                'disabled' => 'disabled',
            ])
            ->add('count', IntegerType::class, [
                'label' => 'Anzahl',
            ])
            ->add('shipId', ShipType::class, [
                'label' => 'Schiff',
                'required' => true,
                'placeholder' => false,
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Erstellen',
            ])
        ;
    }
}
