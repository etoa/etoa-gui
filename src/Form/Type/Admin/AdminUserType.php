<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Admin\AdminRoleManager;
use EtoA\Admin\AdminUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminUserType extends AbstractType
{
    public function __construct(
        private readonly AdminRoleManager $adminRoleManager
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var AdminUser $admin */
        $admin = $options['data'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Realer Name',
                'attr' => [
                    'autofocus' => $admin?->id === null,
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-Mail',
            ])
            ->add('nick', TextType::class, [
                'label' => 'Nickname',
            ]);

        if ($admin->id) {
            $builder
                ->add('passwordString', PasswordType::class, [
                    'required' => false,
                    'label' => 'Neues Passwort',
                ]);
        }

        if ($admin->tfaSecret !== '') {
            $builder
                ->add('tfa_remove', CheckboxType::class, [
                    'required' => false,
                    'label' => 'Zwei-Faktor-Authentifizierung deaktivieren',
                    'mapped' => false,
                ]);
        }

        $builder
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'choices' => array_flip($this->adminRoleManager->getRoles()),
                'label' => 'Rollen',
            ])
            ->add('isContact', ChoiceType::class, [
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
                'label' => 'Kontaktierbar',
            ])
            ->add('locked', ChoiceType::class, [
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Ja' => true,
                    'Nein' => false,
                ],
                'label' => 'Gesperrt',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Speichern',
            ]);
    }
}
