<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Entity\UserProperties;
use EtoA\Ship\ShipDataRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPropertiesType extends AbstractType
{
    public function __construct(
        private readonly ShipDataRepository $shipDataRepository
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        for ($x = 450; $x <= 700; $x += 50) {
            $planetWidth[$x] = $x;
        }

        $builder
            ->add('spyShip', ChoiceType::class, [
                    'placeholder' => '(keines)',
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'choices' => $this->shipDataRepository->getShipsWithAction('spy'),
                    'required' => false
                ]
            )
            ->add('analyzeShip', ChoiceType::class, [
                'placeholder' => '(keines)',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choices' => $this->shipDataRepository->getShipsWithAction('analyze'),
                'required' => false
            ])
            ->add('exploreShip', ChoiceType::class, [
                'placeholder' => '(keines)',
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choices' => $this->shipDataRepository->getShipsWithAction('explore'),
                'required' => false
            ])
            ->add('msgSignature', TextareaType::class, [
                'attr' => ['cols' => 50, 'rows' => 4],
                'required' => false
            ])
            ->add('msgPreview', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('msgCreationPreview', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('msgBlink', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('msgCopy', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('fleetRtnMsg', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('planetCircleWidth', ChoiceType::class, [
                'choices' => $planetWidth,
            ])
            ->add('cssStyle', DesignType::class)
            ->add('itemShow', ChoiceType::class, [
                'choices' => [
                    ' Volle Ansicht ' => 'full',
                    ' Einfache Ansicht ' => 'small',
                ],
                'expanded' => true,
            ])
            ->add('imageFilter', ChoiceType::class, [
                'choices' => [
                    ' An ' => 1,
                    ' Aus ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('havenShipsButtons', ChoiceType::class, [
                'choices' => [
                    ' An ' => 1,
                    ' Aus ' => 0,
                ],
                'expanded' => true,
            ])
            ->add('showAdds', ChoiceType::class, [
                'choices' => [
                    ' Aktiviert ' => 1,
                    ' Deaktiviert ' => 0,
                ],
                'expanded' => true,
            ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserProperties::class,
        ]);
    }
}