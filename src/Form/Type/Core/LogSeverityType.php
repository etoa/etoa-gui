<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Log\LogSeverity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogSeverityType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => true,
            'choices' => array_flip(LogSeverity::SEVERITIES),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
