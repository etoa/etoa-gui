<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class RaceSetupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('race', RaceType::class, [
                'placeholder' => 'Bitte wählen...',
                'label' => false,
                'required' => true,
            ])
            ->add('saveRace', SubmitType::class, [
                'label' => 'Weiter',
            ])
            ->add('random', ButtonType::class, [
                'label' => 'Zufällige Rasse auswählen',
                'attr' => ['onclick' => 'rdm()'],
            ])
            ->add('checker', HiddenType::class);
    }
}