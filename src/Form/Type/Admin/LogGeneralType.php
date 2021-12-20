<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\LogFacilityType;
use EtoA\Form\Type\Core\LogSeverityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LogGeneralType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('severity', LogSeverityType::class, [
                'label' => 'Ab Schweregrad:',
            ])
            ->add('facility', LogFacilityType::class, [
                'label' => 'Kategorie:',
            ])
            ->add('query', TextType::class, [
                'required' => false,
                'label' => 'Suchtext:',
            ]);
    }
}
