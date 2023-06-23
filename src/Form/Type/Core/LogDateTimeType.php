<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogDateTimeType extends AbstractType
{
    public const FORMAT = 'Y-m-d\TH:i:s';

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'label' => 'Zeit:',
            'widget' => 'single_text',
            'with_seconds' => true,
            'input' => 'string',
            'input_format' => self::FORMAT,
        ]);
    }

    public function getParent(): string
    {
        return DateTimeType::class;
    }
}
