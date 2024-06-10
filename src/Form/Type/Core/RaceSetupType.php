<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Form\Type\Core\EntityType;
use EtoA\Form\Type\Core\PositiveIntegerType;
use EtoA\Form\Type\Core\ShipType;
use EtoA\Universe\Entity\EntityLabelSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use EtoA\Race\RaceDataRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class RaceSetupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shipId', RaceType::class, [
                'placeholder' => 'Bitte wählen...',
                'label' => false,
                'required' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Weiter',
            ])
            ->add('random', ButtonType::class, [
                'label' => 'Zufällige Rasse auswählen',
                'attr' => ['onclick' => 'rdm()'],
            ])
            ->add('checker', HiddenType::class, [
                'data' => $options['data'][0],
            ]);
    }
}