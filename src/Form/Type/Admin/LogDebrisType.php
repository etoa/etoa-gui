<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\AdminType;
use EtoA\Form\Type\Core\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
            ->add('date', DateTimeType::class, [
                'label' => 'Zeit:',
                'widget' => 'single_text',
                'with_seconds' => true,
                'input' => 'string',
                'input_format' => 'Y-m-d\TH:i:s',
            ]);
    }
}
