<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\AdminType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EditUserWarningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userNick', TextType::class, [
                'disabled' => 'disabled',
                'label' => 'Spieler',
            ])
            ->add('adminId', AdminType::class, [
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
}
