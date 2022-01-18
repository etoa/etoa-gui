<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Ship\Ship;
use EtoA\Ship\ShipDataRepository;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class EditSpecialShipListType extends EditShipListType
{
    public function __construct(
        private ShipDataRepository $shipDataRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        /** @var Ship $ship */
        $ship = $this->shipDataRepository->getShip($options['data']->shipId, false);

        $builder
            ->add('specialShipExp', IntegerType::class, [
                'label' => 'Exp',
                'attr' => ['size' => 6],
            ]);

        if ($ship->specialBonusWeapon > 0) {
            $builder
                ->add('specialShipBonusWeapon', IntegerType::class, [
                    'label' => 'Waffen',
                    'attr' => ['size' => 4],
                ]);
        }

        if ($ship->specialBonusStructure > 0) {
            $builder
                ->add('specialShipBonusStructure', IntegerType::class, [
                    'label' => 'Struktur',
                ]);
        }

        if ($ship->specialBonusShield > 0) {
            $builder
                ->add('specialShipBonusShield', IntegerType::class, [
                    'label' => 'Schild',
                ]);
        }

        if ($ship->specialBonusHeal > 0) {
            $builder
                ->add('specialShipBonusHeal', IntegerType::class, [
                    'label' => 'Heal',
                ]);
        }

        if ($ship->specialBonusCapacity > 0) {
            $builder
                ->add('specialShipBonusCapacity', IntegerType::class, [
                    'label' => 'Kapazität',
                ]);
        }

        if ($ship->specialBonusSpeed > 0) {
            $builder
                ->add('specialShipBonusSpeed', IntegerType::class, [
                    'label' => 'Speed',
                ]);
        }

        if ($ship->specialBonusPilots > 0) {
            $builder
                ->add('specialShipBonusPilots', IntegerType::class, [
                    'label' => 'Piloten',
                ]);
        }

        if ($ship->specialBonusTarn > 0) {
            $builder
                ->add('specialShipBonusTarn', IntegerType::class, [
                    'label' => 'Tarnung',
                ]);
        }

        if ($ship->specialBonusAntrax > 0) {
            $builder
                ->add('specialShipBonusAnthrax', IntegerType::class, [
                    'label' => 'Giftgas',
                ]);
        }

        if ($ship->specialBonusForsteal > 0) {
            $builder
                ->add('specialShipBonusForSteal', IntegerType::class, [
                    'label' => 'Techklau',
                ]);
        }

        if ($ship->specialBonusBuildDestroy > 0) {
            $builder
                ->add('specialShipBonusBuildDestroy', IntegerType::class, [
                    'label' => 'Bombardier',
                ]);
        }

        if ($ship->specialBonusAntraxFood > 0) {
            $builder
                ->add('specialShipBonusAnthraxFood', IntegerType::class, [
                    'label' => 'Antrax',
                ]);
        }

        if ($ship->specialBonusDeactivate > 0) {
            $builder
                ->add('specialShipBonusDeactivate', IntegerType::class, [
                    'label' => 'Deaktivieren',
                ]);
        }

        if ($ship->specialBonusReadiness > 0) {
            $builder
                ->add('specialShipBonusReadiness', IntegerType::class, [
                    'label' => 'Bereitschaft',
                ]);
        }
    }
}
