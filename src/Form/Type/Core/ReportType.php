<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Entity\Report;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('deleted', CheckboxType::class, [
                'required'=>false,
                'label'=>false,
                'attr' => [
                    'title' => 'Report zum Löschen markieren'
                ]
            ])
            ->add('deleteReport', SubmitType::class, [
                'label' => 'Löschen'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
        ]);
    }
}