<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AllianceNewsCleanupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timespan', ChoiceType::class, [
                'label' => 'News löschen die älter sind als',
                'choices' => [
                    "1 Woche" => 604800,
                    "2 Wochen" => 1209600,
                    "1 Monat" => 2592000,
                    "2 Monate" => 5184000,
                    "3 Monate" => 7776000,
                    "6 Monate" => 15552000,
                ],
                'data' => 2592000,
            ])
            ->add('cleanup', SubmitType::class, [
                'label' => 'Ausführen',
            ]);
    }
}
