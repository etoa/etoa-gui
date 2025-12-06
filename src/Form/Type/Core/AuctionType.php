<?php

namespace EtoA\Form\Type\Core;

use EtoA\Support\StringUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuctionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $planet = $options['planet'];
        $auctionMinDuration = $options['auction_min_duration'];

        $builder
            ->add('auction_last_update', HiddenType::class, [
                'data' => '0',
                'mapped' => false
            ])
            ->add('auction_time_min', HiddenType::class, [
                'data' => time() + ($auctionMinDuration * 24 * 3600),
                'mapped' => false
            ])
            ->add('auction_metal', HiddenType::class, [
                'label' => false,
                'data' => $planet->getResMetal(),
                'mapped' => false
            ])
            ->add('auction_crystal', HiddenType::class, [
                'label' => false,
                'data' => $planet->getResCrystal(),
                'mapped' => false
            ])
            ->add('auction_plastic', HiddenType::class, [
                'label' => false,
                'data' => $planet->getResPlastic(),
                'mapped' => false
            ])
            ->add('auction_fuel', HiddenType::class, [
                'label' => false,
                'data' => $planet->getResFuel(),
                'mapped' => false
            ])
            ->add('auction_food', HiddenType::class, [
                'label' => false,
                'data' => $planet->getResFood(),
                'mapped' => false
            ])
            ->add('sell0', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 9,
                    'maxlength' => 15,
                    'onkeyup'=>"FormatNumber(this.id,this.value,".$planet->getResMetal()." ,'','');checkMarketAuctionFormular('0');"
                ],
                'data' => '0'
            ])
            ->add('sell1', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 9,
                    'maxlength' => 15,
                    'onkeyup'=>"FormatNumber(this.id,this.value,".$planet->getResCrystal()." ,'','');checkMarketAuctionFormular('0');"
                ],
                'data' => '0'
            ])
            ->add('sell2', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 9,
                    'maxlength' => 15,
                    'onkeyup'=>"FormatNumber(this.id,this.value,".$planet->getResPlastic()." ,'','');checkMarketAuctionFormular('0');"
                ],
                'data' => '0'
            ])
            ->add('sell3', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 9,
                    'maxlength' => 15,
                    'onkeyup'=>"FormatNumber(this.id,this.value,".$planet->getResFuel()." ,'','');checkMarketAuctionFormular('0');"
                ],
                'data' => '0'
            ])
            ->add('sell4', TextType::class, [
                'label' => false,
                'attr' => [
                    'size' => 9,
                    'maxlength' => 15,
                    'onkeyup'=>"FormatNumber(this.id,this.value,".$planet->getResFood()." ,'','');checkMarketAuctionFormular('0');"
                ],
                'data' => '0'
            ])
            ->add('currency0',CheckboxType::class, [
                'label' => false,
                'attr' => [
                    'onkeyup' => 'checkMarketAuctionFormular(0);',
                ]
            ])
            ->add('currency1', CheckboxType::class, [
                'label' => false,
                'attr' => [
                    'onclick' => 'checkMarketAuctionFormular(0);',
                ]
            ])
            ->add('currency2', CheckboxType::class, [
                'label' => false,
                'attr' => [
                    'onclick' => 'checkMarketAuctionFormular(0);',
                ]
            ])
            ->add('currency3', CheckboxType::class, [
                'label' => false,
                'attr' => [
                    'onclick' => 'checkMarketAuctionFormular(0);',
                ]
            ])
            ->add('currency4', CheckboxType::class, [
                'label' => false,
                'attr' => [
                    'onclick' => 'checkMarketAuctionFormular(0);',
                ]
            ])
            ->add('auction_time_days', ChoiceType::class, [
                'label' => false,
                'choices' => range(0, 10),
                'mapped' => false,
                'attr' => [
                    'onchange'=>"checkMarketAuctionFormular(0);"
                ]
            ])
            ->add('auction_time_hours', ChoiceType::class, [
                'label' => false,
                'choices' => range(0, 24),
                'mapped' => false,
                'attr' => [
                    'onchange'=>"checkMarketAuctionFormular(0);"
                ]
            ])
            ->add('text', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'size' => 55,
                    'maxlength' => 60,
                    'style' => 'width:98%',
                    'onkeyup' => "checkMarketAuctionFormular('0');",
                ],
                'empty_data' => '',
            ])
            ->add('submit', ButtonType::class, [
                'attr' => [
                    'class' => 'button',
                    'style' => 'color:#f00',
                    'disabled'=>'disabled',
                    'onclick'=>"checkMarketAuctionFormular(1);checkUpdate('auction_selector', 'auction_last_update')"
                ],
                'label' => 'Angebot aufgeben'
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            $data['sell0'] = StringUtils::parseFormattedNumber($data['sell0']);
            $data['sell1'] = StringUtils::parseFormattedNumber($data['sell1']);
            $data['sell2'] = StringUtils::parseFormattedNumber($data['sell2']);
            $data['sell3'] = StringUtils::parseFormattedNumber($data['sell3']);
            $data['sell4'] = StringUtils::parseFormattedNumber($data['sell4']);

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'planet' => null,
            'auction_min_duration' => null,
        ]);

        $resolver->setRequired([
            'planet',
            'auction_min_duration',
        ]);
    }
}
