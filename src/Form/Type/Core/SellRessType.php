<?php

namespace EtoA\Form\Type\Core;

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

class SellRessType extends AbstractType
{
    public function __construct(
        private readonly UserRepository $userRepository
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $marketUserReservationActive = $options['market_user_reservation_active'];
        $hasAlliance = $options['has_alliance'];
        $allianceMarketLevel = $options['alliance_market_level'];
        $cdEnabled = $options['cd_enabled'];
        $nickLength = $options['nick_length'];
        $planet = $options['planet'];

        $builder
            ->add('ress_last_update', HiddenType::class, [
                'data' => '0',
                'mapped' => false
            ])
            ->add('ress_metal', HiddenType::class, [
                'label' => false,
                'data' => $planet->getResMetal(),
                'mapped' => false
            ])
            ->add('ress_crystal', HiddenType::class, [
                'label' => false,
                'data' => $planet->getResCrystal(),
                'mapped' => false
            ])
            ->add('ress_plastic', HiddenType::class, [
                'label' => false,
                'data' => $planet->getResPlastic(),
                'mapped' => false
            ])
            ->add('ress_fuel', HiddenType::class, [
                'label' => false,
                'data' => $planet->getResFuel(),
                'mapped' => false
            ])
            ->add('ress_food', HiddenType::class, [
                'label' => false,
                'data' => $planet->getResFood(),
                'mapped' => false
            ])
            ->add('sell0', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 9,
                    'maxlength' => 15,
                    'onkeyup'=>"FormatNumber(this.id,this.value,".$planet->getResMetal()." ,'','');calcMarketRessPrice('0');"
                ],
                'data' => '0'
            ])
            ->add('sell1', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 9,
                    'maxlength' => 15,
                    'onkeyup'=>"FormatNumber(this.id,this.value,".$planet->getResCrystal()." ,'','');calcMarketRessPrice('0');"
                ],
                'data' => '0'
            ])
            ->add('sell2', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 9,
                    'maxlength' => 15,
                    'onkeyup'=>"FormatNumber(this.id,this.value,".$planet->getResPlastic()." ,'','');calcMarketRessPrice('0');"
                ],
                'data' => '0'
            ])
            ->add('sell3', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 9,
                    'maxlength' => 15,
                    'onkeyup'=>"FormatNumber(this.id,this.value,".$planet->getResFuel()." ,'','');calcMarketRessPrice('0');"
                ],
                'data' => '0'
            ])
            ->add('sell4', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 9,
                    'maxlength' => 15,
                    'onkeyup'=>"FormatNumber(this.id,this.value,".$planet->getResFood()." ,'','');calcMarketRessPrice('0');"
                ],
                'data' => '0'
            ])
            ->add('buy0', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'onkeyup' => 'calcMarketRessPrice(0, 0);',
                ],
                'data' => '0',
            ])
            ->add('buy1', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'onkeyup' => 'calcMarketRessPrice(0, 0);',
                ],
                'data' => '0',
            ])
            ->add('buy2', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'onkeyup' => 'calcMarketRessPrice(0, 0);',
                ],
                'data' => '0',
            ])
            ->add('buy3', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'onkeyup' => 'calcMarketRessPrice(0, 0);',
                ],
                'data' => '0',
            ])
            ->add('buy4', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 7,
                    'maxlength' => 15,
                    'onkeyup' => 'calcMarketRessPrice(0, 0);',
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
                    'onkeyup' => "calcMarketRessPrice('0');",
                ],
                'empty_data' => '',
            ])
            ->add('ressource_offer_reservation', ChoiceType::class, [
                'label' => false,
                'expanded' => true,
                'choices' => $this->getReservationChoices($marketUserReservationActive, $hasAlliance, $allianceMarketLevel, $cdEnabled),
                'data' => '0',
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
                    'onclick'=>"calcMarketRessPrice(1);checkUpdate('ress_selector', 'ress_last_update')"
                ],
                'label' => 'Angebot aufgeben'
            ]);
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
            $data['buy0'] = array_key_exists('buy0',$data)?StringUtils::parseFormattedNumber($data['buy0']):0;
            $data['buy1'] = array_key_exists('buy1',$data)?StringUtils::parseFormattedNumber($data['buy1']):0;
            $data['buy2'] = array_key_exists('buy2',$data)?StringUtils::parseFormattedNumber($data['buy2']):0;
            $data['buy3'] = array_key_exists('buy3',$data)?StringUtils::parseFormattedNumber($data['buy3']):0;
            $data['buy4'] = array_key_exists('buy4',$data)?StringUtils::parseFormattedNumber($data['buy4']):0;

            $data['sell0'] = StringUtils::parseFormattedNumber($data['sell0']);
            $data['sell1'] = StringUtils::parseFormattedNumber($data['sell1']);
            $data['sell2'] = StringUtils::parseFormattedNumber($data['sell2']);
            $data['sell3'] = StringUtils::parseFormattedNumber($data['sell3']);
            $data['sell4'] = StringUtils::parseFormattedNumber($data['sell4']);

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
            'market_user_reservation_active' => false,
            'has_alliance' => false,
            'alliance_market_level' => 0,
            'cd_enabled' => false,
            'nick_length' => 20,
            'planet' => null,
        ]);

        $resolver->setRequired([
            'market_user_reservation_active',
            'has_alliance',
            'alliance_market_level',
            'cd_enabled',
            'nick_length',
            'planet',
        ]);
    }
}
