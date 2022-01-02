<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Message\ReportTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportCategoryType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => true,
            'placeholder' => '(Alle)',
            'choices' => array_flip(ReportTypes::TYPES),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
