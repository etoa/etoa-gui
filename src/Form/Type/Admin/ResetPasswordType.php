<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Benutzername',
                'required' => true,
                'attr' => [
                    'autofocus' => true,
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-Mail',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Neues Passwort anfordern',
            ]);
    }
}
