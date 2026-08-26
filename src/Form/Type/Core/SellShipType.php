<?php

namespace EtoA\Form\Type\Core;

use EtoA\Entity\ShipListItem;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellShipType extends AbstractType
{


    public function __construct(
        private readonly UserRepository $userRepository
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tradeableShips = $options['tradeable_ships'];
        $marketUserReservationActive = $options['market_user_reservation_active'];
        $hasAlliance = $options['has_alliance'];
        $allianceMarketLevel = $options['alliance_market_level'];
        $cdEnabled = $options['cd_enabled'];
        $nickLength = $options['nick_length'];

        $builder
            ->add('ship_last_update', HiddenType::class, [
                'data' => '0',
                'mapped' => false
            ])
            ->add('ship', ChoiceType::class, [
                'label' => false,
                'choices' => $tradeableShips,
                'attr' => [
                    'onchange' => 'calcMarketShipPrice(1, 0);',
                ],
                'choice_value' => 'ship.id',
                'choice_label' => function (?ShipListItem $item): string {
                    return $item->getShip()->getName() . ' (' . $item->getCount() . ')';
                },
                'mapped' => false
            ])
            ->add('count', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 5,
                    'maxlength' => 7,
                    'onkeyup' => 'calcMarketShipPrice(1, 0);',
                ],
                'data' => '0',
            ])
            ->add('ship_percent', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 5,
                    'maxlength' => 7,
                    'onkeyup' => 'calcMarketShipPrice(1, 0);',
                ],
                'data' => '100',
                'mapped' => false
            ])
            ->add('ship_sell_metal', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'disabled' => 'disabled',
                ],
                'data' => '0',
                'disabled' => true,
                'mapped' => false
            ])
            ->add('ship_sell_crystal', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'disabled' => 'disabled',
                ],
                'data' => '0',
                'disabled' => true,
                'mapped' => false
            ])
            ->add('ship_sell_plastic', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'disabled' => 'disabled',
                ],
                'data' => '0',
                'disabled' => true,
                'mapped' => false
            ])
            ->add('ship_sell_fuel', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'disabled' => 'disabled',
                ],
                'data' => '0',
                'disabled' => true,
                'mapped' => false
            ])
            ->add('ship_sell_food', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'disabled' => 'disabled'
                ],
                'data' => '0',
                'disabled' => true,
                'mapped' => false
            ])
            ->add('costs0', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'onkeyup' => 'calcMarketShipPrice(0, 0);',
                ],
                'data' => 0,
            ])
            ->add('costs1', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'onkeyup' => 'calcMarketShipPrice(0, 0);',
                ],
                'data' => '0',
            ])
            ->add('costs2', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'onkeyup' => 'calcMarketShipPrice(0, 0);',
                ],
                'data' => '0',
            ])
            ->add('costs3', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'onkeyup' => 'calcMarketShipPrice(0, 0);',
                ],
                'data' => '0',
            ])
            ->add('costs4', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'onkeyup' => 'calcMarketShipPrice(0, 0);',
                ],
                'data' => '0',
            ])
            ->add('text', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'size' => 55,
                    'maxlength' => 60,
                    'style' => 'width:98%',
                    'onkeyup' => "calcMarketShipPrice('0');",
                ],
                'empty_data' => '',
            ])
            ->add('ship_offer_reservation', ChoiceType::class, [
                'label' => false,
                'expanded' => true,
                'choices' => $this->getReservationChoices($marketUserReservationActive, $hasAlliance, $allianceMarketLevel, $cdEnabled),
                'data' => '0',
                'attr' => [
                    'class' => 'ship-offer-reservation',
                ],
                'mapped' => false
            ])
            ->add('forUser', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'maxlength' => $nickLength,
                    'size' => 25,
                    'autocomplete' => 'off',
                    'data-model' => 'debounce(500)|value'
                ],
                'mapped' => false
            ])
            ->add('submit', ButtonType::class, [
                'attr' => [
                    'class' => 'button',
                    'style' => 'color:#f00',
                    'disabled'=>'disabled',
                    'onclick'=>"calcMarketShipPrice(0, 1);checkUpdate('ship_selector', 'ship_last_update')"
                ],
                'label' => 'Angebot aufgeben'
            ])
            ->add('checker', SingleSubmitType::class);
        $builder->get('forUser')
            ->addModelTransformer(new CallbackTransformer(
                function () {},
                function ($userNick) {
                    // transform the string back to an array
                    return $this->userRepository->findOneBy(['nick'=>$userNick]);
                }
            ))
        ;
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $data['costs0'] = StringUtils::parseFormattedNumber($data['costs0']);
            $data['costs1'] = StringUtils::parseFormattedNumber($data['costs1']);
            $data['costs2'] = StringUtils::parseFormattedNumber($data['costs2']);
            $data['costs3'] = StringUtils::parseFormattedNumber($data['costs3']);
            $data['costs4'] = StringUtils::parseFormattedNumber($data['costs4']);
            $data['count'] = StringUtils::parseFormattedNumber($data['count']);

            $event->setData($data);
        });
    }

    private function getReservationChoices(bool $marketUserReservationActive, bool $hasAlliance, ?int $allianceMarketLevel, bool $cdEnabled): array
    {
        $choices = [
            'Öffentliches Angebot' => '0',
        ];

        if ($marketUserReservationActive) {
            $choices['Für eine bestimmte Person'] = '1';
        }

        if ($hasAlliance && $allianceMarketLevel >= 1 && !$cdEnabled) {
            $choices['Für Allianzmitglieder'] = '2';
        }

        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'tradeable_ships' => [],
            'market_user_reservation_active' => false,
            'has_alliance' => false,
            'alliance_market_level' => 0,
            'cd_enabled' => false,
            'nick_length' => 20,
        ]);

        $resolver->setRequired([
            'tradeable_ships',
            'market_user_reservation_active',
            'has_alliance',
            'alliance_market_level',
            'cd_enabled',
            'nick_length',
        ]);
    }
}
