<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class DeleteAdminSessionLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timespan', ChoiceType::class, [
                'label' => 'Einträge löschen die älter sind als',
                'choices' => [
                    "15 Tage" => 1296000,
                    "30 Tage" => 2592000,
                    "45 Tage" => 3888000,
                    "60 Tage" => 5184000,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ausführen',
            ]);
    }
}
