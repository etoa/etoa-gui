<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class PositiveIntegerType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'attr' => ['min' => 1],
                'constraints' => [new Range(min: 1)],
            ]);
    }

    public function getParent(): string
    {
        return IntegerType::class;
    }
}
