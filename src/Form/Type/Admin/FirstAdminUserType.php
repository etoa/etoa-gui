<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FirstAdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Loginname',
                'required' => true,
                'attr' => [
                    'autofocus' => true,
                ],
            ])
            ->add('passwordString', PasswordType::class, [
                'label' => 'Passwort',
                'required' => true,
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-Mail',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Admin-User erstellen',
            ]);
    }
}
