<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Entity\Ship;
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

        if ($ship->getSpecialBonusWeapon() > 0) {
            $builder
                ->add('specialShipBonusWeapon', IntegerType::class, [
                    'label' => 'Waffen',
                    'attr' => ['size' => 4],
                ]);
        }

        if ($ship->getSpecialBonusStructure() > 0) {
            $builder
                ->add('specialShipBonusStructure', IntegerType::class, [
                    'label' => 'Struktur',
                ]);
        }

        if ($ship->getSpecialBonusShield() > 0) {
            $builder
                ->add('specialShipBonusShield', IntegerType::class, [
                    'label' => 'Schild',
                ]);
        }

        if ($ship->getSpecialBonusHeal() > 0) {
            $builder
                ->add('specialShipBonusHeal', IntegerType::class, [
                    'label' => 'Heal',
                ]);
        }

        if ($ship->getSpecialBonusCapacity() > 0) {
            $builder
                ->add('specialShipBonusCapacity', IntegerType::class, [
                    'label' => 'Kapazität',
                ]);
        }

        if ($ship->getSpecialBonusSpeed() > 0) {
            $builder
                ->add('specialShipBonusSpeed', IntegerType::class, [
                    'label' => 'Speed',
                ]);
        }

        if ($ship->getSpecialBonusPilots() > 0) {
            $builder
                ->add('specialShipBonusPilots', IntegerType::class, [
                    'label' => 'Piloten',
                ]);
        }

        if ($ship->getSpecialBonusTarn() > 0) {
            $builder
                ->add('specialShipBonusTarn', IntegerType::class, [
                    'label' => 'Tarnung',
                ]);
        }

        if ($ship->getSpecialBonusAntrax() > 0) {
            $builder
                ->add('specialShipBonusAnthrax', IntegerType::class, [
                    'label' => 'Giftgas',
                ]);
        }

        if ($ship->getSpecialBonusForsteal() > 0) {
            $builder
                ->add('specialShipBonusForSteal', IntegerType::class, [
                    'label' => 'Techklau',
                ]);
        }

        if ($ship->getSpecialBonusBuildDestroy() > 0) {
            $builder
                ->add('specialShipBonusBuildDestroy', IntegerType::class, [
                    'label' => 'Bombardier',
                ]);
        }

        if ($ship->getSpecialBonusAntraxFood() > 0) {
            $builder
                ->add('specialShipBonusAnthraxFood', IntegerType::class, [
                    'label' => 'Antrax',
                ]);
        }

        if ($ship->getSpecialBonusDeactivate() > 0) {
            $builder
                ->add('specialShipBonusDeactivate', IntegerType::class, [
                    'label' => 'Deaktivieren',
                ]);
        }

        if ($ship->getSpecialBonusReadiness() > 0) {
            $builder
                ->add('specialShipBonusReadiness', IntegerType::class, [
                    'label' => 'Bereitschaft',
                ]);
        }
    }
}
