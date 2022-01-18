<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\ShipType;
use EtoA\Ship\ShipSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ShipXpCalculatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ship', ShipType::class, [
                'search' => ShipSearch::create()->special(true),
                'attr' => ['onchange' => 'this.form.submit()'],
                'placeholder' => false,
            ]);
    }
}
