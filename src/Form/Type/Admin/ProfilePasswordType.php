<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Core\Configuration\ConfigurationService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class ProfilePasswordType extends AbstractType
{
    public function __construct(
        private ConfigurationService $config
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'required' => true,
                'label' => 'Altes Passwort',
            ])
            ->add('new_password', RepeatedType::class, [
                'required' => true,
                'type' => PasswordType::class,
                'constraints' => [
                    new Length(null, $this->config->getInt('password_minlength'), null, null, null, null, 'Das Passwort ist zu kurz! Es muss mindestens ' . $this->config->getInt('password_minlength') . ' Zeichen lang sein!'),
                ],
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'first_options' => ['label' => 'Neues Passwort', 'attr' => ['minlength' => $this->config->getInt('password_minlength')]],
                'second_options' => ['label' => 'Neues Passwort (wiederholen)', 'attr' => ['minlength' => $this->config->getInt('password_minlength')]],
                'invalid_message' => 'Die Kennwortwiederholung stimmt nicht oder das alte und das neue Passwort sind gleich!',
            ])
            ->add('submit', SubmitType::class, []);
    }
}
