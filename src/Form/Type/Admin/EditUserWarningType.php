<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Entity\UserWarning;
use EtoA\Form\Type\Core\AdminType;
use EtoA\Form\Type\Core\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserWarningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserType::class, [
                'disabled' => 'disabled',
                'label' => 'Spieler',
            ])
            ->add('admin', AdminType::class, [
                'label' => 'Admin',
                'placeholder' => false,
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Verwarnungstext',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Speichern',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserWarning::class,
        ]);
    }
}
