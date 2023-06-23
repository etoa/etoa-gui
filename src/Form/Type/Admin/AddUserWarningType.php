<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class AddUserWarningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userId', UserType::class, [
                'required' => true,
                'placeholder' => false,
                'label' => 'Spieler',
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Verwarnungstext',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Neue Verwarnung erteilen',
            ]);
    }
}
