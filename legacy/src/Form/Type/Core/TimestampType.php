<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimestampType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'input' => 'timestamp',
            'with_seconds' => true,
            'widget' => 'single_text',
        ]);
    }

    public function getParent(): string
    {
        return DateTimeType::class;
    }
}
