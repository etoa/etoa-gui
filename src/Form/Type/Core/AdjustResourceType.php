<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class AdjustResourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', IntegerType::class, [
                'label' => false,
            ])
            ->add('addRemove', IntegerType::class, [
                'label' => '+/-',
                'data' => 0,
            ])
            ->addModelTransformer(new CallbackTransformer(
                function (int|float $value): array {
                    return ['value' => $value, 'addRemove' => 0];
                },
                fn (array $value) => $value['value'] + $value['addRemove']
            ));
    }
}
