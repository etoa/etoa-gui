<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\AdminType;
use EtoA\Form\Type\Core\LogDateTimeType;
use EtoA\Form\Type\Core\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LogDebrisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserType::class, [
                'label' => 'User:',
                'placeholder' => '(Alle)',
            ])
            ->add('admin', AdminType::class, [
                'label' => 'Admin:',
                'placeholder' => '(Alle)',
            ])
            ->add('date', LogDateTimeType::class);
    }
}
