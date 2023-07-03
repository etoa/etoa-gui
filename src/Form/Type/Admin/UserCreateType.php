<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\RaceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('nick', TextType::class)
            ->add('password', PasswordType::class, [
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ])
            ->add('raceId', RaceType::class, [
                'placeholder' => 'Keine',
                'label' => 'Rasse',
            ])
            ->add('ghost', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
            ])
            ->add('submit', SubmitType::class);
    }
}
