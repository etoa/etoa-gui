<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\AdminType;
use EtoA\Form\Type\Core\TimestampType;
use EtoA\Form\Type\Core\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LogDebrisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserType::class, [
                'placeholder' => '(Alle)',
            ])
            ->add('admin', AdminType::class, [
                'placeholder' => '(Alle)',
            ])
            ->add('date', TimestampType::class);
    }
}
