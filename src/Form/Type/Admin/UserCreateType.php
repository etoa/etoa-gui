<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Form\Type\Core\RaceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserCreateType extends AbstractType
{
    public function __construct(
        private readonly ConfigurationService $config
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => true,
                ],
            ])
            ->add('email', EmailType::class)
            ->add('nick', TextType::class)
            ->add('password', PasswordType::class, [
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'hash_property_path' => 'password',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => $this->config->getInt('password_minlength')]),
                ],
            ])
            ->add('race', RaceType::class, [
                'placeholder' => 'Keine',
                'label' => 'Rasse',
                'constraints' => [],
            ])
            ->add('ghost', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Erstellen',
            ]);
    }
}
